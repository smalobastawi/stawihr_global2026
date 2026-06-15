<?php

namespace App\Models\Payroll;

use App\Lib\Enumerations\ApprovalStatus;
use App\Lib\Enumerations\Currency;
use App\Lib\Enumerations\GeneralStatus;
use App\Models\Employee;
use App\Models\EmployeeDeductions;
use App\Models\EmployeeEarnings;
use App\Traits\HasApprovalWorkflow;
use App\Traits\ProvidesApprovalDetails;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\Facades\LogBatch;
use Spatie\Activitylog\LogOptions;

class EmployeePayroll extends Model
{
    use HasFactory, SoftDeletes, HasApprovalWorkflow, ProvidesApprovalDetails, LogsActivity;

    protected $fillable = [
        'employee_id',
        'payroll_number',
        'basic_salary',
        'income_frequency',
        'phone_number',
        'currency',
        'payment_method',
        'bank_name',
        'bank_branch',
        'account_number',
        'account_name',
        'kra_pin',
        'nssf_number',
        'shif_number',
        'tax_status',
        'disability_exemption',
        'pension_scheme_id',
        'employee_pension_rate',
        'employer_pension_rate',
        'overtime_rate_normal',
        'overtime_rate_weekend',
        'overtime_rate_holiday',
        'is_active',
        'effective_date',
        'created_by',
        'updated_by',
        'status',
        'approval_status',
        'date_approved',
        'approved_by',
    ];

    protected $casts = [
        'basic_salary' => 'decimal:2',
        'overtime_rate_normal' => 'decimal:2',
        'overtime_rate_weekend' => 'decimal:2',
        'overtime_rate_holiday' => 'decimal:2',
        'employee_pension_rate' => 'decimal:4',
        'employer_pension_rate' => 'decimal:4',
        'disability_exemption' => 'boolean',
        'is_active' => 'boolean',
        'effective_date' => 'date'
    ];

    // Payment methods
    const PAYMENT_METHODS = [
        'bank_transfer' => 'Bank Transfer',
        'mobile_money' => 'Mobile Money',

    ];

    // Tax status options
    const TAX_STATUS = [
        'resident' => 'Resident',
        'non_resident' => 'Non-Resident',
        'exempt' => 'Tax Exempt'
    ];

    // Income frequency options
    const INCOME_FREQUENCY = [
        'daily' => 'Daily',
        'weekly' => 'Weekly',
        'monthly' => 'Monthly'
    ];

    /**
     * Relationship with Employee
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    /**
     * Relationship with Pension Schemes (many-to-many)
     */
    public function pensionSchemes()
    {
        return $this->belongsToMany(PensionScheme::class, 'employee_pension_schemes')
            ->withPivot('employee_rate', 'employer_rate')
            ->withTimestamps();
    }

    /**
     * Relationship with Pension Scheme (legacy - for backward compatibility)
     */
    public function pensionScheme()
    {
        return $this->hasMany(PensionScheme::class, 'id', 'employee_payroll_id');
    }

    /**
     * Relationship with Payroll Allowances
     */
    public function allowances()
    {
        $payrollPeriod = getCurrentPayrollPeriod();
        $periodStart = $payrollPeriod->input_period_start->format('Y-m-d');
        $periodEnd = $payrollPeriod->input_period_end->format('Y-m-d');

        return $this->hasMany(EmployeeEarnings::class, 'employee_id', 'employee_id')
            ->where('earning_category', 'Allowance')
            ->where('status', GeneralStatus::ACTIVE)
            ->where('approval_status', ApprovalStatus::APPROVED)
            ->where(function ($query) use ($periodStart, $periodEnd) {
                $query->where(function ($q) use ($periodStart, $periodEnd) {
                    // Records that are active during ANY part of the payroll period
                    $q->where('effective_from', '<=', $periodEnd)
                        ->where(function ($subQ) use ($periodStart) {
                            $subQ->whereNull('effective_to')
                                ->orWhere('effective_to', '>=', $periodStart);
                        });
                });
            });
    }

