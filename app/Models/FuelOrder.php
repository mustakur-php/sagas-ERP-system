<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\FuelOrderFinance;


class FuelOrder extends Model
{
    use HasFactory;

    public const STATUS_PENDING = 'pending';
    public const STATUS_PARTIAL = 'partial';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_REJECTED = 'rejected';
    public const STATUS_RECEIVED = 'received';
    public const STATUS_CANCELLED = 'cancelled';

    protected $fillable = [
        'order_number',
        'station_id',
        'company_id',
        'created_by',
        'status',
        'current_step',
        'request_date',
        'notes',
        'submitted_at',
        'completed_at',
        'cancelled_at',
        'supplier_id',
        'carrier_id',
        'delivery_method',
        'transport_cost',
        'expected_delivery_date',
        'transport_notes',
        'payment_method',
        'payment_reference',
        'payment_amount',
        'paid_at',
    ];

    protected $casts = [
        'request_date' => 'date',
        'submitted_at' => 'datetime',
        'completed_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'paid_at' => 'datetime',
    ];

    public function station()
    {
        return $this->belongsTo(Station::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function items()
    {
        return $this->hasMany(FuelOrderItem::class);
    }

    public function logs()
    {
        return $this->hasMany(FuelOrderLog::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function carrier()
    {
        return $this->belongsTo(Carrier::class);
    }

    public function canBeApproved(): bool
    {
        return $this->current_step === 'operations_review'
            && $this->status === 'submitted';
    }

    public function canBeRejected(): bool
    {
        return $this->current_step === 'operations_review'
            && $this->status === 'submitted';
    }

    public function canBeReceived(): bool
    {
        return $this->current_step === 'station_receipt';
    }

    public function finance()
    {
        return $this->hasOne(FuelOrderFinance::class);
    }
    
    public function transport()
    {
        return $this->hasOne(FuelOrderTransport::class);
    }

}