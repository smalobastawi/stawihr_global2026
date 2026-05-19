<?php
/*
 * Copyright (c) 2023/9/6 sw@stawitech
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class EmployeeEducationQualification extends Model
{
    //use BelongsToCompany;

    use SoftDeletes;
    protected $table = 'employee_education_qualification';
    protected $primaryKey = 'employee_education_qualification_id';
    protected $fillable = [
        'employee_education_qualification_id',
        'employee_id',
        'institute',
        'board_university',
        'degree',
        'passing_year',
        'result',
        'cgpa',
        'certificate',
        'status',
        'location_id'
    ];

    protected $dates = [
        'deleted_at'
    ];
}
