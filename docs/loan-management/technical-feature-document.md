# Loan Management Feature - Technical Specification Document

## 1. Overview

### 1.1 Purpose
The Loan Management module extends the existing payroll system to handle employee loans with flexible repayment terms, topup capabilities, and both automatic and manual deduction options.

### 1.2 Scope
- Employee loan applications with approval workflow
- HR direct loan creation with auto-approval
- Loan topup functionality with consolidated repayment
- Automatic payroll deduction integration
- Manual deduction entry option
- Comprehensive dashboard and reporting

### 1.3 Key Differentiators from Salary Advances
| Feature | Salary Advance | Loan Management |
|---------|---------------|-----------------|
| Repayment Period | Typically 1 month | 1-24 months or more |
| Interest | Usually none | Configurable (can be zero) |
| Topup Capability | No | Yes |
| Deduction Mode | Fixed automatic | Automatic or Manual |
| Amount Range | Small (e.g., 50% of salary) | Larger amounts |
| Amortization | Simple | Full schedule |

---

## 2. Database Schema

### 2.1 New Tables

#### 2.1.1 `loan_types`
Stores configurable loan categories.

```sql
CREATE TABLE loan_types (
    loan_type_id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    max_amount DECIMAL(15,2),
    max_amount_type ENUM('fixed', 'percentage_of_basic', 'percentage_of_gross'),
    max_percentage DECIMAL(5,2),
    max_repayment_months INT,
    interest_rate DECIMAL(5,2) DEFAULT 0.00,
    interest_type ENUM('flat', 'reducing_balance') DEFAULT 'flat',
    allow_multiple_active_loans BOOLEAN DEFAULT FALSE,
    requires_approval BOOLEAN DEFAULT TRUE,
    auto_deduct_default BOOLEAN DEFAULT TRUE,
    company_id INT,
    is_active BOOLEAN DEFAULT TRUE,
    created_by INT,
    updated_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL
);
```

#### 2.1.2 `loan_applications`
Tracks employee loan applications.

```sql
CREATE TABLE loan_applications (
    loan_application_id INT PRIMARY KEY AUTO_INCREMENT,
    employee_id INT NOT NULL,
    loan_type_id INT NOT NULL,
    application_type ENUM('new_loan', 'topup') DEFAULT 'new_loan',
    parent_loan_id INT NULL,
    amount_requested DECIMAL(15,2) NOT NULL,
    purpose TEXT,
    repayment_start_date DATE NOT NULL,
    proposed_monthly_installment DECIMAL(15,2) NOT NULL,
    proposed_repayment_months INT NOT NULL,
    requested_by INT,
    application_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('draft', 'submitted', 'under_review', 'approved', 'rejected', 'cancelled') DEFAULT 'draft',
    approval_status VARCHAR(50),
    date_approved TIMESTAMP NULL,
    approved_by INT,
    approval_comments TEXT,
    processed_by INT,
    processed_at TIMESTAMP NULL,
    rejection_reason TEXT,
    company_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL,
    
    FOREIGN KEY (employee_id) REFERENCES employee(employee_id),
    FOREIGN KEY (loan_type_id) REFERENCES loan_types(loan_type_id),
    FOREIGN KEY (parent_loan_id) REFERENCES loans(loan_id),
    FOREIGN KEY (requested_by) REFERENCES user(user_id),
    FOREIGN KEY (approved_by) REFERENCES user(user_id),
    FOREIGN KEY (processed_by) REFERENCES user(user_id)
);
```

#### 2.1.3 `loans`
Master loan records after approval.

```sql
CREATE TABLE loans (
    loan_id INT PRIMARY KEY AUTO_INCREMENT,
    loan_application_id INT,
    employee_id INT NOT NULL,
    loan_type_id INT NOT NULL,
    loan_reference VARCHAR(50) UNIQUE,
    original_amount DECIMAL(15,2) NOT NULL,
    interest_rate DECIMAL(5,2) DEFAULT 0.00,
    interest_type ENUM('flat', 'reducing_balance') DEFAULT 'flat',
    total_interest DECIMAL(15,2) DEFAULT 0.00,
    total_amount DECIMAL(15,2) NOT NULL,
    amount_paid DECIMAL(15,2) DEFAULT 0.00,
    outstanding_balance DECIMAL(15,2) NOT NULL,
    monthly_installment DECIMAL(15,2) NOT NULL,
    repayment_start_date DATE NOT NULL,
    repayment_end_date DATE,
    total_months INT NOT NULL,
    remaining_months INT,
    months_paid INT DEFAULT 0,
    status ENUM('pending_disbursement', 'active', 'paid_off', 'suspended', 'written_off', 'cancelled') DEFAULT 'pending_disbursement',
    deduction_mode ENUM('automatic', 'manual') DEFAULT 'automatic',
    is_topup BOOLEAN DEFAULT FALSE,
    parent_loan_id INT NULL,
    consolidated_from_loan_id INT NULL,
    topup_amount DECIMAL(15,2) DEFAULT 0.00,
    previous_outstanding DECIMAL(15,2) DEFAULT 0.00,
    disbursement_date DATE NULL,
    disbursement_method ENUM('bank_transfer', 'cash', 'cheque', 'payroll') DEFAULT 'bank_transfer',
    bank_account_id INT,
    notes TEXT,
    company_id INT,
    created_by INT,
    updated_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL,
    
    FOREIGN KEY (loan_application_id) REFERENCES loan_applications(loan_application_id),
    FOREIGN KEY (employee_id) REFERENCES employee(employee_id),
    FOREIGN KEY (loan_type_id) REFERENCES loan_types(loan_type_id),
    FOREIGN KEY (parent_loan_id) REFERENCES loans(loan_id),
    FOREIGN KEY (consolidated_from_loan_id) REFERENCES loans(loan_id),
    FOREIGN KEY (bank_account_id) REFERENCES employee_bank_accounts(id)
);
```

#### 2.1.4 `loan_repayment_schedule`
Detailed amortization schedule.