    public function earnings()
    {
        $payrollPeriod = getCurrentPayrollPeriod();
        $periodStart = $payrollPeriod->input_period_start->format('Y-m-d');
        $periodEnd = $payrollPeriod->input_period_end->format('Y-m-d');

        return $this->hasMany(EmployeeEarnings::class, 'employee_id', 'employee_id')
            ->where('status', GeneralStatus::ACTIVE)
            ->where('approval_status', ApprovalStatus::APPROVED)
            ->where(function ($query) use ($periodStart, $periodEnd) {
                $query->where(function ($q) use ($periodStart, $periodEnd) {
                    // Records that are active during ANY part of the payroll period
                    $q->where('effective_from', '<=', $periodEnd)
                        ->where(function ($subQ) use ($periodStart) {
                            $subQ->whereNull('effective_to')
                                ->orWhere('effective_to', '>=', $periodStart);
                        });
                });
            });
    }

    /**
     * Relationship with Payroll Deductions
     */
    public function deductions()
    {
        return $this->hasMany(EmployeeDeductions::class, 'employee_id', 'employee_id');
    }

    /**
     * Relationship with Payroll Records
     */
    public function payrollRecords()
    {
        return $this->hasMany(PayrollRecord::class);
    }

    /**
     * Get active allowances for the employee
     */
    /**
     * Get active allowances for the employee for current payroll period
     */
    public function getActiveAllowances($payrollPeriod = null)
    {
        if (!$payrollPeriod) {
            $payrollPeriod = getCurrentPayrollPeriod();
        }

        $periodStart = $payrollPeriod->input_period_start->format('Y-m-d');
        $periodEnd = $payrollPeriod->input_period_end->format('Y-m-d');

        return EmployeeEarnings::where('employee_id', $this->employee_id)
            ->where('earning_category', 'Allowance') // Make sure this matches your enum/category value
            ->where('status', GeneralStatus::ACTIVE)
            ->where('approval_status', ApprovalStatus::APPROVED)
            ->where(function ($query) use ($periodStart, $periodEnd) {
                $query->where(function ($q) use ($periodStart, $periodEnd) {
                    // Records that are active during ANY part of the payroll period
                    $q->where('effective_from', '<=', $periodEnd)
                        ->where(function ($subQ) use ($periodStart) {
                            $subQ->whereNull('effective_to')
                                ->orWhere('effective_to', '>=', $periodStart);
                        });
                });
            })
            ->get();
    }

    public function getAllActiveEarnings()
    {
        $payrollPeriod = getCurrentPayrollPeriod();
        $periodStart = $payrollPeriod->input_period_start->format('Y-m-d');
        $periodEnd = $payrollPeriod->input_period_end->format('Y-m-d');

        return EmployeeEarnings::where('employee_id', $this->employee_id)
            ->where('approval_status', ApprovalStatus::APPROVED)
            ->where('status', GeneralStatus::ACTIVE)
            ->where(function ($query) use ($periodStart, $periodEnd) {
                $query->where(function ($q) use ($periodStart, $periodEnd) {
                    // Records that are active during ANY part of the period
                    $q->where(function ($innerQ) use ($periodStart, $periodEnd) {
                        // Records that start before period end AND end after period start
                        $innerQ->where('effective_from', '<=', $periodEnd)
                            ->where(function ($subQ) use ($periodStart) {
                                $subQ->whereNull('effective_to')
                                    ->orWhere('effective_to', '>=', $periodStart);
                            });
                    });
                });
            })
            ->get();
    }

    public function getSalaryTypeEarning()
    {
        return EmployeeEarnings::where('employee_id', $this->employee_id)
            ->whereHas('payrollEarningType', function ($query) {
                $query->where('name', 'LIKE', '%salary%');
            })
            ->with('payrollEarningType')
            ->where('approval_status', ApprovalStatus::APPROVED)
            ->where('status', GeneralStatus::ACTIVE)
            ->where('effective_from', '>=', getCurrentPayrollPeriod()->input_period_start->format('Y-m-d'))
            ->where(function ($query) {
                $query->whereNull('effective_to')
                    ->orWhere('effective_to', '>=', getCurrentPayrollPeriod()->input_period_end->format('Y-m-d'));
            })
            ->first();
    }

