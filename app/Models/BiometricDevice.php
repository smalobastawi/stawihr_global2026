<?php
/*
 * Copyright (c) 2023/9/6 sw@stawitech
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToCompany;
use Rats\Zkteco\Lib\ZKTeco;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class BiometricDevice extends Model
{
    //use BelongsToCompany;

    use HasFactory;
    use  LogsActivity;
    protected $table = 'morpho_devices';

    protected $fillable = [
        'device_ip_address',
        'device_serial',
        'port',
        'device_name',
        'device_location',
        'timeout',
        'device_status',
        'status',
        'device_type',
    ];

    public static function updateDeviceStatus()
    {
        // Update the device status
        $zkDevice = 0;
        $deviceSerial = 0;
        $deviceStatus = 0;
        $biometricDevice1 = BiometricDevice::all();

        foreach ($biometricDevice1 as $biometricDevice) {
            $ch = curl_init($biometricDevice->device_ip_address);
            curl_setopt($ch, CURLOPT_TIMEOUT, 3);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $data = curl_exec($ch);
            $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($httpcode !== 0) {

                if ($deviceSerial) {
                    $deviceStatus = 'active';
                } else {
                    $deviceStatus = 'offline';
                }
            } else {
                $deviceStatus = 'offline';
            }

            $data = [
                'device_status' => $deviceStatus,
            ];

            $biometricDevice->update($data);
        }


        // Redirect back to the index page
        return 'success';
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll();
    }
}
