<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoanDeduction extends Model
{
    use HasFactory;

    protected $fillable = [
        'loan_id',
        'employee_id',
        'payroll_period_id',
        'amount',
        'deduction_date',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'deduction_date' => 'date',
    ];

    public function loan()
    {
        return $this->belongsTo(Loan::class, 'loan_id');
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function payrollPeriod()
    {
        return $this->belongsTo(\App\Models\Payroll\PayrollPeriod::class, 'payroll_period_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
