<?php
/*
 * Copyright (c) 2023/9/6 sw@stawitech
 */

namespace App\Http\Controllers;

use App\Models\LeaversAndJoiners;
use App\Http\Requests\StoreLeaversAndJoinersRequest;
use App\Http\Requests\UpdateLeaversAndJoinersRequest;

class LeaversAndJoinersController extends Controller
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
     * @param  \App\Http\Requests\StoreLeaversAndJoinersRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreLeaversAndJoinersRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\LeaversAndJoiners  $leaversAndJoiners
     * @return \Illuminate\Http\Response
     */
    public function show(LeaversAndJoiners $leaversAndJoiners)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\LeaversAndJoiners  $leaversAndJoiners
     * @return \Illuminate\Http\Response
     */
    public function edit(LeaversAndJoiners $leaversAndJoiners)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateLeaversAndJoinersRequest  $request
     * @param  \App\Models\LeaversAndJoiners  $leaversAndJoiners
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateLeaversAndJoinersRequest $request, LeaversAndJoiners $leaversAndJoiners)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\LeaversAndJoiners  $leaversAndJoiners
     * @return \Illuminate\Http\Response
     */
    public function destroy(LeaversAndJoiners $leaversAndJoiners)
    {
        //
    }
}