    /**
     * Get active deductions for the employee
     */


    /**
     * Calculate total allowances
     */
    public function getTotalAllowances($payrollPeriod = null)
    {
        $allowances = $this->getActiveAllowances();
        $total = 0;
        $basicSalary = $this->basic_salary;

        foreach ($allowances as $allowance) {
            $total += $allowance->calculateEarningAmount($basicSalary);
        }

        return $total;
    }

    /**
     * Calculate total earnings from all EmployeeEarnings
     */
    public function getTotalEarnings($payrollPeriod = null)
    {
        $earnings = $this->getAllActiveEarnings();
        $total = 0;
        $basicSalary = $this->basic_salary;
        $grossSalary = $this->getGrossSalary();

        foreach ($earnings as $earning) {

            $total += $earning->calculateEarningAmount($basicSalary, $grossSalary);
        }

        return $total;
    }

    /**
     * Calculate total deductions (excluding statutory)
     */


    /**
     * Get gross salary (basic + allowances)
     */
    public function getGrossSalary($payrollPeriod = null)
    {
        return $this->basic_salary + $this->getTotalAllowances($payrollPeriod);
    }

    /**
     * Generate unique payroll number
     */
    public static function generatePayrollNumber()
    {
        $maxAttempts = 5;
        $attempt = 0;

        while ($attempt < $maxAttempts) {
            try {
                $lastEmployee = self::orderBy('id', 'desc')->first();
                $nextNumber = $lastEmployee ? (intval(substr($lastEmployee->payroll_number, -4)) + 1) : 1;
                $payrollNumber = 'EMPR' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

                // Verify uniqueness
                if (!self::where('payroll_number', $payrollNumber)->exists()) {
                    return $payrollNumber;
                }

                $attempt++;
                usleep(100000); // Wait 100ms before retry

            } catch (\Exception $e) {
                $attempt++;
                if ($attempt >= $maxAttempts) {
                    throw $e;
                }
            }
        }

        throw new \Exception('Failed to generate unique payroll number after ' . $maxAttempts . ' attempts');
    }

    /**
     * Scope for active employees
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for employees with valid KRA PIN
     */
    public function scopeWithKraPin($query)
    {
        return $query->whereNotNull('kra_pin')->where('kra_pin', '!=', '');
    }

    /**
     * Scope for employees with NSSF number
     */
    public function scopeWithNssfNumber($query)
    {
        return $query->whereNotNull('nssf_number')->where('nssf_number', '!=', '');
    }

    /**
     * Scope for employees with SHIF number
     */
    public function scopeWithShifNumber($query)
    {
        return $query->whereNotNull('shif_number')->where('shif_number', '!=', '');
    }

    // In your EmployeePayroll model

    /**
     * Relationship with EmployeeDeductions
     */
    public function employeeDeductions()
    {

        return $this->hasMany(EmployeeDeductions::class, 'employee_id', 'employee_id');
    }

    /**
     * Get active deductions for the employee
     */
    /**
     * Get active deductions for the employee for current payroll period
     */
    public function getActiveDeductions($payrollPeriod = null)
    {
        if (!$payrollPeriod) {
            $payrollPeriod = getCurrentPayrollPeriod();
        }

        $periodStart = $payrollPeriod->input_period_start->format('Y-m-d');
        $periodEnd = $payrollPeriod->input_period_end->format('Y-m-d');

        return $this->employeeDeductions()
            ->active()
            ->approved()
            ->where(function ($query) use ($periodStart, $periodEnd) {
                $query->where(function ($q) use ($periodStart, $periodEnd) {
                    // Records that are active during ANY part of the payroll period
                    $q->where('effective_from', '<=', $periodEnd)
                        ->where(function ($subQ) use ($periodStart) {
                            $subQ->whereNull('effective_to')
                                ->orWhere('effective_to', '>=', $periodStart);
                        });
                });
            })
            ->get();
    }

