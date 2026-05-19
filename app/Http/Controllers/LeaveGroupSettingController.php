<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLeaveGroupSettingRequest;
use App\Http\Requests\UpdateLeaveGroupSettingRequest;
use App\Models\LeaveGroupSetting;

class LeaveGroupSettingController extends Controller
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
     * @param  \App\Http\Requests\StoreLeaveGroupSettingRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreLeaveGroupSettingRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\LeaveGroupSetting  $leaveGroupSetting
     * @return \Illuminate\Http\Response
     */
    public function show(LeaveGroupSetting $leaveGroupSetting)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\LeaveGroupSetting  $leaveGroupSetting
     * @return \Illuminate\Http\Response
     */
    public function edit(LeaveGroupSetting $leaveGroupSetting)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateLeaveGroupSettingRequest  $request
     * @param  \App\Models\LeaveGroupSetting  $leaveGroupSetting
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateLeaveGroupSettingRequest $request, LeaveGroupSetting $leaveGroupSetting)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\LeaveGroupSetting  $leaveGroupSetting
     * @return \Illuminate\Http\Response
     */
    public function destroy(LeaveGroupSetting $leaveGroupSetting)
    {
        //
    }
}
