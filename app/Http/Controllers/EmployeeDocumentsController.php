<?php
/*
 * Copyright (c) 2023/9/6 sw@stawitech
 */

namespace App\Http\Controllers;

use App\Models\EmployeeDocuments;
use App\Http\Requests\StoreEmployeeDocumentsRequest;
use App\Http\Requests\UpdateEmployeeDocumentsRequest;

class EmployeeDocumentsController extends Controller
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
     * @param  \App\Http\Requests\StoreEmployeeDocumentsRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreEmployeeDocumentsRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\EmployeeDocuments  $employeeDocuments
     * @return \Illuminate\Http\Response
     */
    public function show(EmployeeDocuments $employeeDocuments)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\EmployeeDocuments  $employeeDocuments
     * @return \Illuminate\Http\Response
     */
    public function edit(EmployeeDocuments $employeeDocuments)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateEmployeeDocumentsRequest  $request
     * @param  \App\Models\EmployeeDocuments  $employeeDocuments
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateEmployeeDocumentsRequest $request, EmployeeDocuments $employeeDocuments)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\EmployeeDocuments  $employeeDocuments
     * @return \Illuminate\Http\Response
     */
    public function destroy(EmployeeDocuments $employeeDocuments)
    {
        //
    }
}
