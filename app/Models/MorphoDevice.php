<?php
/*
 * Copyright (c) 2023/9/6 sw@stawitech
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToCompany;

class MorphoDevice extends Model
{
    //use BelongsToCompany;

    use HasFactory;
    protected $table = 'morpho_devices';

    protected $fillable = [
        'device_ip_address',
        'device_serial',
        'port',
        'device_location',
        'timeout',
        'device_status',
        'device_name',
        'device_type'
    ];
}
