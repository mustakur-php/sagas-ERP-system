<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MaintenanceJobOrder extends Model
{
    protected $fillable = [
        'maintenance_request_id',
        'company_id',
        'assigned_to',
        'status',
        'assigned_at',
        'started_at',
        'completed_at',
        'technician_notes',
        'resolution_notes'
    ];

    public function maintenanceRequest()
    {
        return $this->belongsTo(MaintenanceRequest::class, 'maintenance_request_id');
    }

    public function technician()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }
}
