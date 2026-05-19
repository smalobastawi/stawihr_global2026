<?php

namespace App\Models\Payroll;

use App\Lib\Enumerations\PayrollStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\HasApprovalWorkflow;
use App\Traits\ProvidesApprovalDetails;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\Facades\LogBatch;
use Spatie\Activitylog\LogOptions;

class PayrollRecord extends Model
{
    use HasFactory, SoftDeletes, HasApprovalWorkflow, ProvidesApprovalDetails, LogsActivity;

    protected $fillable = [
        'employee_payroll_id',
        'payroll_period_id',
        'basic_salary',
        'total_allowances',
        'gross_salary',
        'total_deductions',
        'statutory_deductions',
        'non_statutory_deductions',
        'claim_recoveries',
        'advance_deductions',
        'loan_deductions',
        'paye_tax',
        'nssf_contribution',
        'shif_contribution',
        'housing_levy',
        'pension_contribution',
        'net_salary',
        'payment_method',
        'payment_reference',
        'payment_date',
        'status',
        'processed_by',
        'approved_by',
        'created_by',
        'updated_by',
        'employee_id',
        'approval_status',
        'date_approved',
        'payroll_record_status',
        'industrial_training_levy',
        'nssf_tier1_company_contribution',
        'nssf_tier2_company_contribution',
        'housing_levy_company_contribution',
        'employer_pension_contribution',
        'shif_company_contribution',
        'unpaid_amount',
        'metadata',
        'nssf_tier1_contribution',
        'nssf_tier2_contribution',
    ];

    protected $casts = [
        'basic_salary' => 'decimal:2',
        'total_allowances' => 'decimal:2',
        'gross_salary' => 'decimal:2',
        'total_deductions' => 'decimal:2',
        'statutory_deductions' => 'decimal:2',
        'non_statutory_deductions' => 'decimal:2',
        'claim_recoveries' => 'decimal:2',
        'advance_deductions' => 'decimal:2',
        'loan_deductions' => 'decimal:2',
        'paye_tax' => 'decimal:2',
        'nssf_contribution' => 'decimal:2',
        'shif_contribution' => 'decimal:2',
        'housing_levy' => 'decimal:2',
        'pension_contribution' => 'decimal:2',
        'net_salary' => 'decimal:2',
        'payment_date' => 'date',
        'industrial_training_levy' => 'decimal:2',
        'nssf_tier1_company_contribution' => 'decimal:2',
        'nssf_tier2_company_contribution' => 'decimal:2',
        'housing_levy_company_contribution' => 'decimal:2',
        'employer_pension_contribution' => 'decimal:2',
        'shif_company_contribution' => 'decimal:2',
        'unpaid_amount' => 'decimal:2',
    ];

    // Payroll status

    // Update status constants
    const STATUS_DRAFT = 'draft';
    const STATUS_CALCULATED = 'calculated';
    const STATUS_PENDING_APPROVAL = 'pending_approval';
    const STATUS_REVIEWED = 'reviewed';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';
    const STATUS_PAID = 'paid';
    const STATUS_CANCELLED = 'cancelled';

    const STATUSES = [
        self::STATUS_DRAFT => 'Draft',
        self::STATUS_CALCULATED => 'Calculated',
        self::STATUS_PENDING_APPROVAL => 'Pending Approval',
        self::STATUS_REVIEWED => 'Reviewed',
        self::STATUS_APPROVED => 'Approved',
        self::STATUS_REJECTED => 'Rejected',
        self::STATUS_PAID => 'Paid',
        self::STATUS_CANCELLED => 'Cancelled'
    ];

    /**
     * Relationship with Employee Payroll
     */
    public function employeePayroll()
    {
        return $this->belongsTo(EmployeePayroll::class);
    }

    /**
     * Relationship with Payroll Period
     */
    public function payrollPeriod()
    {
        return $this->belongsTo(PayrollPeriod::class);
    }