```sql
CREATE TABLE loan_repayment_schedule (
    schedule_id INT PRIMARY KEY AUTO_INCREMENT,
    loan_id INT NOT NULL,
    installment_number INT NOT NULL,
    due_date DATE NOT NULL,
    beginning_balance DECIMAL(15,2) NOT NULL,
    principal_amount DECIMAL(15,2) NOT NULL,
    interest_amount DECIMAL(15,2) DEFAULT 0.00,
    total_installment DECIMAL(15,2) NOT NULL,
    ending_balance DECIMAL(15,2) NOT NULL,
    status ENUM('pending', 'paid', 'partially_paid', 'overdue', 'waived') DEFAULT 'pending',
    amount_paid DECIMAL(15,2) DEFAULT 0.00,
    payroll_record_id INT NULL,
    paid_date DATE NULL,
    company_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (loan_id) REFERENCES loans(loan_id),
    FOREIGN KEY (payroll_record_id) REFERENCES payroll_records(id),
    UNIQUE KEY unique_loan_installment (loan_id, installment_number)
);
```

#### 2.1.5 `loan_payments`
Actual payment records.

```sql
CREATE TABLE loan_payments (
    payment_id INT PRIMARY KEY AUTO_INCREMENT,
    loan_id INT NOT NULL,
    schedule_id INT,
    payroll_record_id INT,
    payment_type ENUM('payroll_deduction', 'manual_entry', 'lump_sum', 'topup_adjustment') DEFAULT 'payroll_deduction',
    amount DECIMAL(15,2) NOT NULL,
    principal_paid DECIMAL(15,2) NOT NULL,
    interest_paid DECIMAL(15,2) DEFAULT 0.00,
    payment_date DATE NOT NULL,
    payment_method ENUM('payroll_deduction', 'bank_transfer', 'cash', 'cheque') DEFAULT 'payroll_deduction',
    reference_number VARCHAR(100),
    notes TEXT,
    processed_by INT,
    company_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (loan_id) REFERENCES loans(loan_id),
    FOREIGN KEY (schedule_id) REFERENCES loan_repayment_schedule(schedule_id),
    FOREIGN KEY (payroll_record_id) REFERENCES payroll_records(id),
    FOREIGN KEY (processed_by) REFERENCES user(user_id)
);
```

#### 2.1.6 `loan_manual_deductions`
For manual deduction entry in payroll.

```sql
CREATE TABLE loan_manual_deductions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    loan_id INT NOT NULL,
    payroll_period_id INT NOT NULL,
    employee_id INT NOT NULL,
    amount DECIMAL(15,2) NOT NULL,
    notes TEXT,
    status ENUM('pending', 'processed', 'cancelled') DEFAULT 'pending',
    processed_by INT,
    created_by INT,
    company_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (loan_id) REFERENCES loans(loan_id),
    FOREIGN KEY (payroll_period_id) REFERENCES payroll_periods(id),
    FOREIGN KEY (employee_id) REFERENCES employee(employee_id),
    UNIQUE KEY unique_period_loan (payroll_period_id, loan_id)
);
```

---

## 3. Models Structure

### 3.1 LoanType Model (`app/Models/Loan/LoanType.php`)
```php
<?php

namespace App\Models\Loan;

use App\Models\Company;
use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LoanType extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $table = 'loan_types';
    protected $primaryKey = 'loan_type_id';

    protected $fillable = [
        'name',
        'description',
        'max_amount',
        'max_amount_type',
        'max_percentage',
        'max_repayment_months',
        'interest_rate',
        'interest_type',
        'allow_multiple_active_loans',
        'requires_approval',
        'auto_deduct_default',
        'company_id',
        'is_active',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'max_amount' => 'decimal:2',
        'interest_rate' => 'decimal:2',
        'allow_multiple_active_loans' => 'boolean',
        'requires_approval' => 'boolean',
        'auto_deduct_default' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function loans()
    {
        return $this->hasMany(Loan::class, 'loan_type_id');
    }

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function calculateMaxAmount($basicSalary, $grossSalary)
    {
        return match($this->max_amount_type) {
            'fixed' => $this->max_amount,
            'percentage_of_basic' => ($this->max_percentage / 100) * $basicSalary,
            'percentage_of_gross' => ($this->max_percentage / 100) * $grossSalary,
            default => $this->max_amount
        };
    }
}
```

### 3.2 LoanApplication Model (`app/Models/Loan/LoanApplication.php`)
```php
<?php

namespace App\Models\Loan;

use App\Models\Employee;
use App\Models\User;
use App\Traits\LogsActivity;
use App\Traits\HasApprovalWorkflow;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LoanApplication extends Model
{
    use HasFactory, SoftDeletes, LogsActivity, HasApprovalWorkflow;

    protected $table = 'loan_applications';
    protected $primaryKey = 'loan_application_id';

    protected $fillable = [
        'employee_id',
        'loan_type_id',
        'application_type',
        'parent_loan_id',
        'amount_requested',
        'purpose',
        'repayment_start_date',
        'proposed_monthly_installment',
        'proposed_repayment_months',
        'requested_by',
        'status',
        'company_id'
    ];

    protected $casts = [
        'amount_requested' => 'decimal:2',
        'proposed_monthly_installment' => 'decimal:2',
        'repayment_start_date' => 'date',
        'application_date' => 'datetime',
        'date_approved' => 'datetime',
        'processed_at' => 'datetime',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function loanType()
    {
        return $this->belongsTo(LoanType::class, 'loan_type_id');
    }

    public function parentLoan()
    {
        return $this->belongsTo(Loan::class, 'parent_loan_id');
    }

    public function loan()
    {
        return $this->hasOne(Loan::class, 'loan_application_id');
    }

    public function requestedBy()
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function scopePending($query)
    {
        return $query->whereIn('status', ['submitted', 'under_review']);
    }

    public function scopeTopups($query)
    {
        return $query->where('application_type', 'topup');
    }

    public function isTopup()
    {
        return $this->application_type === 'topup';
    }

    public function approve($approverId, $comments = null)
    {
        $this->update([
            'status' => 'approved',
            'date_approved' => now(),
            'approved_by' => $approverId,
            'approval_comments' => $comments
        ]);
    }

    public function reject($approverId, $reason)
    {
        $this->update([
            'status' => 'rejected',
            'date_approved' => now(),
            'approved_by' => $approverId,
            'rejection_reason' => $reason
        ]);
    }

    public function canBeCancelled()
    {
        return in_array($this->status, ['draft', 'submitted']);
    }
}
```

