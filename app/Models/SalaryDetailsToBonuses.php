<?php
/*
 * Copyright (c) 2023/9/6 sw@stawitech
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class SalaryDetailsToBonuses extends Model
{
    //use BelongsToCompany;

    protected $table = 'salary_details_to_bonuses';
    protected $primaryKey = 'salary_details_to_bonuses_id';

    protected $fillable = [
        'salary_details_to_bonuses_id',
        'salary_details_id',
        'salary_bonus_id',
        'amount_of_bonus',
        'location_id'
    ];

    public function salaryDetailsBonuses()
    {
        return $this->belongsTo(SalaryDetails::class, 'salary_details_id', 'salary_details_to_bonuses_id');
    }
}
