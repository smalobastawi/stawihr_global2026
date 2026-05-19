<?php
/*
 * Copyright (c) 2023/9/6 sw@stawitech
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToCompany;

class HourlySalary extends Model
{
    //use BelongsToCompany;

    protected $table = 'hourly_salaries';
    protected $primaryKey = 'hourly_salaries_id';

    protected $fillable = [
        'hourly_salaries_id',
        'hourly_grade',
        'hourly_rate'
    ];
}