### 3.3 Loan Model (`app/Models/Loan/Loan.php`)
```php
<?php

namespace App\Models\Loan;

use App\Models\Employee;
use App\Models\User;
use App\Models\Payroll\PayrollRecord;
use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Loan extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $table = 'loans';
    protected $primaryKey = 'loan_id';

    protected $fillable = [
        'loan_application_id',
        'employee_id',
        'loan_type_id',
        'loan_reference',
        'original_amount',
        'interest_rate',
        'interest_type',
        'total_interest',
        'total_amount',
        'outstanding_balance',
        'monthly_installment',
        'repayment_start_date',
        'repayment_end_date',
        'total_months',
        'remaining_months',
        'status',
        'deduction_mode',
        'is_topup',
        'parent_loan_id',
        'consolidated_from_loan_id',
        'topup_amount',
        'previous_outstanding',
        'disbursement_date',
        'disbursement_method',
        'bank_account_id',
        'notes',
        'company_id',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'original_amount' => 'decimal:2',
        'interest_rate' => 'decimal:2',
        'total_interest' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'amount_paid' => 'decimal:2',
        'outstanding_balance' => 'decimal:2',
        'monthly_installment' => 'decimal:2',
        'topup_amount' => 'decimal:2',
        'previous_outstanding' => 'decimal:2',
        'repayment_start_date' => 'date',
        'repayment_end_date' => 'date',
        'disbursement_date' => 'date',
        'is_topup' => 'boolean',
        'deduction_mode' => 'string',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function loanType()
    {
        return $this->belongsTo(LoanType::class, 'loan_type_id');
    }

    public function application()
    {
        return $this->belongsTo(LoanApplication::class, 'loan_application_id');
    }

    public function repaymentSchedule()
    {
        return $this->hasMany(LoanRepaymentSchedule::class, 'loan_id')
            ->orderBy('installment_number');
    }

    public function payments()
    {
        return $this->hasMany(LoanPayment::class, 'loan_id');
    }

    public function parentLoan()
    {
        return $this->belongsTo(self::class, 'parent_loan_id');
    }

    public function childLoans()
    {
        return $this->hasMany(self::class, 'parent_loan_id');
    }

    public function consolidatedFrom()
    {
        return $this->belongsTo(self::class, 'consolidated_from_loan_id');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopePendingDisbursement($query)
    {
        return $query->where('status', 'pending_disbursement');
    }

    public function scopeForPayrollPeriod($query, $startDate, $endDate)
    {
        return $query->where('status', 'active')
            ->where('deduction_mode', 'automatic')
            ->where('repayment_start_date', '<=', $endDate)
            ->where(function($q) {
                $q->where('remaining_months', '>', 0)
                  ->orWhereNull('remaining_months');
            });
    }

    public function isActive()
    {
        return $this->status === 'active';
    }

    public function isFullyPaid()
    {
        return $this->status === 'paid_off' || $this->outstanding_balance <= 0;
    }

    public function getNextDueInstallment()
    {
        return $this->repaymentSchedule()
            ->whereIn('status', ['pending', 'partially_paid'])
            ->first();
    }

    public function getCurrentMonthInstallment($payrollDate)
    {
        return $this->repaymentSchedule()
            ->whereMonth('due_date', $payrollDate->month)
            ->whereYear('due_date', $payrollDate->year)
            ->first();
    }

    public function getPaymentProgress()
    {
        if ($this->total_amount == 0) return 100;
        return round(($this->amount_paid / $this->total_amount) * 100, 2);
    }

    public function markAsDisbursed($disbursementDate = null)
    {
        $this->update([
            'status' => 'active',
            'disbursement_date' => $disbursementDate ?? now(),
        ]);
    }

    public function recordPayment($amount, $payrollRecordId = null, $paymentType = 'payroll_deduction')
    {
        $nextInstallment = $this->getNextDueInstallment();
        
        if (!$nextInstallment) return false;

        $interestPaid = min($amount, $nextInstallment->interest_amount - $nextInstallment->amount_paid);
        $principalPaid = $amount - $interestPaid;

        LoanPayment::create([
            'loan_id' => $this->loan_id,
            'schedule_id' => $nextInstallment->schedule_id,
            'payroll_record_id' => $payrollRecordId,
            'payment_type' => $paymentType,
            'amount' => $amount,
            'principal_paid' => $principalPaid,
            'interest_paid' => $interestPaid,
            'payment_date' => now(),
        ]);

        // Update installment status
        $newAmountPaid = $nextInstallment->amount_paid + $amount;
        $status = $newAmountPaid >= $nextInstallment->total_installment ? 'paid' : 'partially_paid';
        
        $nextInstallment->update([
            'amount_paid' => $newAmountPaid,
            'status' => $status,
            'paid_date' => $status === 'paid' ? now() : null,
            'payroll_record_id' => $payrollRecordId
        ]);

        // Update loan balance
        $this->increment('amount_paid', $amount);
        $this->decrement('outstanding_balance', $amount);
        
        if ($this->outstanding_balance <= 0) {
            $this->update([
                'status' => 'paid_off',
                'remaining_months' => 0,
                'outstanding_balance' => 0
            ]);
        } else {
            $this->decrement('remaining_months');
            $this->increment('months_paid');
        }

        return true;
    }

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($loan) {
            if (empty($loan->loan_reference)) {
                $loan->loan_reference = self::generateReference();
            }
        });
    }

    protected static function generateReference()
    {
        $prefix = 'LN';
        $year = date('Y');
        $lastLoan = self::whereYear('created_at', $year)
            ->orderBy('loan_id', 'desc')
            ->first();
        
        $sequence = $lastLoan ? intval(substr($lastLoan->loan_reference, -6)) + 1 : 1;
        return $prefix . $year . str_pad($sequence, 6, '0', STR_PAD_LEFT);
    }
}
```

