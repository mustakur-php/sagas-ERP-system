<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FuelOrderTransport extends Model
{
    protected $fillable = [
        'fuel_order_id',
        'supplier_id',
        'carrier_id',
        'transport_cost',
        'driver_name',
        'truck_number',
        'departure_time',
        'arrival_time',
        'status',
        'notes',
        'assigned_by',
        'assigned_at',
    ];

    public function order()
    {
        return $this->belongsTo(FuelOrder::class, 'fuel_order_id');
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function carrier()
    {
        return $this->belongsTo(Carrier::class);
    }

    public function assignedBy()
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }
}
