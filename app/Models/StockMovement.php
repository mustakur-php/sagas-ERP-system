<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StockMovement extends Model
{
    use HasFactory;

    public const TYPE_OPENING_BALANCE = 'opening_balance';
    public const TYPE_PURCHASE = 'purchase';
    public const TYPE_SALE = 'sale';
    public const TYPE_RETURNED = 'returned';
    public const TYPE_ADJUSTMENT = 'adjustment';

    protected $fillable = [
        'station_tank_id',
        'station_id',
        'fuel_type_id',
        'movement_type',
        'quantity',
        'movement_date',
        'reference_type',
        'reference_id',
        'notes',
    ];

    protected $casts = [
        'movement_date' => 'date',
    ];

    public function tank()
    {
        return $this->belongsTo(StationTank::class, 'station_tank_id');
    }

    public function station()
    {
        return $this->belongsTo(Station::class);
    }

    public function fuelType()
    {
        return $this->belongsTo(FuelType::class);
    }

    public function isInflow(): bool
    {
        return in_array($this->movement_type, [
            self::TYPE_OPENING_BALANCE,
            self::TYPE_PURCHASE,
            self::TYPE_RETURNED,
            self::TYPE_ADJUSTMENT,
        ], true);
    }

    public function isOutflow(): bool
    {
        return $this->movement_type === self::TYPE_SALE;
    }
}