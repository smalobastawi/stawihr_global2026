<?php

namespace App\Http\Controllers\Payroll;

use App\Models\SalaryDeductions;
use App\Http\Requests\StoreSalaryDeductionsRequest;
use App\Http\Requests\UpdateSalaryDeductionsRequest;

class SalaryDeductionsController extends Controller
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
     * @param  \App\Http\Requests\StoreSalaryDeductionsRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreSalaryDeductionsRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\SalaryDeductions  $salaryDeductions
     * @return \Illuminate\Http\Response
     */
    public function show(SalaryDeductions $salaryDeductions)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\SalaryDeductions  $salaryDeductions
     * @return \Illuminate\Http\Response
     */
    public function edit(SalaryDeductions $salaryDeductions)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateSalaryDeductionsRequest  $request
     * @param  \App\Models\SalaryDeductions  $salaryDeductions
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateSalaryDeductionsRequest $request, SalaryDeductions $salaryDeductions)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\SalaryDeductions  $salaryDeductions
     * @return \Illuminate\Http\Response
     */
    public function destroy(SalaryDeductions $salaryDeductions)
    {
        //
    }
}
