<?php
/*
 * Copyright (c) 2023/9/6 sw@stawitech
 */

namespace App\Http\Controllers\Api;

use App\Models\BiometricDevice;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\MorphoDevice;
use Illuminate\Support\Facades\Log;

class BiometricDevicesController
{
    public function updateDeviceList(Request $request)
    {
        $datas = json_decode(file_get_contents("php://input"));

       
        foreach ($datas as $data) {
            $biometricDevice = [];
            $biometricDeviceType = $data->is_attendance ? 'attendance' : 'other'; 
            $biometricDevice['device_name'] = $data->terminal_name . ' - ' . $data->alias;
            $biometricDevice['device_ip_address'] = $data->ip_address;
            $biometricDevice['device_location'] = $data->alias;
            $biometricDevice['device_type'] = $biometricDeviceType;
            $biometricDevice['id'] = $data->id;
            $biometricDevice['status'] = $data->state;
            $biometricDevice['device_serial'] = $data->sn;
        
            $biometricDevice1 = BiometricDevice::updateOrCreate(
                [ 'device_serial' => $data->sn],
                $biometricDevice
            );
        
        }
        

return 'success';
}

}