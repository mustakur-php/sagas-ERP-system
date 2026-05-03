<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class HrPosition extends Model
{
    use HasFactory;

    protected $table = 'hr_positions';

    protected $fillable = [
        'company_id',
        'department_id',
        'title_ar',
        'title_en',
        'code',
        'default_basic_salary',
        'is_active',
    ];

    protected $casts = [
        'default_basic_salary' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    /*
    | العلاقات
    */

    public function department()
    {
        return $this->belongsTo(HrDepartment::class, 'department_id');
    }

    public function employees()
    {
        return $this->hasMany(HrEmployee::class, 'position_id');
    }
}