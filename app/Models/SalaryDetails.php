<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Facades\LogBatch;
use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class SalaryDetails extends Model
{
    //use BelongsToCompany;

    use SoftDeletes;

    protected $table = 'salary_details';
    protected $primaryKey = 'salary_details_id';

    protected $fillable = [
        'salary_details_id',
        'employee_id',
        'month_of_salary',
        'basic_salary',
        'total_allowance',
        'total_deduction',
        'total_late',
        'total_late_amount',
        'total_absence',
        'total_absence_amount',
        'overtime_rate',
        'total_over_time_hour',
        'total_overtime_amount',
        'total_present',
        'total_leave',
        'total_working_days',
        'tax',
        'gross_salary',
        'comment',
        'status',
        'created_by',
        'updated_by',
        'payment_method',
        'action',
        'hourly_rate',
        'taxable_salary',
        'per_day_salary',
        'net_salary',
        'working_hour',
        'payroll_no',
        'gross_pay',
        'nssf_no',
        'nhif_no',
        'PAYE_tax',
        'public_holidays_pay',
        'kra_pin',
        'employee_id_no',
        'nhifRate',
        'nssf_amount',
        'no_of_holidays_worked',
        'total_bonuses',
        'total_advances',
        'house_allowance',
        'transport_allowance',
        'banking_allowance',
        'deductible_advance',
        'payroll_claim',
        'pro_rata',
        'department_id',
        'total_nssf',
        'nssf_tier_2',
        'nssf_tier_1',
        'airtime_untaxed',
        'ahl_amount',
        'housing_levy_july',
        'SHIF_amount',
        'location_id'
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function hourlySalaries()
    {
        return $this->belongsTo(HourlySalary::class, 'hourly_salaries_id');
    }

    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    public function designation()
    {
        return $this->belongsTo(Designation::class, 'designation_id');
    }

    public function salaryBonuses()
    {
        return $this->hasMany(SalaryDetailsToBonuses::class, 'salary_details_id');
    }

    public function salaryAdvance()
    {
        return $this->hasMany(SalaryDetailsToAdvances::class, 'salary_details_id');
    }

    public function allowances()
    {
        return $this->hasMany(SalaryDetailsToAllowance::class, 'salary_details_id');
    }

    public function deductions()
    {
        return $this->hasMany(SalaryDetailsToDeduction::class, 'salary_details_id');
    }

    public function getActivitylogOptions(): LogOptions
    {
        LogBatch::startBatch();
        return LogOptions::defaults()
            ->logAll();
    }
}
