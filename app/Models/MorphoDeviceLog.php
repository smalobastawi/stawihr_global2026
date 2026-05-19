<?php
/*
 * Copyright (c) 2023/9/6 sw@stawitech
 */

namespace App\Models;

use App\Traits\WithSupervisorPermissions;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MorphoDeviceLog extends Model
{
    use HasFactory;
    use WithSupervisorPermissions;

    protected $table = 'morpho_device_logs';
    protected $fillable = [
        'id_no',
        'user_first_name',
        'user_name',
        'device_id',
        'year',
        'month',
        'day',
        'hour',
        'minute',
        'second',
        'date_time',
        'ip_address',
        'time_logged',
        'date',
        'location',
        'payroll_number',
        'updated_status',
        
    ];

    protected $dates =['time_logged', 'date'];

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'id_no', 'national_id');
    }

    public function biometricDevice()
    {
        return $this->belongsTo(MorphoDevice::class, 'device_id', 'device_serial');
    }

}
