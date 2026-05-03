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
            $query->whereHas('station', function ($q) use ($user) {
                $q->where('company_id', $user->company_id);
            });
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
            'under_review' => 'تحت مراجعة التشغيل',
            'assigned_to_department' => 'محول للقسم',
            'assigned_to_technician' => 'معين على مسؤول',
            'in_progress' => 'قيد التنفيذ',
            'pending_operations_approval' => 'بانتظار اعتماد التشغيل',
            'returned' => 'معاد للتعديل',
            'closed' => 'مغلق',
            'cancelled' => 'ملغي',
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

        
        $maintenanceRequest = MaintenanceRequest::create([
            'report_number' => $this->generateReportNumber(),
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
            'current_step' => 'operations_review',
            'assigned_to' => null,
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
            'under_review' => 'تحت مراجعة التشغيل',
            'assigned_to_department' => 'محول للقسم',
            'assigned_to_technician' => 'معين على مسؤول',
            'in_progress' => 'قيد التنفيذ',
            'pending_operations_approval' => 'بانتظار اعتماد التشغيل',
            'returned' => 'معاد للقسم',
            'closed' => 'مغلق',
            'cancelled' => 'ملغي',
        ];

        $priorities = [
            'low' => 'منخفضة',
            'medium' => 'متوسطة',
            'high' => 'عالية',
            'urgent' => 'عاجلة',
        ];

        $technicians = \App\Models\User::where('company_id', optional($maintenanceRequest->station)->company_id)
            ->whereHas('roles', function ($q) {
                $q->where('name', 'Technician');
            })
            ->orderBy('name')
            ->get();
        $departments = Department::orderBy('name')->get();

        return view('maintenance-requests.show', compact(
            'maintenanceRequest',
            'statuses',
            'priorities',
            'technicians',
            'departments'
        ));
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
                optional($maintenanceRequest->station)->company_id != $user->company_id ||
                ($user->station_id && $maintenanceRequest->station_id != $user->station_id)
            )
        ) {
            abort(403, 'غير مصرح لك بتعديل هذا البلاغ');
        }

        $departments = Department::orderBy('name')->get();

        $statuses = [
            'new' => 'جديد',
            'under_review' => 'تحت مراجعة التشغيل',
            'assigned_to_department' => 'محول للقسم',
            'assigned_to_technician' => 'معين على مسؤول',
            'in_progress' => 'قيد التنفيذ',
            'pending_operations_approval' => 'بانتظار اعتماد التشغيل',
            'returned' => 'معاد للقسم',
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

    public function update(Request $request, $id)
    {
        if (!auth()->user()->hasPermission('edit_maintenance_requests')) {
            abort(403, 'غير مصرح لك بتعديل البلاغات');
        }

        $user = Auth::user();
        $maintenanceRequest = MaintenanceRequest::findOrFail($id);

        if (
            Auth::check() &&
            (
                optional($maintenanceRequest->station)->company_id != $user->company_id ||
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
            'status' => 'required|in:new,under_review,assigned_to_department,assigned_to_technician,in_progress,pending_operations_approval,returned,closed,cancelled',
            'notes' => 'nullable|string',
        ]);

        $data = [
            'department_id' => $request->department_id,
            'title' => $request->title,
            'description' => $request->description,
            'priority' => $request->priority,
            'status' => $request->status,
            'notes' => $this->appendNote($maintenanceRequest->notes, $request->notes),
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
                optional($maintenanceRequest->station)->company_id != $user->company_id ||
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

    public function assignDepartment(Request $request, $id)
    {
        $request->validate([
            'department_id' => ['required', 'exists:departments,id'],
            'notes' => ['nullable', 'string'],
        ]);

        $maintenanceRequest = MaintenanceRequest::findOrFail($id);

        $maintenanceRequest->update([
            'department_id' => $request->department_id,
            'status' => 'assigned_to_department',
            'current_step' => 'department_review',
            'notes' => $this->appendNote($maintenanceRequest->notes, $request->notes),
        ]);

        return back()->with('success', 'تم تحويل البلاغ إلى القسم المختص');
    }

    public function assignTechnician(Request $request, $id)
    {
        $request->validate([
            'assigned_to' => ['required', 'exists:users,id'],
            'notes' => ['nullable', 'string'],
        ]);

        $maintenanceRequest = MaintenanceRequest::findOrFail($id);
        $maintenanceRequest->update([
            'assigned_to' => $request->assigned_to,
            'status' => 'assigned_to_technician',
            'current_step' => 'technician_work',
            'notes' => $this->appendNote($maintenanceRequest->notes, $request->notes),
        ]);

        return back()->with('success', 'تم تعيين المسؤول عن التنفيذ');
    }

    public function markResolved(Request $request, $id)
    {
        $request->validate([
            'notes' => ['required', 'string'],
        ]);

        $maintenanceRequest = MaintenanceRequest::findOrFail($id);
        $maintenanceRequest->update([
            'status' => 'pending_operations_approval',
            'current_step' => 'operations_final_review',
            'notes' => $this->appendNote($maintenanceRequest->notes, $request->notes),
        ]);

        return back()->with('success', 'تم إرسال البلاغ لاعتماد التشغيل');
    }

    public function approveClosure($id)
    {
        $maintenanceRequest = MaintenanceRequest::findOrFail($id);
        $maintenanceRequest->update([
            'status' => 'closed',
            'current_step' => 'closed',
            'closed_at' => now(),
            'assigned_to' => null,
        ]);

        return back()->with('success', 'تم إغلاق البلاغ بنجاح');
    }

    public function returnToDepartment(Request $request, $id)
    {
        $request->validate([
            'reason' => ['required', 'string'],
        ]);

        $maintenanceRequest = MaintenanceRequest::findOrFail($id);
        $maintenanceRequest->update([
            'status' => 'returned',
            'current_step' => 'department_review',
            'notes' => $this->appendNote($maintenanceRequest->notes, 'سبب الإرجاع: ' . $request->reason),
        ]);

        return back()->with('success', 'تم إرجاع البلاغ إلى القسم');
    }

    private function appendNote(?string $oldNotes, ?string $newNote): ?string
    {
        if (!$newNote) {
            return $oldNotes;
        }

        return $oldNotes
            ? $oldNotes . "\n----------------\n" . $newNote
            : $newNote;
    }

}