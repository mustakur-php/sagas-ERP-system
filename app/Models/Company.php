<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Company extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'status',
        'notes',
        'parent_company_id',
    ];

    public function stations()
    {
        return $this->hasMany(Station::class);
    }

    public function parent()
    {
        return $this->belongsTo(Company::class, 'parent_company_id');
    }

    public function children()
    {
        return $this->hasMany(Company::class, 'parent_company_id');
    }

    public function isParentCompany(): bool
    {
        return is_null($this->parent_company_id);
    }

    public function isChildCompany(): bool
    {
        return !is_null($this->parent_company_id);
    }

    public function fuelOrders()
    {
        return $this->hasMany(FuelOrder::class);
    }

    public function suppliers()
    {
        return $this->hasMany(Supplier::class);
    }

    public function carriers()
    {
        return $this->hasMany(Carrier::class);
    }

}