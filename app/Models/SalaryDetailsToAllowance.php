<?php
/*
 * Copyright (c) 2023/9/6 sw@stawitech
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToCompany;

class SalaryDetailsToAllowance extends Model
{
    //use BelongsToCompany;

    protected $table = 'salary_details_to_allowance';
    protected $primaryKey = 'salary_details_to_allowance_id';

    protected $fillable = [
        'salary_details_to_allowance_id',
        'salary_details_id',
        'allowance_id',
        'amount_of_allowance',
        'location_id'
    ];
}
