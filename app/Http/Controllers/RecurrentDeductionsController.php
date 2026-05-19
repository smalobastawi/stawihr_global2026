<?php
/*
 * Copyright (c) 2023/9/6 sw@stawitech
 */

namespace App\Http\Controllers;

use App\Models\RecurrentDeductions;
use App\Http\Requests\StoreRecurrentDeductionsRequest;
use App\Http\Requests\UpdateRecurrentDeductionsRequest;

class RecurrentDeductionsController extends Controller
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
     * @param  \App\Http\Requests\StoreRecurrentDeductionsRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreRecurrentDeductionsRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\RecurrentDeductions  $recurrentDeductions
     * @return \Illuminate\Http\Response
     */
    public function show(RecurrentDeductions $recurrentDeductions)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\RecurrentDeductions  $recurrentDeductions
     * @return \Illuminate\Http\Response
     */
    public function edit(RecurrentDeductions $recurrentDeductions)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateRecurrentDeductionsRequest  $request
     * @param  \App\Models\RecurrentDeductions  $recurrentDeductions
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateRecurrentDeductionsRequest $request, RecurrentDeductions $recurrentDeductions)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\RecurrentDeductions  $recurrentDeductions
     * @return \Illuminate\Http\Response
     */
    public function destroy(RecurrentDeductions $recurrentDeductions)
    {
        //
    }
}