    /**
     * Relationship with Employee (through EmployeePayroll)
     */
    public function employee()
    {
        return $this->hasOneThrough(
            \App\Models\Employee::class,
            EmployeePayroll::class,
            'id', // Foreign key on EmployeePayroll table
            'employee_id', // Foreign key on Employee table (changed from 'id')
            'employee_payroll_id', // Local key on PayrollRecord table
            'employee_id' // Local key on EmployeePayroll table
        );
    }

    /**
     * Relationship with Payroll Record Details
     */
    public function details()
    {
        return $this->hasMany(PayrollRecordDetail::class);
    }

    /**
     * Get allowance details
     */
    public function getAllowanceDetails()
    {
        return $this->details()->whereIn('type', ['allowance', 'earning'])->get();
    }

    /**
     * Get deduction details
     */
    public function getDeductionDetails()
    {
        return $this->details()->where('type', 'deduction')->get();
    }

    /**
     * Get company contribution details
     */
    public function getCompanyContributionDetails()
    {
        return $this->details()->where('type', 'company_contribution')->get();
    }

    /**
     * Get statutory deduction details
     */
    public function getStatutoryDeductionDetails()
    {
        return $this->details()->where('type', 'statutory_deduction')->get();
    }

    /**
     * Scope for specific status
     */
    public function scopeStatus($query, $status)
    {
        return $query->where('payroll_record_status', $status);
    }

    /**
     * Scope for paid records
     */
    public function scopePaid($query)
    {
        return $query->where('payroll_record_status', PayrollStatus::PAID);
    }

    /**
     * Scope for unpaid records
     */
    public function scopeUnpaid($query)
    {
        return $query->whereIn('payroll_record_status', [PayrollStatus::DRAFT, PayrollStatus::CALCULATED, PayrollStatus::APPROVED]);
    }

    /**
     * Scope for specific payroll period
     */
    public function scopeForPeriod($query, $periodId)
    {
        return $query->where('payroll_period_id', $periodId);
    }

    /**
     * Check if record can be edited
     */
    public function canBeEdited()
    {
        return in_array($this->payroll_record_status, [PayrollStatus::DRAFT, PayrollStatus::CALCULATED]);
    }

    /**
     * Check if record can be approved
     */
    public function canBeApproved()
    {
        return $this->payroll_record_status === PayrollStatus::CALCULATED;
    }

    /**
     * Check if record can be paid
     */
    public function canBePaid()
    {
        return $this->payroll_record_status === PayrollStatus::APPROVED;
    }

    /**
     * Mark as paid
     */
    public function markAsPaid($paymentReference = null, $paymentDate = null)
    {
        $this->update([
            'status' => self::STATUS_PAID,
            'payment_reference' => $paymentReference,
            'payment_date' => $paymentDate ?? now(),
            'payroll_record_status' => PayrollStatus::PAID,
            'approval_status' => PayrollStatus::PAID
        ]);
    }

    public function pensionScheme()
    {
        return $this->belongsTo(PensionScheme::class);
    }

    public function getApprovalDetails(): array
    {
        $details = [
            'Employee' => $this->employee->fullName() ?? 'N/A',
            'Employee ID' => $this->employee->employee_id ?? 'N/A',
            'Payroll Period' => $this->payrollPeriod->period_name ?? 'N/A',
            'Basic Salary' => number_format($this->basic_salary, 2),
            'Total Allowances' => number_format($this->total_allowances, 2),
            'Gross Salary' => number_format($this->gross_salary, 2),
            'Total Deductions' => number_format($this->total_deductions, 2),
            'Statutory Deductions' => number_format($this->statutory_deductions, 2),
            'Non Statutory Deductions' => number_format($this->non_statutory_deductions, 2),
            'Claim Recoveries' => number_format($this->claim_recoveries, 2),
            'Advance Deductions' => number_format($this->advance_deductions, 2),
            'Loan Deductions' => number_format($this->loan_deductions, 2),
            'PAYE Tax' => number_format($this->paye_tax, 2),
            'NSSF Contribution' => number_format($this->nssf_contribution, 2),
            'SHIF Contribution' => number_format($this->shif_contribution, 2),
            'Housing Levy' => number_format($this->housing_levy, 2),
            'Pension Contribution' => number_format($this->pension_contribution, 2),
            'Net Salary' => number_format($this->net_salary, 2),
            'Industrial Training Levy' => number_format($this->industrial_training_levy, 2),
            'NSSF Tier1 Company Contribution' => number_format($this->nssf_tier1_company_contribution, 2),
            'NSSF Tier2 Company Contribution' => number_format($this->nssf_tier2_company_contribution, 2),
            'Housing Levy Company Contribution' => number_format($this->housing_levy_company_contribution, 2),
            'Employer Pension Contribution' => number_format($this->employer_pension_contribution, 2),
            'SHIF Company Contribution' => number_format($this->shif_company_contribution, 2),
            'Unpaid Amount' => number_format($this->unpaid_amount, 2),
            'Payment Method' => $this->payment_method ?? 'N/A',
            'Status' => $this->payroll_record_status ?? 'N/A'
        ];

        // Add overtime details
        $overtimeDetails = $this->getOvertimeBreakdown();
        if (!empty($overtimeDetails)) {
            $details = array_merge($details, $overtimeDetails);
        }

        return $details;
    }

