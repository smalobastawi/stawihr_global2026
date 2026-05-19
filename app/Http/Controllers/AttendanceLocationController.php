<?php

namespace App\Http\Controllers;

use App\Models\AttendanceLocation;
use App\Http\Requests\StoreAttendanceLocationRequest;
use App\Http\Requests\UpdateAttendanceLocationRequest;

class AttendanceLocationController extends Controller
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
     * @param  \App\Http\Requests\StoreAttendanceLocationRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreAttendanceLocationRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\AttendanceLocation  $attendanceLocation
     * @return \Illuminate\Http\Response
     */
    public function show(AttendanceLocation $attendanceLocation)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\AttendanceLocation  $attendanceLocation
     * @return \Illuminate\Http\Response
     */
    public function edit(AttendanceLocation $attendanceLocation)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateAttendanceLocationRequest  $request
     * @param  \App\Models\AttendanceLocation  $attendanceLocation
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateAttendanceLocationRequest $request, AttendanceLocation $attendanceLocation)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\AttendanceLocation  $attendanceLocation
     * @return \Illuminate\Http\Response
     */
    public function destroy(AttendanceLocation $attendanceLocation)
    {
        //
    }
}
