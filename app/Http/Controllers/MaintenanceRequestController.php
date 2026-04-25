<?php

namespace App\Http\Controllers;

use App\Models\MaintenanceRequest;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MaintenanceRequestController extends Controller
{
    public function index(Request $request)
    {

        if (!auth()->user()->hasPermission('view_maintenance_requests')) {
            abort(403, 'غير مصرح لك بعرض البلاغات');
        }

        $user = Auth::user();

        $query = MaintenanceRequest::with(['department', 'station', 'user'])
            ->latest();

        // فلترة حسب الشركة
        if ($user && $user->company_id) {
            $query->where('company_id', $user->company_id);
        }

        // فلترة حسب المحطة إذا كان المستخدم مرتبطًا بمحطة
        if ($user && $user->station_id) {
            $query->where('station_id', $user->station_id);
        }

        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        $maintenanceRequests = $query->paginate(15)->withQueryString();

        $departments = Department::orderBy('name')->get();

        $statuses = [
            'new' => 'جديد',
            'forwarded_to_maintenance' => 'محول إلى الصيانة',
            'in_progress' => 'قيد التنفيذ',
            'completed' => 'مكتمل',
            'cancelled' => 'ملغي',
            'closed' => 'مغلق',
        ];

        $priorities = [
            'low' => 'منخفض',
            'medium' => 'متوسط',
            'high' => 'عالي',
            'urgent' => 'عاجل',
        ];

        return view('maintenance-requests.index', compact(
            'maintenanceRequests',
            'departments',
            'statuses',
            'priorities'
        ));
    }

    public function create()
    {

        if (!auth()->user()->hasPermission('create_maintenance_requests')) {
            abort(403, 'غير مصرح لك بإضافة بلاغ');
        }

        $departments = Department::orderBy('name')->get();

        $priorities = [
            'low' => 'منخفض',
            'medium' => 'متوسط',
            'high' => 'عالي',
            'urgent' => 'عاجل',
        ];

        return view('maintenance-requests.create', compact('departments', 'priorities'));
    }

    public function store(Request $request)
    {

        if (!auth()->user()->hasPermission('create_maintenance_requests')) {
            abort(403, 'غير مصرح لك بإضافة بلاغ');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'department_id' => 'nullable|exists:departments,id',
            'priority' => 'required|in:low,medium,high,urgent',
            'notes' => 'nullable|string',
        ]);

        if (!Auth::check()) {
            return redirect()->back()->with('error', 'يجب تسجيل الدخول أولاً');
        }

        $user = Auth::user();

        if (!$user->station_id) {
            return redirect()->back()->with('error', 'المستخدم الحالي غير مرتبط بمحطة');
        }

        if (!$user->company_id) {
            return redirect()->back()->with('error', 'المستخدم الحالي غير مرتبط بشركة');
        }

        $maintenanceRequest = MaintenanceRequest::create([
            'report_number' => $this->generateReportNumber(),
            'company_id' => $user->company_id,
            'station_id' => $user->station_id,
            'user_id' => $user->id,
            'department_id' => $request->department_id,
            'title' => $request->title,
            'description' => $request->description,
            'priority' => $request->priority,
            'status' => 'new',
            'reported_at' => now(),
            'closed_at' => null,
            'notes' => $request->notes,
        ]);

        $maintenanceRequest->update([
            'report_number' => 'MR-' . str_pad($maintenanceRequest->id, 6, '0', STR_PAD_LEFT),
        ]);

        return redirect()
            ->route('maintenance-requests.index')
            ->with('success', 'تم إنشاء البلاغ بنجاح');
    }

    public function show($id)
    {
        $maintenanceRequest = MaintenanceRequest::with([
            'station.company',
            'user',
            'department',
            'logs.user',
            'jobOrder',
        ])->findOrFail($id);

        $statuses = [
            'new' => 'جديد',
            'under_review' => 'تحت المراجعة',
            'forwarded_to_maintenance' => 'محول إلى الصيانة',
            'in_progress' => 'قيد التنفيذ',
            'completed' => 'مكتمل',
            'closed' => 'مغلق',
            'cancelled' => 'ملغي',
        ];

        $priorities = [
            'low' => 'منخفضة',
            'medium' => 'متوسطة',
            'high' => 'عالية',
            'urgent' => 'عاجلة',
        ];

        $technicians = \App\Models\User::where('company_id', $maintenanceRequest->company_id)
            ->whereHas('role', function ($q) {
                $q->where('name', 'Technician');
            })
            ->orderBy('name')
            ->get();

        return view('maintenance-requests.show', compact('maintenanceRequest', 'statuses', 'priorities', 'technicians'));
    }

    public function edit(MaintenanceRequest $maintenanceRequest)
    {

        if (!auth()->user()->hasPermission('edit_maintenance_requests')) {
            abort(403, 'غير مصرح لك بتعديل البلاغات');
        }

        $user = Auth::user();

        if (
            Auth::check() &&
            (
                $maintenanceRequest->company_id != $user->company_id ||
                ($user->station_id && $maintenanceRequest->station_id != $user->station_id)
            )
        ) {
            abort(403, 'غير مصرح لك بتعديل هذا البلاغ');
        }

        $departments = Department::orderBy('name')->get();

        $statuses = [
            'new' => 'جديد',
            'forwarded_to_maintenance' => 'محول إلى الصيانة',
            'in_progress' => 'قيد التنفيذ',
            'completed' => 'مكتمل',
            'closed' => 'مغلق',
            'cancelled' => 'ملغي',
        ];

        $priorities = [
            'low' => 'منخفض',
            'medium' => 'متوسط',
            'high' => 'عالي',
            'urgent' => 'عاجل',
        ];

        return view('maintenance-requests.edit', compact(
            'maintenanceRequest',
            'departments',
            'statuses',
            'priorities'
        ));
    }

    public function update(Request $request, MaintenanceRequest $maintenanceRequest)
    {

        if (!auth()->user()->hasPermission('edit_maintenance_requests')) {
            abort(403, 'غير مصرح لك بتعديل البلاغات');
        }

        $user = Auth::user();

        if (
            Auth::check() &&
            (
                $maintenanceRequest->company_id != $user->company_id ||
                ($user->station_id && $maintenanceRequest->station_id != $user->station_id)
            )
        ) {
            abort(403, 'غير مصرح لك بتعديل هذا البلاغ');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'department_id' => 'nullable|exists:departments,id',
            'priority' => 'required|in:low,medium,high,urgent',
            'status' => 'required|in:new,under_review,forwarded_to_maintenance,in_progress,completed,closed,cancelled',
            'notes' => 'nullable|string',
        ]);

        $data = [
            'department_id' => $request->department_id,
            'title' => $request->title,
            'description' => $request->description,
            'priority' => $request->priority,
            'status' => $request->status,
            'notes' => $request->notes,
        ];

        if ($request->status === 'closed' && !$maintenanceRequest->closed_at) {
            $data['closed_at'] = now();
        }

        if ($request->status !== 'closed') {
            $data['closed_at'] = null;
        }

        $maintenanceRequest->update($data);

        return redirect()
            ->route('maintenance-requests.index')
            ->with('success', 'تم تحديث البلاغ بنجاح');
    }

    public function destroy(MaintenanceRequest $maintenanceRequest)
    {

        if (!auth()->user()->hasPermission('delete_maintenance_requests')) {
            abort(403, 'غير مصرح لك بحذف البلاغات');
        }

        $user = Auth::user();

        if (
            Auth::check() &&
            (
                $maintenanceRequest->company_id != $user->company_id ||
                ($user->station_id && $maintenanceRequest->station_id != $user->station_id)
            )
        ) {
            abort(403, 'غير مصرح لك بحذف هذا البلاغ');
        }

        $maintenanceRequest->delete();

        return redirect()
            ->route('maintenance-requests.index')
            ->with('success', 'تم حذف البلاغ بنجاح');
    }

    private function generateReportNumber(): string
    {
        $date = now()->format('Ymd');
        $lastId = (MaintenanceRequest::max('id') ?? 0) + 1;

        return 'MR-' . $date . '-' . str_pad((string) $lastId, 5, '0', STR_PAD_LEFT);
    }

    public function updatestatus(Request $request, $id)
    {
        $request->validate([
            'status' => ['required', 'in:new,under_review,forwarded_to_maintenance,in_progress,completed,closed,cancelled'],
        ]);

        $maintenanceRequest = MaintenanceRequest::findOrFail($id);

        $maintenanceRequest->update([
            'status' => $request->status,
        ]);

        return redirect()
            ->route('maintenance-requests.show', $maintenanceRequest->id)
            ->with('success', 'تم تحديث حالة البلاغ بنجاح');
    }
}