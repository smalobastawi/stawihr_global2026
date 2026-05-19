<?php
/*
 * Copyright (c) 2023/9/6 sw@stawitech
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToCompany;

class SalaryBonusTypes extends Model
{
    //use BelongsToCompany;

    protected $table = 'salary_bonus_types';
    protected $primaryKey = 'bonus_type_id';
    protected $fillable = [
        'bonus_type_id',
        'bonus_type_name',
        'bonus_type_limit',
        'status',
    ];
}