    /**
     * Get detailed overtime breakdown
     */
    private function getOvertimeBreakdown(): array
    {
        $overtimeDetails = [];

        // Get all earning details that are overtime-related
        $overtimeEarnings = $this->details()
            ->where('type', 'earning')
            ->where(function ($query) {
                $query->where('code', 'like', '%overtime%')
                    ->orWhere('name', 'like', '%overtime%');
            })
            ->get();

        if ($overtimeEarnings->isNotEmpty()) {
            $totalOvertime = 0;
            $overtimeBreakdown = [];

            foreach ($overtimeEarnings as $overtime) {
                $totalOvertime += $overtime->amount;

                // Parse metadata for detailed information
                $metadata = $overtime->metadata ? json_decode($overtime->metadata, true) : [];

                $breakdownInfo = [
                    'Amount' => number_format($overtime->amount, 2),
                    'Units' => $overtime->units ?? 0,
                    'Rate' => $overtime->rate ?? 0,
                ];

                // Add metadata details if available
                if (!empty($metadata)) {
                    if (isset($metadata['days'])) {
                        $breakdownInfo['Days'] = $metadata['days'];
                    }
                    if (isset($metadata['hours'])) {
                        $breakdownInfo['Hours'] = $metadata['hours'];
                    }
                    if (isset($metadata['daily_rate'])) {
                        $breakdownInfo['Daily Rate'] = number_format($metadata['daily_rate'], 2);
                    }
                    if (isset($metadata['hourly_rate'])) {
                        $breakdownInfo['Hourly Rate'] = number_format($metadata['hourly_rate'], 2);
                    }
                    if (isset($metadata['days_amount'])) {
                        $breakdownInfo['Days Amount'] = number_format($metadata['days_amount'], 2);
                    }
                    if (isset($metadata['hours_amount'])) {
                        $breakdownInfo['Hours Amount'] = number_format($metadata['hours_amount'], 2);
                    }
                }

                $overtimeBreakdown[$overtime->name] = $breakdownInfo;
            }

            $overtimeDetails['Total Overtime Earnings'] = number_format($totalOvertime, 2);

            // Add detailed breakdown if there are multiple overtime types
            if (count($overtimeBreakdown) > 1) {
                foreach ($overtimeBreakdown as $type => $breakdown) {
                    $overtimeDetails["Overtime - {$type}"] = implode(', ', array_map(
                        fn($key, $value) => "{$key}: {$value}",
                        array_keys($breakdown),
                        $breakdown
                    ));
                }
            } else {
                // Single overtime type - flatten the details
                foreach ($overtimeBreakdown as $type => $breakdown) {
                    $overtimeDetails["Overtime Details"] = "{$type}: " . implode(', ', array_map(
                        fn($key, $value) => "{$key}: {$value}",
                        array_keys($breakdown),
                        $breakdown
                    ));
                }
            }
        }

        return $overtimeDetails;
    }
    public function getActivitylogOptions(): LogOptions
    {
        LogBatch::startBatch();
        return LogOptions::defaults()
            ->logAll();
    }
}