<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VendorPayment extends Model
{
    protected $fillable = [
        'vendor_id',
        'fuel_purchase_id',
        'payment_date',
        'amount',
        'payment_method',
        'reference_no',
        'notes',
    ];

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    public function fuelPurchase()
    {
        return $this->belongsTo(FuelPurchase::class);
    }
}