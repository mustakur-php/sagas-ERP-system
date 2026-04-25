<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FuelPurchase extends Model
{
    protected $fillable = [
        'vendor_id',
        'station_id',
        'purchase_no',
        'purchase_date',
        'invoice_no',
        'total_amount',
        'paid_amount',
        'remaining_amount',
        'status',
        'notes',
    ];

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    public function station()
    {
        return $this->belongsTo(Station::class);
    }

    public function payments()
    {
        return $this->hasMany(VendorPayment::class);
    }
}