### 3.4 LoanRepaymentSchedule Model (`app/Models/Loan/LoanRepaymentSchedule.php`)
```php
<?php

namespace App\Models\Loan;

use App\Models\Payroll\PayrollRecord;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoanRepaymentSchedule extends Model
{
    use HasFactory;

    protected $table = 'loan_repayment_schedule';
    protected $primaryKey = 'schedule_id';

    protected $fillable = [
        'loan_id',
        'installment_number',
        'due_date',
        'beginning_balance',
        'principal_amount',
        'interest_amount',
        'total_installment',
        'ending_balance',
        'status',
        'amount_paid',
        'payroll_record_id',
        'paid_date',
        'company_id'
    ];

    protected $casts = [
        'beginning_balance' => 'decimal:2',
        'principal_amount' => 'decimal:2',
        'interest_amount' => 'decimal:2',
        'total_installment' => 'decimal:2',
        'ending_balance' => 'decimal:2',
        'amount_paid' => 'decimal:2',
        'due_date' => 'date',
        'paid_date' => 'date',
    ];

    public function loan()
    {
        return $this->belongsTo(Loan::class, 'loan_id');
    }

    public function payrollRecord()
    {
        return $this->belongsTo(PayrollRecord::class, 'payroll_record_id');
    }
}
```

### 3.5 LoanPayment Model (`app/Models/Loan/LoanPayment.php`)
```php
<?php

namespace App\Models\Loan;

use App\Models\User;
use App\Models\Payroll\PayrollRecord;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoanPayment extends Model
{
    use HasFactory;

    protected $table = 'loan_payments';
    protected $primaryKey = 'payment_id';

    protected $fillable = [
        'loan_id',
        'schedule_id',
        'payroll_record_id',
        'payment_type',
        'amount',
        'principal_paid',
        'interest_paid',
        'payment_date',
        'payment_method',
        'reference_number',
        'notes',
        'processed_by',
        'company_id'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'principal_paid' => 'decimal:2',
        'interest_paid' => 'decimal:2',
        'payment_date' => 'date',
    ];

    public function loan()
    {
        return $this->belongsTo(Loan::class, 'loan_id');
    }

    public function schedule()
    {
        return $this->belongsTo(LoanRepaymentSchedule::class, 'schedule_id');
    }

    public function payrollRecord()
    {
        return $this->belongsTo(PayrollRecord::class, 'payroll_record_id');
    }

    public function processedBy()
    {
        return $this->belongsTo(User::class, 'processed_by');
    }
}
```

### 3.6 LoanManualDeduction Model (`app/Models/Loan/LoanManualDeduction.php`)
```php
<?php

namespace App\Models\Loan;

use App\Models\Employee;
use App\Models\Payroll\PayrollPeriod;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoanManualDeduction extends Model
{
    use HasFactory;

    protected $table = 'loan_manual_deductions';

    protected $fillable = [
        'loan_id',
        'payroll_period_id',
        'employee_id',
        'amount',
        'notes',
        'status',
        'processed_by',
        'created_by',
        'company_id'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public function loan()
    {
        return $this->belongsTo(Loan::class, 'loan_id');
    }

    public function payrollPeriod()
    {
        return $this->belongsTo(PayrollPeriod::class, 'payroll_period_id');
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function processedBy()
    {
        return $this->belongsTo(User::class, 'processed_by');
    }
}
```

---

## 4. Services

