<?php
/*
 * Copyright (c) 2023/9/6 sw@stawitech
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToCompany;

class SalaryDetailsToDeduction extends Model
{
    //use BelongsToCompany;

    protected $table = 'salary_details_to_deduction';
    protected $primaryKey = 'salary_details_to_deduction_id';

    protected $fillable = [
        'salary_details_to_deduction_id',
        'salary_details_id',
        'deduction_id',
        'amount_of_deduction',
        'location_id'
    ];
}
