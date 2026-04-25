<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FuelType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'price',
    ];

    public function stationFuelPrices()
    {
        return $this->hasMany(StationFuelPrice::class);
    }

    public function tanks()
    {
        return $this->hasMany(StationTank::class);
    }

    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class);
    }

    public function fuelOrderItems()
    {
        return $this->hasMany(FuelOrderItem::class);
    }

    public function supplierFuelPrices()
    {
        return $this->hasMany(SupplierFuelPrice::class);
    }

}