<?php

namespace App\Http\Controllers;

use App\Models\MaintenanceJobOrder;
use App\Models\MaintenanceRequest;
use App\Models\User;
use Illuminate\Http\Request;


class MaintenanceJobOrderController extends Controller
{
    public function index()
    {
        $jobs = MaintenanceJobOrder::with([
            'maintenanceRequest.station',
            'technician',
        ])->latest()->get();

        return view('maintenance_job_orders.index', compact('jobs'));
    }

    public function show($id)
    {
        $job = MaintenanceJobOrder::with([
            'maintenanceRequest.station',
            'maintenanceRequest.user',
            'technician',
        ])->findOrFail($id);

        $technicians = User::orderBy('name')->get();

        return view('maintenance_job_orders.show', compact('job', 'technicians'));
    }

    public function createFromRequest(Request $request, $requestId)
    {
        $request->validate([
            'assigned_to' => ['required', 'exists:users,id'],
        ]);

        $maintenanceRequest = MaintenanceRequest::with('jobOrder')->findOrFail($requestId);

        if ($maintenanceRequest->jobOrder) {
            return back()->with('error', 'تم إنشاء أمر صيانة مسبقاً لهذا البلاغ');
        }

        \DB::transaction(function () use ($maintenanceRequest, $request) {
            MaintenanceJobOrder::create([
                'maintenance_request_id' => $maintenanceRequest->id,
                'company_id' => $maintenanceRequest->company_id,
                'assigned_to' => $request->assigned_to,
                'assigned_at' => now(),
                'status' => 'assigned',
            ]);

            $maintenanceRequest->update([
                'status' => 'forwarded_to_maintenance',
            ]);
        });

        return redirect()
            ->route('maintenance-requests.show', $maintenanceRequest->id)
            ->with('success', 'تم إنشاء أمر العمل وتحويل البلاغ إلى الصيانة بنجاح');
    }

    public function assignTechnician(Request $request, $id)
    {
        $request->validate([
            'assigned_to' => ['required', 'exists:users,id'],
        ]);

        $job = MaintenanceJobOrder::findOrFail($id);

        $job->update([
            'assigned_to' => $request->assigned_to,
            'status' => 'assigned',
            'assigned_at' => now(),
        ]);

        return back()->with('success', 'تم تعيين الفني بنجاح');
    }

    public function updatestatus(Request $request, $id)
    {
        $request->validate([
            'status' => ['required', 'in:pending,assigned,in_progress,completed,cancelled'],
            'technician_notes' => ['nullable', 'string'],
            'resolution_notes' => ['nullable', 'string'],
        ]);

        $job = MaintenanceJobOrder::findOrFail($id);

        $data = [
            'status' => $request->status,
            'technician_notes' => $request->technician_notes,
            'resolution_notes' => $request->resolution_notes,
        ];

        if ($request->status === 'in_progress' && !$job->started_at) {
            $data['started_at'] = now();
        }

        if ($request->status === 'completed' && !$job->completed_at) {
            $data['completed_at'] = now();
        }

        $job->update($data);

        return back()->with('success', 'تم تحديث حالة طلب الصيانة');
    }
}