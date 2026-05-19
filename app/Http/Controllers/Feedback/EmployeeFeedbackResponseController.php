<?php

namespace App\Http\Controllers\Feedback;
use App\Http\Controllers\Controller;

use App\Models\EmployeeFeedbackResponse;
use App\Http\Requests\StoreEmployeeFeedbackResponseRequest;
use App\Http\Requests\UpdateEmployeeFeedbackResponseRequest;

class EmployeeFeedbackResponseController extends Controller
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
     * @param  \App\Http\Requests\StoreEmployeeFeedbackResponseRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreEmployeeFeedbackResponseRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\EmployeeFeedbackResponse  $employeeFeedbackResponse
     * @return \Illuminate\Http\Response
     */
    public function show(EmployeeFeedbackResponse $employeeFeedbackResponse)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\EmployeeFeedbackResponse  $employeeFeedbackResponse
     * @return \Illuminate\Http\Response
     */
    public function edit(EmployeeFeedbackResponse $employeeFeedbackResponse)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateEmployeeFeedbackResponseRequest  $request
     * @param  \App\Models\EmployeeFeedbackResponse  $employeeFeedbackResponse
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateEmployeeFeedbackResponseRequest $request, EmployeeFeedbackResponse $employeeFeedbackResponse)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\EmployeeFeedbackResponse  $employeeFeedbackResponse
     * @return \Illuminate\Http\Response
     */
    public function destroy(EmployeeFeedbackResponse $employeeFeedbackResponse)
    {
        //
    }
}
