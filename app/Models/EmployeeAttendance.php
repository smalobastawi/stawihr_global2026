<?php
/*
 * Copyright (c) 2023/9/6 sw@stawitech
 */

namespace App\Models;

use App\Traits\WithBranchPermissions;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class EmployeeAttendance extends Model
{
    //use BelongsToCompany;

    use WithBranchPermissions;
    protected $table = 'employee_attendance';
    protected $primaryKey = 'employee_attendance_id';
}