### 4.1 LoanCalculationService (`app/Services/Loan/LoanCalculationService.php`)
```php
<?php

namespace App\Services\Loan;

use App\Models\Loan\Loan;
use App\Models\Loan\LoanType;
use App\Models\Loan\LoanApplication;
use App\Models\Loan\LoanRepaymentSchedule;
use App\Models\Payroll\EmployeePayroll;
use Carbon\Carbon;

class LoanCalculationService
{
    /**
     * Calculate maximum loan amount based on type and salary
     */
    public function calculateMaxAmount(LoanType $loanType, EmployeePayroll $payroll): float
    {
        $basicSalary = $payroll->basic_salary ?? 0;
        $grossSalary = $basicSalary + $payroll->getTotalAllowances();
        
        return $loanType->calculateMaxAmount($basicSalary, $grossSalary);
    }

    /**
     * Generate loan repayment schedule
     */
    public function generateRepaymentSchedule(
        float $principal,
        float $interestRate,
        string $interestType,
        int $months,
        Carbon $startDate,
        ?float $fixedInstallment = null
    ): array {
        $schedule = [];
        $balance = $principal;
        $totalInterest = 0;
        
        if ($interestType === 'flat') {
            // Flat interest calculation
            $monthlyInterest = ($principal * ($interestRate / 100)) / 12;
            $totalInterestAmount = $monthlyInterest * $months;
            
            if ($fixedInstallment) {
                $monthlyPrincipal = $fixedInstallment - $monthlyInterest;
            } else {
                $monthlyPrincipal = $principal / $months;
            }
            
            for ($i = 1; $i <= $months; $i++) {
                $dueDate = $startDate->copy()->addMonths($i - 1);
                $interest = $monthlyInterest;
                $principalAmount = min($monthlyPrincipal, $balance);
                $installment = $fixedInstallment ?? ($principalAmount + $interest);
                
                $schedule[] = [
                    'installment_number' => $i,
                    'due_date' => $dueDate,
                    'beginning_balance' => $balance,
                    'principal_amount' => $principalAmount,
                    'interest_amount' => $interest,
                    'total_installment' => $installment,
                    'ending_balance' => max(0, $balance - $principalAmount)
                ];
                
                $balance -= $principalAmount;
                $totalInterest += $interest;
            }
        } else {
            // Reducing balance interest calculation
            $monthlyRate = ($interestRate / 100) / 12;
            
            if ($fixedInstallment && $monthlyRate > 0) {
                // EMI formula for reducing balance
                $installment = $fixedInstallment;
            } elseif ($monthlyRate > 0) {
                $installment = ($principal * $monthlyRate * pow(1 + $monthlyRate, $months)) / 
                              (pow(1 + $monthlyRate, $months) - 1);
            } else {
                $installment = $principal / $months;
            }
            
            for ($i = 1; $i <= $months; $i++) {
                $dueDate = $startDate->copy()->addMonths($i - 1);
                $interest = $balance * $monthlyRate;
                $principalAmount = min($installment - $interest, $balance);
                
                if ($i == $months) {
                    $principalAmount = $balance; // Final adjustment
                }
                
                $totalInstallment = $principalAmount + $interest;
                
                $schedule[] = [
                    'installment_number' => $i,
                    'due_date' => $dueDate,
                    'beginning_balance' => $balance,
                    'principal_amount' => $principalAmount,
                    'interest_amount' => $interest,
                    'total_installment' => $totalInstallment,
                    'ending_balance' => max(0, $balance - $principalAmount)
                ];
                
                $balance -= $principalAmount;
                $totalInterest += $interest;
            }
        }
        
        return [
            'schedule' => $schedule,
            'total_interest' => $totalInterest,
            'total_amount' => $principal + $totalInterest
        ];
    }

    /**
     * Calculate topup loan with consolidation
     */
    public function calculateTopupLoan(
        Loan $existingLoan,
        float $topupAmount,
        int $newRepaymentMonths,
        float $interestRate,
        string $interestType
    ): array {
        $remainingBalance = $existingLoan->outstanding_balance;
        $totalNewPrincipal = $remainingBalance + $topupAmount;
        
        $scheduleData = $this->generateRepaymentSchedule(
            $totalNewPrincipal,
            $interestRate,
            $interestType,
            $newRepaymentMonths,
            Carbon::parse($existingLoan->repayment_start_date)->addMonths($existingLoan->months_paid + 1)
        );
        
        return [
            'previous_loan_id' => $existingLoan->loan_id,
            'previous_outstanding' => $remainingBalance,
            'topup_amount' => $topupAmount,
            'new_principal' => $totalNewPrincipal,
            'new_monthly_installment' => $scheduleData['schedule'][0]['total_installment'] ?? 0,
            'new_total_months' => $newRepaymentMonths,
            'new_total_interest' => $scheduleData['total_interest'],
            'new_total_amount' => $scheduleData['total_amount'],
            'new_schedule' => $scheduleData['schedule']
        ];
    }

    /**
     * Calculate loan eligibility
     */
    public function checkEligibility(
        int $employeeId,
        LoanType $loanType,
        float $requestedAmount
    ): array {
        $payroll = EmployeePayroll::where('employee_id', $employeeId)->first();
        
        if (!$payroll) {
            return [
                'eligible' => false,
                'reason' => 'No active payroll profile found'
            ];
        }
        
        // Check max amount eligibility
        $maxAmount = $this->calculateMaxAmount($loanType, $payroll);
        if ($requestedAmount > $maxAmount) {
            return [
                'eligible' => false,
                'reason' => "Requested amount exceeds maximum allowed ({$maxAmount})"
            ];
        }
        
        // Check if employee has active loans of this type
        if (!$loanType->allow_multiple_active_loans) {
            $activeLoanCount = Loan::where('employee_id', $employeeId)
                ->where('loan_type_id', $loanType->loan_type_id)
                ->where('status', 'active')
                ->count();
            
            if ($activeLoanCount > 0) {
                return [
                    'eligible' => false,
                    'reason' => 'Employee already has an active loan of this type'
                ];
            }
        }
        
        return [
            'eligible' => true,
            'max_amount' => $maxAmount,
            'payroll' => $payroll
        ];
    }

    /**
     * Get monthly loan deductions for payroll
     */
    public function getMonthlyDeductions(int $employeeId, Carbon $payrollDate): array
    {
        $loans = Loan::where('employee_id', $employeeId)
            ->where('status', 'active')
            ->where('deduction_mode', 'automatic')
            ->get();
        
        $deductions = [];
        
        foreach ($loans as $loan) {
            $installment = $loan->getCurrentMonthInstallment($payrollDate);
            
            if ($installment && $installment->status !== 'paid') {
                $remainingAmount = $installment->total_installment - $installment->amount_paid;
                
                $deductions[] = [
                    'loan_id' => $loan->loan_id,
                    'loan_reference' => $loan->loan_reference,
                    'description' => "Loan Repayment - {$loan->loan_reference}",
                    'amount' => $remainingAmount,
                    'schedule_id' => $installment->schedule_id,
                    'is_partial' => $installment->status === 'partially_paid'
                ];
            }
        }
        
        return $deductions;
    }
}
```