    /**
     * Calculate total deductions (excluding statutory)
     */
    public function getTotalDeductions($payrollPeriod = null)
    {
        $deductions = $this->getActiveDeductions();
        $total = 0;
        $basicSalary = $this->basic_salary;
        $grossSalary = $this->getGrossSalary();

        foreach ($deductions as $deduction) {
            $total += $deduction->calculateDeductionAmount($basicSalary, $grossSalary);
        }

        return $total;
    }

    /**
     * Get statutory deductions (if you need to separate them)
     */
    public function getStatutoryDeductions($payrollPeriod = null)
    {
        if (!$payrollPeriod) {
            $payrollPeriod = getCurrentPayrollPeriod();
        }

        $periodStart = $payrollPeriod->input_period_start->format('Y-m-d');
        $periodEnd = $payrollPeriod->input_period_end->format('Y-m-d');

        return $this->employeeDeductions()
            ->active()
            ->approved()
            ->where(function ($query) use ($periodStart, $periodEnd) {
                $query->where(function ($q) use ($periodStart, $periodEnd) {
                    $q->where('effective_from', '<=', $periodEnd)
                        ->where(function ($subQ) use ($periodStart) {
                            $subQ->whereNull('effective_to')
                                ->orWhere('effective_to', '>=', $periodStart);
                        });
                });
            })
            ->whereHas('payrollDeductionType', function ($query) {
                $query->where('is_statutory', true);
            })
            ->get();
    }

    /**
     * Calculate total statutory deductions
     */
    public function getTotalStatutoryDeductions($payrollPeriod = null)
    {
        $deductions = $this->getStatutoryDeductions($payrollPeriod);
        $total = 0;
        $basicSalary = $this->basic_salary;
        $grossSalary = $this->getGrossSalary($payrollPeriod);

        foreach ($deductions as $deduction) {
            $total += $deduction->calculateDeductionAmount($basicSalary, $grossSalary);
        }

        return $total;
    }

    /**
     * Calculate overtime pay for normal working days
     *
     * @param int $hours Number of overtime hours
     * @param int $workingDaysInMonth Number of working days in the month
     * @return float
     */
    // In your EmployeePayroll model
    public function calculateNormalOvertimePay($hours)
    {
        if (!$this->overtime_rate_normal || $hours <= 0) {
            return 0;
        }

        $hourlyRate = $this->basic_salary / ($this->getWorkingHoursPerMonth());
        return $hourlyRate * $this->overtime_rate_normal * $hours;
    }

    public function calculateWeekendOvertimePay($hours)
    {
        if (!$this->overtime_rate_weekend || $hours <= 0) {
            return 0;
        }

        $hourlyRate = $this->basic_salary / ($this->getWorkingHoursPerMonth());
        return $hourlyRate * $this->overtime_rate_weekend * $hours;
    }

    public function calculateHolidayOvertimePay($hours)
    {
        if (!$this->overtime_rate_holiday || $hours <= 0) {
            return 0;
        }

        $hourlyRate = $this->basic_salary / ($this->getWorkingHoursPerMonth());
        return $hourlyRate * $this->overtime_rate_holiday * $hours;
    }

    // Add this helper method to your EmployeePayroll model
    public function getWorkingHoursPerMonth($workingDaysInMonth = 22)
    {
        return $workingDaysInMonth * 8; // Assuming 8 hours per workday
    }

    /**
     * Calculate total overtime pay for all types
     *
     * @param array $overtimeData ['normal' => hours, 'weekend' => hours, 'holiday' => hours]
     * @param int $workingDaysInMonth Number of working days in the month
     * @return float
     */
    public function calculateTotalOvertimePay($overtimeData, $workingDaysInMonth = 22)
    {
        $total = 0;

        if (isset($overtimeData['normal'])) {
            $total += $this->calculateNormalOvertimePay($overtimeData['normal'], $workingDaysInMonth);
        }

        if (isset($overtimeData['weekend'])) {
            $total += $this->calculateWeekendOvertimePay($overtimeData['weekend'], $workingDaysInMonth);
        }

        if (isset($overtimeData['holiday'])) {
            $total += $this->calculateHolidayOvertimePay($overtimeData['holiday'], $workingDaysInMonth);
        }

        return $total;
    }

