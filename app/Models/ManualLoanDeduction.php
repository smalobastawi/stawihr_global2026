<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ManualLoanDeduction extends Model
{
    use HasFactory;

    protected $table = 'manual_loan_deductions';

    protected $fillable = [
        'loan_id',
        'employee_id',
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

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
