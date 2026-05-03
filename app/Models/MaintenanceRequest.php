<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MaintenanceRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'report_number',
        'station_id',
        'user_id',
        'department_id',
        'assigned_to',
        'title',
        'description',
        'priority',
        'status',
        'current_step',
        'reported_at',
        'closed_at',
        'notes',
    ];

    protected $casts = [
        'reported_at' => 'datetime',
        'closed_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->reported_at)) {
                $model->reported_at = now();
            }
        });
    }

    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function station()
    {
        return $this->belongsTo(Station::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function jobOrder()
    {
        return $this->hasOne(MaintenanceJobOrder::class);
    }

    public function logs()
    {
        return $this->hasMany(MaintenanceRequestLog::class);
    }

}