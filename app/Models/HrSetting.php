<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class HrSetting extends Model
{
    use HasFactory;

    protected $table = 'hr_settings';

    protected $fillable = [
        'company_id',
        'group',
        'key',
        'value',
        'type',
        'description',
    ];
}
