<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SupplierFuelPrice extends Model
{
    use HasFactory;

    protected $fillable = [
        'supplier_id',
        'fuel_type_id',
        'price_per_liter',
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function fuelType()
    {
        return $this->belongsTo(FuelType::class);
    }
}