### 4.2 LoanService (`app/Services/Loan/LoanService.php`)
```php
<?php

namespace App\Services\Loan;

use App\Models\Loan\Loan;
use App\Models\Loan\LoanApplication;
use App\Models\Loan\LoanRepaymentSchedule;
use App\Models\Loan\LoanType;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class LoanService
{
    protected $calculationService;
    
    public function __construct(LoanCalculationService $calculationService)
    {
        $this->calculationService = $calculationService;
    }
    
    /**
     * Create a new loan from approved application
     */
    public function createLoanFromApplication(LoanApplication $application, array $disbursementData = []): Loan
    {
        return DB::transaction(function () use ($application, $disbursementData) {
            $loanType = $application->loanType;
            
            // Calculate repayment schedule
            $scheduleData = $this->calculationService->generateRepaymentSchedule(
                $application->amount_requested,
                $loanType->interest_rate,
                $loanType->interest_type,
                $application->proposed_repayment_months,
                Carbon::parse($application->repayment_start_date),
                $application->proposed_monthly_installment
            );
            
            // Create loan record
            $loan = Loan::create([
                'loan_application_id' => $application->loan_application_id,
                'employee_id' => $application->employee_id,
                'loan_type_id' => $application->loan_type_id,
                'original_amount' => $application->amount_requested,
                'interest_rate' => $loanType->interest_rate,
                'interest_type' => $loanType->interest_type,
                'total_interest' => $scheduleData['total_interest'],
                'total_amount' => $scheduleData['total_amount'],
                'outstanding_balance' => $scheduleData['total_amount'],
                'monthly_installment' => $application->proposed_monthly_installment,
                'repayment_start_date' => $application->repayment_start_date,
                'repayment_end_date' => $scheduleData['schedule'][count($scheduleData['schedule']) - 1]['due_date'],
                'total_months' => $application->proposed_repayment_months,
                'remaining_months' => $application->proposed_repayment_months,
                'status' => 'pending_disbursement',
                'deduction_mode' => $loanType->auto_deduct_default ? 'automatic' : 'manual',
                'is_topup' => $application->isTopup(),
                'parent_loan_id' => $application->parent_loan_id,
                'disbursement_method' => $disbursementData['method'] ?? 'bank_transfer',
                'bank_account_id' => $disbursementData['bank_account_id'] ?? null,
                'notes' => $disbursementData['notes'] ?? null,
                'company_id' => $application->company_id,
            ]);
            
            // Create repayment schedule
            foreach ($scheduleData['schedule'] as $installment) {
                LoanRepaymentSchedule::create([
                    'loan_id' => $loan->loan_id,
                    'installment_number' => $installment['installment_number'],
                    'due_date' => $installment['due_date'],
                    'beginning_balance' => $installment['beginning_balance'],
                    'principal_amount' => $installment['principal_amount'],
                    'interest_amount' => $installment['interest_amount'],
                    'total_installment' => $installment['total_installment'],
                    'ending_balance' => $installment['ending_balance'],
                    'company_id' => $application->company_id,
                ]);
            }
            
            // Mark application as processed
            $application->update([
                'status' => 'processed',
                'processed_by' => auth()->id(),
                'processed_at' => now()
            ]);
            
            // If topup, mark parent loan as consolidated
            if ($application->isTopup() && $application->parent_loan_id) {
                $parentLoan = Loan::find($application->parent_loan_id);
                if ($parentLoan) {
                    $parentLoan->update([
                        'status' => 'paid_off',
                        'outstanding_balance' => 0,
                        'remaining_months' => 0
                    ]);
                }
                
                $loan->update([
                    'consolidated_from_loan_id' => $application->parent_loan_id,
                    'previous_outstanding' => $parentLoan->outstanding_balance ?? 0,
                    'topup_amount' => $application->amount_requested - ($parentLoan->outstanding_balance ?? 0)
                ]);
            }
            
            return $loan;
        });
    }
    
    /**
     * Process loan disbursement
     */
    public function disburseLoan(Loan $loan, ?Carbon $disbursementDate = null): bool
    {
        $loan->markAsDisbursed($disbursementDate);
        return true;
    }
    
    /**
     * Process loan topup
     */
    public function processTopup(Loan $existingLoan, float $topupAmount, int $newMonths, Carbon $startDate): Loan
    {
        return DB::transaction(function () use ($existingLoan, $topupAmount, $newMonths, $startDate) {
            // Calculate new consolidated loan
            $calculation = $this->calculationService->calculateTopupLoan(
                $existingLoan,
                $topupAmount,
                $newMonths,
                $existingLoan->interest_rate,
                $existingLoan->interest_type
            );
            
            // Create topup application
            $application = LoanApplication::create([
                'employee_id' => $existingLoan->employee_id,
                'loan_type_id' => $existingLoan->loan_type_id,
                'application_type' => 'topup',
                'parent_loan_id' => $existingLoan->loan_id,
                'amount_requested' => $calculation['new_principal'],
                'purpose' => 'Topup loan on existing loan ' . $existingLoan->loan_reference,
                'repayment_start_date' => $startDate,
                'proposed_monthly_installment' => $calculation['new_monthly_installment'],
                'proposed_repayment_months' => $newMonths,
                'requested_by' => auth()->id(),
                'status' => 'approved', // Auto-approve for HR initiated
                'date_approved' => now(),
                'approved_by' => auth()->id(),
                'company_id' => $existingLoan->company_id,
            ]);
            
            // Create new consolidated loan
            $newLoan = $this->createLoanFromApplication($application);
            
            return $newLoan;
        });
    }
    
    /**
     * Get loan statistics for dashboard
     */
    public function getDashboardStats(?int $companyId = null): array
    {
        $query = Loan::query();
        if ($companyId) {
            $query->where('company_id', $companyId);
        }
        
        return [
            'total_active_loans' => (clone $query)->where('status', 'active')->count(),
            'total_amount_disbursed' => (clone $query)->whereIn('status', ['active', 'paid_off'])->sum('original_amount'),
            'total_outstanding' => (clone $query)->where('status', 'active')->sum('outstanding_balance'),
            'total_paid_off' => (clone $query)->where('status', 'paid_off')->count(),
            'pending_disbursement' => (clone $query)->where('status', 'pending_disbursement')->count(),
            'pending_approval' => LoanApplication::whereIn('status', ['submitted', 'under_review'])
                ->when($companyId, fn($q) => $q->where('company_id', $companyId))
                ->count(),
            'total_topups' => (clone $query)->where('is_topup', true)->count(),
            'monthly_collections' => $this->getMonthlyCollections($companyId),
        ];
    }
    
    /**
     * Get monthly collection data
     */
    private function getMonthlyCollections(?int $companyId = null): array
    {
        $months = collect();
        
        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $months->push([
                'month' => $date->format('M Y'),
                'amount' => \App\Models\Loan\LoanPayment::whereMonth('payment_date', $date->month)
                    ->whereYear('payment_date', $date->year)
                    ->when($companyId, fn($q) => $q->where('company_id', $companyId))
                    ->sum('amount')
            ]);
        }
        
        return $months->toArray();
    }
}
```

---

## 5. Payroll Integration

### 5.1 Modified KenyanPayrollCalculationService

Add the following methods to the existing service:

