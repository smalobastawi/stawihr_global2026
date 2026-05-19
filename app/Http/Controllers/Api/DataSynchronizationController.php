<?php
/*
 * Copyright (c) 2023/16/6 sw@stawitech
 */

namespace App\Http\Controllers\Api;
use App\Models\Employee;

class DataSynchronizationController
{
    public function syncEmployeeData()
    {
        $employees = Employee::all();
        return $employees;
    }
    public function synchDevices()
    {

    }

}