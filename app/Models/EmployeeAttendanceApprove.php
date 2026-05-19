<?php
/*
 * Copyright (c) 2023/9/6 sw@stawitech
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToCompany;

class EmployeeAttendanceApprove extends Model
{
    //use BelongsToCompany;

    protected $table = 'employee_attendance_approve';
    protected $primaryKey = 'employee_attendance_approve_id';

    protected $fillable = [
        'employee_attendance_approve_id',
        'employee_id',
        'finger_print_id',
        'date',
        'in_time',
        'out_time',
        'working_hour',
        'approve_working_hour',
        'created_by',
        'updated_by'
    ];
}
