<?php
/*
 * Copyright (c) 2023/9/6 sw@stawitech
 */

namespace App\Http\Controllers;

use App\Models\LunchReport;
use App\Http\Requests\StoreLunchReportRequest;
use App\Http\Requests\UpdateLunchReportRequest;

class LunchReportController extends Controller
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
     * @param  \App\Http\Requests\StoreLunchReportRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreLunchReportRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\LunchReport  $lunchReport
     * @return \Illuminate\Http\Response
     */
    public function show(LunchReport $lunchReport)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\LunchReport  $lunchReport
     * @return \Illuminate\Http\Response
     */
    public function edit(LunchReport $lunchReport)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateLunchReportRequest  $request
     * @param  \App\Models\LunchReport  $lunchReport
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateLunchReportRequest $request, LunchReport $lunchReport)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\LunchReport  $lunchReport
     * @return \Illuminate\Http\Response
     */
    public function destroy(LunchReport $lunchReport)
    {
        //
    }
}
