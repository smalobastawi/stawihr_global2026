<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EmployeeOvertime extends Model
{
    //use BelongsToCompany;

    use HasFactory;

    protected $table = 'employee_overtimes';

    protected $fillable = [
        'employee_id',
        'month_year',
        'hours_worked',
        'overtime_rate',
        'total_amount',
        'weekend_hours_totals',
        'weekend_days_totals',
        'public_holiday_days_totals',
        'public_holiday_hours_totals',
        'weekday_days_total',
        'weekday_hours_total',
        'payroll_period_id',
        'payroll_month',
        'created_by',
        'updated_by',
        'status',
        'weekday_amount_calculated',
        'weekend_amount_calculated',
        'holiday_amount_calculated',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
