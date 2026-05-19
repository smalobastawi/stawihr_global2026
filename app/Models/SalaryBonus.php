<?php
/*
 * Copyright (c) 2023/9/6 sw@stawitech
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class SalaryBonus extends Model
{
    //use BelongsToCompany;

    protected $table = 'salary_bonuses';
    protected $primaryKey = 'salary_bonus_id';
    protected $fillable = [
        'name',
        'amount',
        'employee_id',
        'month',
        'date_issued',
        'salary_bonus_id',
        'location_id',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }
}
