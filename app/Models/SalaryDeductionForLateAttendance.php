<?php
/*
 * Copyright (c) 2023/9/6 sw@stawitech
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToCompany;

class SalaryDeductionForLateAttendance extends Model
{
    //use BelongsToCompany;

    protected $table = 'salary_deduction_for_late_attendance';
    protected $primaryKey = 'salary_deduction_for_late_attendance_id';

    protected $fillable = [
        'salary_deduction_for_late_attendance_id',
        'for_days',
        'day_of_salary_deduction',
        'status',
        'location_id'
    ];
}