```php
/**
 * Calculate loan deductions for payroll
 */
protected function calculateLoanDeductions($employeePayroll, $payrollPeriod): array
{
    $loanService = app(LoanCalculationService::class);
    
    $deductions = $loanService->getMonthlyDeductions(
        $employeePayroll->employee_id,
        Carbon::parse($payrollPeriod->payroll_date)
    );
    
    $totalDeduction = 0;
    $deductionDetails = [];
    
    foreach ($deductions as $deduction) {
        $totalDeduction += $deduction['amount'];
        $deductionDetails[] = [
            'loan_id' => $deduction['loan_id'],
            'schedule_id' => $deduction['schedule_id'],
            'description' => $deduction['description'],
            'amount' => $deduction['amount'],
            'is_partial' => $deduction['is_partial']
        ];
    }
    
    return [
        'total' => $totalDeduction,
        'details' => $deductionDetails
    ];
}

/**
 * Save loan deductions to payroll record details
 */
protected function saveLoanDeductionDetails($payrollRecord, array $loanDeductions): void
{
    foreach ($loanDeductions['details'] as $deduction) {
        PayrollRecordDetail::create([
            'payroll_record_id' => $payrollRecord->id,
            'employee_id' => $payrollRecord->employee_id,
            'detail_type' => 'loan_deduction',
            'description' => $deduction['description'],
            'amount' => $deduction['amount'],
            'reference_id' => $deduction['loan_id'],
            'reference_type' => 'loan',
            'additional_data' => json_encode([
                'schedule_id' => $deduction['schedule_id'],
                'is_partial' => $deduction['is_partial']
            ])
        ]);
    }
}

/**
 * Process loan payments after payroll approval
 */
public function processLoanPayments($payrollPeriod): void
{
    $payrollRecords = PayrollRecord::where('payroll_period_id', $payrollPeriod->id)
        ->where('status', 'approved')
        ->get();
    
    foreach ($payrollRecords as $record) {
        $loanDetails = PayrollRecordDetail::where('payroll_record_id', $record->id)
            ->where('detail_type', 'loan_deduction')
            ->get();
        
        foreach ($loanDetails as $detail) {
            $loan = Loan::find($detail->reference_id);
            if ($loan) {
                $loan->recordPayment($detail->amount, $record->id, 'payroll_deduction');
            }
        }
    }
}
```

### 5.2 Integration Points

1. **During Payroll Calculation:**
   - Call `calculateLoanDeductions()` in the main calculation flow
   - Include loan deductions in the net salary calculation

2. **During Payroll Approval:**
   - Call `processLoanPayments()` to record actual loan payments
   - Update loan balances and repayment schedules

3. **Manual Deduction Entry:**
   - Admin can enter manual loan deductions via `LoanManualDeduction` model
   - These are included in payroll calculation instead of automatic deductions

---

## 6. Controllers Structure

### 6.1 Admin Controllers

#### LoanTypeController (`app/Http/Controllers/Admin/Loan/LoanTypeController.php`)
- CRUD operations for loan types
- Interest rate configuration

#### LoanController (`app/Http/Controllers/Admin/Loan/LoanController.php`)
- List all loans with filters
- View loan details and schedule
- Process disbursement
- Suspend/write-off loans

#### LoanApplicationController (`app/Http/Controllers/Admin/Loan/LoanApplicationController.php`)
- View all applications
- Review and approve/reject
- Bulk approval actions

#### LoanDashboardController (`app/Http/Controllers/Admin/Loan/LoanDashboardController.php`)
- Dashboard with statistics
- Charts and reports

#### LoanManualDeductionController (`app/Http/Controllers/Admin/Loan/LoanManualDeductionController.php`)
- Enter manual deductions per period
- View deduction history
- Override automatic deductions

### 6.2 ESS (Employee Self-Service) Controllers

#### EmployeeLoanController (`app/Http/Controllers/ESS/LoanController.php`)
- View my loans
- Apply for new loan
- Apply for topup
- View repayment schedule
- Track application status

---

## 7. Routes

### 7.1 Admin Routes
```php
Route::middleware(['auth', 'permission'])->prefix('admin')->group(function () {
    
    // Loan Management Module
    Route::prefix('loans')->name('loans.')->group(function () {
        
        // Dashboard
        Route::get('dashboard', [LoanDashboardController::class, 'index'])->name('dashboard');
        
        // Loan Types (Setup)
        Route::resource('types', LoanTypeController::class);
        
        // Loan Applications
        Route::get('applications', [LoanApplicationController::class, 'index'])->name('applications.index');
        Route::get('applications/{id}', [LoanApplicationController::class, 'show'])->name('applications.show');
        Route::post('applications/{id}/approve', [LoanApplicationController::class, 'approve'])->name('applications.approve');
        Route::post('applications/{id}/reject', [LoanApplicationController::class, 'reject'])->name('applications.reject');
        Route::get('applications/pending/list', [LoanApplicationController::class, 'pendingList'])->name('applications.pending');
        
        // Loans
        Route::get('/', [LoanController::class, 'index'])->name('index');
        Route::get('create', [LoanController::class, 'create'])->name('create');
        Route::post('store', [LoanController::class, 'store'])->name('store');
        Route::get('{id}', [LoanController::class, 'show'])->name('show');
        Route::post('{id}/disburse', [LoanController::class, 'disburse'])->name('disburse');
        Route::post('{id}/suspend', [LoanController::class, 'suspend'])->name('suspend');
        Route::post('{id}/write-off', [LoanController::class, 'writeOff'])->name('write-off');
        Route::post('{id}/topup', [LoanController::class, 'topup'])->name('topup');
        
        // Manual Deductions
        Route::get('manual-deductions', [LoanManualDeductionController::class, 'index'])->name('manual-deductions.index');
        Route::get('manual-deductions/create', [LoanManualDeductionController::class, 'create'])->name('manual-deductions.create');
        Route::post('manual-deductions/store', [LoanManualDeductionController::class, 'store'])->name('manual-deductions.store');
        
        // Reports
        Route::get('reports/summary', [LoanReportController::class, 'summary'])->name('reports.summary');
        Route::get('reports/collections', [LoanReportController::class, 'collections'])->name('reports.collections');
        Route::get('reports/outstanding', [LoanReportController::class, 'outstanding'])->name('reports.outstanding');
    });
});
```

