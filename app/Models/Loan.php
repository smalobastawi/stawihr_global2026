<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToCompany;
use Spatie\Activitylog\Facades\LogBatch;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Support\Facades\Auth;
use App\Lib\Enumerations\ApprovalStatus;
use App\Lib\Enumerations\GeneralStatus;
use App\Traits\HasApprovalWorkflow;
use App\Traits\ProvidesApprovalDetails;

class Loan extends Model
{
    use HasFactory, LogsActivity, HasApprovalWorkflow, ProvidesApprovalDetails;

    protected $fillable = [
        'employee_id',
        'loan_type_id',
        'amount',
        'interest_rate',
        'duration_months',
        'monthly_installment',
        'total_repayable',
        'balance',
        'start_date',
        'end_date',
        'purpose',
        'justification',
        'status',
        'approval_status',
        'approved_by',
        'approved_at',
        'approval_notes',
        'date_approved',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'approved_at' => 'datetime',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        LogBatch::startBatch();
        return LogOptions::defaults()
            ->logAll();
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function loanType()
    {
        return $this->belongsTo(LoanType::class, 'loan_type_id');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function deductions()
    {
        return $this->hasMany(LoanDeduction::class, 'loan_id');
    }

    public function manualDeductions()
    {
        return $this->hasMany(ManualLoanDeduction::class, 'loan_id');
    }

    public function approve($approver, string $notes = null)
    {
        $this->update([
            'approved_by' => is_object($approver) ? $approver->id : $approver,
            'approved_at' => now(),
            'approval_notes' => $notes,
            'status' => GeneralStatus::ACTIVE,
            'approval_status' => ApprovalStatus::APPROVED,
            'date_approved' => now(),
        ]);
        return $this;
    }

    public function reject($rejectorId, string $notes = null)
    {
        $this->update([
            'approved_by' => $rejectorId,
            'approved_at' => now(),
            'approval_notes' => $notes,
            'status' => GeneralStatus::INACTIVE,
            'approval_status' => ApprovalStatus::REJECTED,
        ]);
        return $this;
    }

    public function suspend($suspenderId, string $notes = null)
    {
        $this->update([
            'approved_by' => $suspenderId,
            'approved_at' => now(),
            'approval_notes' => $notes,
            'status' => GeneralStatus::SUSPENDED,
            'approval_status' => ApprovalStatus::CANCELLED,
        ]);
        return $this;
    }

    public function scopeApproved($query)
    {
        return $query->where('approval_status', ApprovalStatus::APPROVED);
    }

    public function scopePendingApproval($query)
    {
        return $query->where('approval_status', ApprovalStatus::PENDING);
    }

    public function scopeRejected($query)
    {
        return $query->where('approval_status', ApprovalStatus::REJECTED);
    }

    public function scopeActive($query)
    {
        return $query->where('status', GeneralStatus::ACTIVE)
                   ->where('balance', '>', 0);
    }

    public function getApprovalDetails(): array
    {
        return [
            'Employee' => $this->employee->full_name ?? 'N/A',
            'Loan Type' => $this->loanType->name ?? 'N/A',
            'Amount' => number_format($this->amount, 2),
            'Duration' => $this->duration_months . ' months',
            'Monthly Installment' => number_format($this->monthly_installment, 2),
        ];
    }
}
