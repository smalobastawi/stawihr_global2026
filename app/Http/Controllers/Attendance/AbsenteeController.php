<?php
/*
 * Copyright (c) 2023/9/6 sw@stawitech
 */

namespace App\Http\Controllers\Attendance;
use App\Http\Controllers\Controller;
use App\Models\Absentee;
use Illuminate\Http\Request;


class AbsenteeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //

        {{{}}}
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
     * @param  \App\Absentee  $absentee
     * @return \Illuminate\Http\Response
     */
    public function show(Absentee $absentee)

    {
        //
        $name=$absentee->id();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Model\Absentee  $absentee
     * @return \Illuminate\Http\Response
     */
    public function edit(Absentee $absentee)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Absentee  $absentee
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Absentee $absentee)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Absentee  $absentee
     * @return \Illuminate\Http\Response
     */
    public function destroy(Absentee $absentee)
    {
        //
    }
}
