<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class HrActivity extends Model
{
    use HasFactory;

    protected $table = 'hr_activities';

    protected $fillable = [
        'company_id',
        'employee_id',
        'user_id',
        'type',
        'title',
        'description',
        'meta',
    ];

    protected $casts = [
        'meta' => 'array',
    ];

    public function employee()
    {
        return $this->belongsTo(HrEmployee::class, 'employee_id');
    }
}
