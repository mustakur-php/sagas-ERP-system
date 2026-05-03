<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class HrWorkLocation extends Model
{
    use HasFactory;

    protected $table = 'hr_work_locations';

    protected $fillable = [
        'company_id',
        'station_id',
        'name',
        'latitude',
        'longitude',
        'radius_meters',
        'is_active',
    ];

    protected $casts = [
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
        'is_active' => 'boolean',
    ];

    public function employees()
    {
        return $this->hasMany(HrEmployee::class, 'work_location_id');
    }
}