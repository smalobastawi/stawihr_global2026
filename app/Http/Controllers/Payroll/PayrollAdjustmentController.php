<?php

namespace App\Http\Controllers\Payroll;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePayrollAdjustmentRequest;
use App\Http\Requests\UpdatePayrollAdjustmentRequest;
use App\Models\PayrollAdjustment;

class PayrollAdjustmentController extends Controller
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
     * @param  \App\Http\Requests\StorePayrollAdjustmentRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StorePayrollAdjustmentRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\PayrollAdjustment  $payrollAdjustment
     * @return \Illuminate\Http\Response
     */
    public function show(PayrollAdjustment $payrollAdjustment)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\PayrollAdjustment  $payrollAdjustment
     * @return \Illuminate\Http\Response
     */
    public function edit(PayrollAdjustment $payrollAdjustment)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdatePayrollAdjustmentRequest  $request
     * @param  \App\Models\PayrollAdjustment  $payrollAdjustment
     * @return \Illuminate\Http\Response
     */
    public function update(UpdatePayrollAdjustmentRequest $request, PayrollAdjustment $payrollAdjustment)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\PayrollAdjustment  $payrollAdjustment
     * @return \Illuminate\Http\Response
     */
    public function destroy(PayrollAdjustment $payrollAdjustment)
    {
        //
    }
}
