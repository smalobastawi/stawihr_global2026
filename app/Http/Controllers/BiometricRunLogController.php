<?php
/*
 * Copyright (c) 2023/9/6 sw@stawitech
 */

namespace App\Http\Controllers;

use App\Models\BiometricRunLog;
use App\Http\Requests\StoreBiometricRunLogRequest;
use App\Http\Requests\UpdateBiometricRunLogRequest;

class BiometricRunLogController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
     * @param  \App\Http\Requests\StoreBiometricRunLogRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreBiometricRunLogRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\BiometricRunLog  $biometricRunLog
     * @return \Illuminate\Http\Response
     */
    public function show(BiometricRunLog $biometricRunLog)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\BiometricRunLog  $biometricRunLog
     * @return \Illuminate\Http\Response
     */
    public function edit(BiometricRunLog $biometricRunLog)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateBiometricRunLogRequest  $request
     * @param  \App\Models\BiometricRunLog  $biometricRunLog
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateBiometricRunLogRequest $request, BiometricRunLog $biometricRunLog)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\BiometricRunLog  $biometricRunLog
     * @return \Illuminate\Http\Response
     */
    public function destroy(BiometricRunLog $biometricRunLog)
    {
        //
    }
}
