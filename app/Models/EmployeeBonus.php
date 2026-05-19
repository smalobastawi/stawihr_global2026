<?php
/*
 * Copyright (c) 2023/9/6 sw@stawitech
 */

namespace App\Models;

use App\Traits\WithBranchPermissions;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class EmployeeBonus extends Model
{
    //use BelongsToCompany;

    use softDeletes;
    use WithBranchPermissions;
    protected $table = 'employee_bonus';
    protected $primaryKey = 'employee_bonus_id';

    protected $fillable = [
        'employee_bonus_id',
        'bonus_setting_id',
        'employee_id',
        'month',
        'gross_salary',
        'basic_salary',
        'bonus_amount',
        'tax'
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function festivalName()
    {
        return $this->belongsTo(BonusSetting::class, 'bonus_setting_id');
    }

    public function hourlySalaries()
    {
        return $this->belongsTo(HourlySalary::class, 'hourly_salaries_id');
    }
}
