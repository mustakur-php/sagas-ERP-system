<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class HrDepartment extends Model
{
    use HasFactory;

    protected $table = 'hr_departments';

    protected $fillable = [
        'company_id',
        'name_ar',
        'name_en',
        'code',
        'manager_employee_id',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function employees()
    {
        return $this->hasMany(HrEmployee::class, 'department_id');
    }

    public function positions()
    {
        return $this->hasMany(HrPosition::class, 'department_id');
    }

    public function manager()
    {
        return $this->belongsTo(HrEmployee::class, 'manager_employee_id');
    }
}