    /**
     * Get default overtime rates
     */
    public static function getDefaultOvertimeRates()
    {
        return [
            'normal' => 1.5,    // Overtime 1.5
            'weekend' => 2.0,   // Overtime 2
            'holiday' => 2.0,   // Overtime 2 for public holidays
            'half' => 0.5       // Half rate for specific cases
        ];
    }

    public function getDisplayCurrency(): string
    {
        if (!empty($this->currency) && Currency::isValid($this->currency)) {
            return strtoupper($this->currency);
        }

        $companyCurrency = $this->employee?->company?->currency;

        if (!empty($companyCurrency) && Currency::isValid($companyCurrency)) {
            return strtoupper($companyCurrency);
        }

        return Currency::DEFAULT;
    }

    public function getApprovalDetails(): array
    {
        $details = [
            'Employee' => $this->employee->fullName() ?? 'N/A',
            'Employee ID' => $this->employee->employee_id ?? 'N/A',
            'Payroll Number' => $this->payroll_number ?? 'N/A',
            'Basic Salary' => number_format($this->basic_salary, 2),
            'Income Frequency' => $this->income_frequency ?? 'N/A',
            'Currency' => $this->getDisplayCurrency(),
            'Payment Method' => $this->payment_method ?? 'N/A',
            'Bank Details' => $this->bank_name ? "{$this->bank_name} ({$this->bank_branch}) - ••••" . substr($this->account_number, -4) : 'N/A',
            'Account Name' => $this->account_name ?? 'N/A',
            'KRA PIN' => $this->kra_pin ?? 'N/A',
            'NSSF Number' => $this->nssf_number ?? 'N/A',
            'SHIF Number' => $this->shif_number ?? 'N/A',
            'Tax Status' => $this->tax_status ?? 'N/A',
            'Disability Exemption' => $this->disability_exemption ? 'Yes' : 'No',
            'Overtime Rate (Normal)' => $this->overtime_rate_normal ? $this->overtime_rate_normal . 'x' : 'N/A',
            'Overtime Rate (Weekend)' => $this->overtime_rate_weekend ? $this->overtime_rate_weekend . 'x' : 'N/A',
            'Overtime Rate (Holiday)' => $this->overtime_rate_holiday ? $this->overtime_rate_holiday . 'x' : 'N/A',
            'Status' => $this->is_active ? 'Active' : 'Inactive',
            'Effective Date' => $this->effective_date ? $this->effective_date->format('Y-m-d') : 'N/A'
        ];

        // Add pension schemes with their rates (convert array to string)
        if ($this->pensionSchemes->isNotEmpty()) {
            $pensionDetails = $this->pensionSchemes->map(function ($scheme) {
                return sprintf(
                    "%s: Employee %s%%, Employer %s%%",
                    $scheme->name,
                    $scheme->pivot->employee_rate,
                    $scheme->pivot->employer_rate
                );
            })->implode('; ');

            $details['Pension Schemes'] = $pensionDetails;
        } else {
            $details['Pension Schemes'] = 'None';
        }

        // Add active allowances if needed (convert array to string)
        $allowances = $this->getActiveAllowances();
        if ($allowances->isNotEmpty()) {
            $details['Active Allowances'] = $allowances->map(function ($allowance) {
                return $allowance->payrollEarningType->name . ': ' . number_format($allowance->amount, 2);
            })->implode('; ');
        } else {
            $details['Active Allowances'] = 'None';
        }

        // Add active deductions if needed (convert array to string)
        $deductions = $this->getActiveDeductions();
        if ($deductions->isNotEmpty()) {
            $details['Active Deductions'] = $deductions->map(function ($deduction) {
                return $deduction->payrollDeductionType->name . ': ' . number_format($deduction->amount, 2);
            })->implode('; ');
        } else {
            $details['Active Deductions'] = 'None';
        }

        return $details;
    }
    public function getActivitylogOptions(): LogOptions
    {
        LogBatch::startBatch();
        return LogOptions::defaults()
            ->logAll();
    }
}
