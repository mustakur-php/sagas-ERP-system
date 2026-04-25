<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vendor extends Model
{
    protected $fillable = [
        'code',
        'name',
        'name_en',
        'phone',
        'email',
        'vendor_type',
        'is_active',
        'notes',
    ];

    public function fuelPurchases()
    {
        return $this->hasMany(FuelPurchase::class);
    }

    public function payments()
    {
        return $this->hasMany(VendorPayment::class);
    }
}