### 7.2 ESS Routes
```php
Route::middleware(['auth'])->prefix('ess')->name('ess.')->group(function () {
    
    // Loans
    Route::prefix('loans')->name('loans.')->group(function () {
        Route::get('/', [LoanController::class, 'index'])->name('index');
        Route::get('create', [LoanController::class, 'create'])->name('create');
        Route::post('store', [LoanController::class, 'store'])->name('store');
        Route::get('{id}', [LoanController::class, 'show'])->name('show');
        
        // Topup
        Route::get('{id}/topup', [LoanController::class, 'topupForm'])->name('topup.form');
        Route::post('{id}/topup', [LoanController::class, 'topupStore'])->name('topup.store');
        
        // Applications
        Route::get('applications/history', [LoanController::class, 'applicationHistory'])->name('applications.history');
    });
});
```

---

## 8. Views Structure

```
resources/views/admin/loans/
├── dashboard.blade.php                 # Statistics and charts
├── types/
│   ├── index.blade.php
│   ├── create.blade.php
│   └── edit.blade.php
├── applications/
│   ├── index.blade.php                 # All applications list
│   ├── pending.blade.php               # Pending approvals
│   ├── show.blade.php                  # Application details
│   └── review.blade.php                # Review form
├── loans/
│   ├── index.blade.php                 # All loans list
│   ├── create.blade.php                # HR create loan
│   ├── show.blade.php                  # Loan details with schedule
│   ├── schedule.blade.php              # Repayment schedule view
│   └── disburse.blade.php              # Disbursement form
├── manual-deductions/
│   ├── index.blade.php
│   └── create.blade.php
└── reports/
    ├── summary.blade.php
    ├── collections.blade.php
    └── outstanding.blade.php

resources/views/ess/loans/
├── index.blade.php                     # My loans list
├── create.blade.php                    # Apply for loan
├── show.blade.php                      # View loan details
├── topup-form.blade.php                # Topup application
└── schedule.blade.php                  # View my repayment schedule
```

---

## 9. Permissions

Add the following permissions to the system:

| Permission | Description |
|------------|-------------|
| `loans.dashboard` | View loan dashboard |
| `loans.types.index` | View loan types |
| `loans.types.create` | Create loan types |
| `loans.types.edit` | Edit loan types |
| `loans.types.delete` | Delete loan types |
| `loans.index` | View all loans |
| `loans.create` | Create loan (HR) |
| `loans.show` | View loan details |
| `loans.disburse` | Disburse loans |
| `loans.suspend` | Suspend loans |
| `loans.write-off` | Write off loans |
| `loans.topup` | Process topups |
| `loans.applications.index` | View applications |
| `loans.applications.approve` | Approve applications |
| `loans.applications.reject` | Reject applications |
| `loans.manual-deductions.index` | View manual deductions |
| `loans.manual-deductions.create` | Create manual deductions |
| `loans.reports.summary` | View summary reports |
| `loans.reports.collections` | View collection reports |
| `loans.reports.outstanding` | View outstanding reports |
| `ess.loans.index` | Employee view own loans |
| `ess.loans.create` | Employee apply for loan |
| `ess.loans.topup` | Employee apply for topup |

---

## 10. API Endpoints (Optional for AJAX)

```php
// Loan calculation API
Route::get('api/loans/calculate', [LoanApiController::class, 'calculate']);
Route::get('api/loans/eligibility/{employeeId}', [LoanApiController::class, 'checkEligibility']);
Route::get('api/loans/{id}/schedule', [LoanApiController::class, 'getSchedule']);
Route::post('api/loans/calculate-topup', [LoanApiController::class, 'calculateTopup']);
```

---

## 11. Validation Rules

### Loan Application Validation
```php
$rules = [
    'loan_type_id' => 'required|exists:loan_types,loan_type_id',
    'amount_requested' => 'required|numeric|min:1',
    'purpose' => 'nullable|string|max:500',
    'repayment_start_date' => 'required|date|after_or_equal:today',
    'proposed_monthly_installment' => 'required|numeric|min:1',
    'proposed_repayment_months' => 'required|integer|min:1|max:60',
];
```

### Topup Validation
```php
$rules = [
    'existing_loan_id' => 'required|exists:loans,loan_id',
    'topup_amount' => 'required|numeric|min:1',
    'new_repayment_months' => 'required|integer|min:1',
];
```

---

## 12. Testing Considerations

### Unit Tests
- Loan calculation accuracy (flat vs reducing balance)
- Repayment schedule generation
- Topup calculations
- Eligibility checks

### Feature Tests
- Application workflow (submit → approve → disburse)
- Payroll deduction integration
- Manual deduction entry
- Topup consolidation flow

### Edge Cases
- Employee with multiple active loans (if allowed)
- Early loan payoff
- Partial payments
- Interest rate changes
- Payroll calculation with suspended loans

---

## 13. Migration Rollback Plan

```php
public function down()
{
    Schema::dropIfExists('loan_manual_deductions');
    Schema::dropIfExists('loan_payments');
    Schema::dropIfExists('loan_repayment_schedule');
    Schema::dropIfExists('loans');
    Schema::dropIfExists('loan_applications');
    Schema::dropIfExists('loan_types');
}
```

---

## 14. Future Enhancements

1. **Loan Insurance:** Optional insurance on loans
2. **Co-signer Support:** Require guarantor for large loans
3. **Loan Re-amortization:** Change terms mid-loan
4. **Bulk Disbursement:** Process multiple loans at once
5. **Loan Notifications:** SMS/Email reminders for due payments
6. **Credit Score Integration:** External credit checking
7. **Multi-currency Support:** For international organizations

---

*Document Version: 1.0*
*Last Updated: 2026-05-01*
*Author: System Architect*
