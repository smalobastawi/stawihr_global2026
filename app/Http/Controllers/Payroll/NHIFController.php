<?php
/*
 * Copyright (c) 2023/9/6 sw@stawitech
 */

namespace App\Http\Controllers\Payroll;


namespace App\Http\Controllers\Payroll;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\NHIF;
class NHIFController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $nhifRates = NHIF::all();
        return view('admin.payroll.nhif.nhifSetup',['nhifRates'=>$nhifRates]);
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
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\NHIF  $nHIF
     * @return \Illuminate\Http\Response
     */
    public function show(NHIF $nHIF)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\NHIF  $nHIF
     * @return \Illuminate\Http\Response
     */
    public function edit(NHIF $nHIF)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\NHIF  $nHIF
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, NHIF $nHIF)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\NHIF  $nHIF
     * @return \Illuminate\Http\Response
     */
    public function destroy(NHIF $nHIF)
    {
        //
    }
}
