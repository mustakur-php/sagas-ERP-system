<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Station extends Model
{
    protected $fillable = [
        'code',
        'name',
        'name_en',
        'region',
        'city',
        'status',
        'company_id',
    ];

    public function fuelPurchases()
    {
        return $this->hasMany(FuelPurchase::class);
    }

    public const STATUSES = [
    'active' => 'نشطة',
    'inactive' => 'غير نشطة',
    'under_maintenance' => 'تحت الصيانة',
    'stopped' => 'متوقفة',
    ];

    public function dailyClosings()
    {
        return $this->hasMany(DailyClosing::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function maintenanceRequests()
    {
        return $this->hasMany(MaintenanceRequest::class);
    }

    public function fuelPrices()
    {
        return $this->hasMany(StationFuelPrice::class);
    }

    public function nozzles()
    {
        return $this->hasMany(StationNozzle::class);
    }

    public function tanks()
    {
        return $this->hasMany(StationTank::class);
    }

    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class);
    }

    public function fuelOrders()
    {
        return $this->hasMany(FuelOrder::class);
    }

}