<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LoanApplication extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'employee_id',
        'loan_type_id',
        'amount_requested',
        'duration_months',
        'reason',
        'approval_comments',
        'status',
        'approved_by',
        'date_approved',
        'amount_approved',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'date_approved' => 'datetime',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function loanType()
    {
        return $this->belongsTo(LoanType::class, 'loan_type_id');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function scopePending($query)
    {
        return $query->where('status', 0);
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 1);
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 2);
    }

    public function scopeSelfService($query)
    {
        if (!employeeInfo()) {
            return $query->whereRaw('1 = 0');
        }
        return $query->where('employee_id', employeeInfo()->employee_id);
    }

    public function loan()
    {
        return $this->hasOne(Loan::class, 'loan_application_id');
    }
}
