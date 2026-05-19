<?php
/*
 * Copyright (c) 2023/9/6 sw@stawitech
 */

namespace App\Http\Controllers;

use App\Http\Requests\StoreMorphoDeviceRequest;
use App\Http\Requests\UpdateMorphoDeviceRequest;
use App\Models\MorphoDevice;

class MorphoDeviceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $apiKey=env('JAVA_API_KEY');
        $requestApiKey=  \Request::header('API_KEY');
        if($requestApiKey && $requestApiKey==$apiKey){
        $devices=MorphoDevice::all();
        return response()->json($devices);
        }
        
        //
        abort(401, 'UnAuthorised');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreMorphoDeviceRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreMorphoDeviceRequest $request)
    {

        $alreadyAdded = BiometricDevice::where('device_ip_address', $request->device_ip_address)->get();
        if($alreadyAdded->count()>0 )
        {
            return redirect()->route('biometricDevices')->with(['error'=>'Device already added']);
        }


        $biometricDevice = new MorphoDevice();
        $biometricDevice->device_ip_address = $request->device_ip_address;
        $biometricDevice->device_location = $request->device_location;
        $biometricDevice->device_serial = $request->deviceSerial;
       // $biometricDevice->device_status = $deviceStatus;

        $biometricDevice->save();
        return redirect()->route('biometricDevices')->with(['success'=>'Device added successfully']);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\MorphoDevice  $morphoDevice
     * @return \Illuminate\Http\Response
     */
    public function show(MorphoDevice $morphoDevice)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\MorphoDevice  $morphoDevice
     * @return \Illuminate\Http\Response
     */
    public function edit(MorphoDevice $morphoDevice)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateMorphoDeviceRequest  $request
     * @param  \App\Models\MorphoDevice  $morphoDevice
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateMorphoDeviceRequest $request, MorphoDevice $morphoDevice)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\MorphoDevice  $morphoDevice
     * @return \Illuminate\Http\Response
     */
    public function destroy(MorphoDevice $morphoDevice)
    {
        //
    }
}
