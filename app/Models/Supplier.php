<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Supplier extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'name',
        'phone',
        'contact_person',
        'notes',
        'is_active',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function fuelPrices()
    {
        return $this->hasMany(SupplierFuelPrice::class);
    }

    public function fuelOrders()
    {
        return $this->hasMany(FuelOrder::class);
    }
}