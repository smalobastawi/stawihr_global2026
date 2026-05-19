<?php

namespace App\Services\Payroll;

use App\Jobs\RecalculatePayrollJob;
use App\Lib\Enumerations\ApprovalStatus;
use App\Lib\Enumerations\GeneralStatus;
use App\Models\Employee;
use App\Models\Payroll\PayrollConfiguration;
use App\Models\Payroll\EmployeePayroll;
use App\Models\Payroll\PayrollRecord;
use App\Models\Payroll\PayrollRecordDetail;
use App\Models\Payroll\PayrollPeriod;
use App\Models\Payroll\PayrollClaimRecovery;
use Carbon\Carbon;
use App\Models\EmployeeOvertime;
use App\Lib\Enumerations\CalculationTypes;
use App\Lib\Enumerations\OvertimeCalculationType;
use App\Lib\Enumerations\PayrollStatus;
use App\Models\Payroll\PayrollClaim;
use App\Models\Loan;
use App\Models\Payroll;
use App\Models\LoanDeduction;
use App\Models\ManualLoanDeduction;
use App\Repositories\AttendanceRepository;
use App\Models\ApprovalWorkflow;
use DateTime;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class KenyanPayrollCalculationService
{
    /**
     * Calculate payroll for an employee
     */
    protected $attendanceRepository, $currentPayrollPeriod, $payrollChangeService;

    public function __construct(AttendanceRepository $attendanceRepository, PayrollChangeService $payrollChangeService)
    {
        $this->attendanceRepository = $attendanceRepository;
        $this->payrollChangeService = $payrollChangeService;
        $this->currentPayrollPeriod = getCurrentPayrollPeriod();
    }

    public function calculateEmployeePayroll(EmployeePayroll $employeePayroll, PayrollPeriod $period)
    {
        // Get basic salary - now using prorated calculation that handles salary changes
        $basicSalary = $this->calculateProratedBasicSalary($employeePayroll, $period);


        $employeeDetails = Employee::findOrFail($employeePayroll->employee_id);

        // Calculate allowances
        $allowances = $this->calculateAllowances($employeePayroll);
        // Calculate overtime earnings
        $overtimeEarnings = $this->calculateOvertimeEarnings($employeePayroll, $period);
        $allEarnings = array_merge($allowances, $overtimeEarnings);

        $totalAllowances = array_sum(array_column($allEarnings, 'amount'));

        $pensionablePay = $this->calculatePensionablePay($basicSalary, $allEarnings);

        // Calculate gross salary
        $grossSalary = $basicSalary + $totalAllowances;
        $allInsurances = $this->getallInsurances($employeePayroll, $grossSalary);
        $insuranceRelief_all = $this->calculateInsuranceRelief_All($allInsurances);

        // Check if gross salary is zero and assign zeros to all deductions
        if ($grossSalary <= 0) {
            return $this->createZeroPayrollRecord($employeePayroll, $period, $basicSalary, $totalAllowances, $allEarnings);
        }

        $nssfBreakdown = $this->calculateNssfContribution($employeeDetails, $grossSalary, $this->currentPayrollPeriod);

        $nssfContribution = $nssfBreakdown['total'];
        $nssfTier1 = $nssfBreakdown['tier1'];
        $nssfTier2 = $nssfBreakdown['tier2'];

        $shifContribution = $this->calculateShifContribution($grossSalary);
        $housingLevy = $this->calculateHousingLevy($grossSalary);
        $housingLevyRelief = $this->getAHLRelief($housingLevy);
        $pensionContribution = $this->calculatePensionContribution($employeePayroll, $pensionablePay);

        // Calculate taxable income (gross salary minus non-taxable allowances)
        $taxableIncome = $this->calculateTaxableIncome($grossSalary, $allEarnings, $nssfContribution, $shifContribution, $housingLevy, $pensionContribution);

        $insuranceRelief = $this->calculateInsuranceRelief_SHIF($shifContribution);

        // Calculate statutory deductions
        $payeTax = $this->calculatePayeTax($taxableIncome, $employeePayroll, $insuranceRelief_all);

        // Calculate non-statutory deductions
        $nonStatutoryDeductions = $this->calculateNonStatutoryDeductions($employeePayroll, $grossSalary);

        // Calculate claim recoveries
        $claimRecoveries = $this->calculateClaimRecoveries($employeePayroll, $period);

        // Calculate loan deductions
        $loanDeductions = $this->calculateLoanDeductions($employeePayroll, $period);

        $totalNonStatutoryDeductions = array_sum(array_column($nonStatutoryDeductions, 'amount'));
        $totalClaimRecoveries = array_sum(array_column($claimRecoveries, 'amount'));
        $totalLoanDeductions = array_sum(array_column($loanDeductions, 'amount'));

        // Calculate total deductions
        $statutoryDeductions = $payeTax + $nssfContribution + $shifContribution + $housingLevy + $pensionContribution;
        $totalDeductions = $statutoryDeductions + $totalNonStatutoryDeductions + $totalClaimRecoveries + $totalLoanDeductions;

        // Calculate net salary
        $netSalary = $grossSalary - $totalDeductions;

        // Calculate company contributions
        $industrialTrainingLevy = $this->calculateIndustrialTrainingLevy($employeePayroll);
        $nssfTier1Company = $this->calculateNssfTier1Company($employeeDetails, $grossSalary, $this->currentPayrollPeriod);
        $nssfTier2Company = $this->calculateNssfTier2Company($employeeDetails, $grossSalary, $this->currentPayrollPeriod);
        $housingLevyCompany = $this->calculateHousingLevyCompany($grossSalary);
        $employerPensionContribution = $this->calculateEmployerPensionContribution($employeePayroll, $grossSalary);

        $shifCompanyContribution = 0; // Employer is not required to contribute shif

        // Get salary change metadata for record keeping
        $salaryChanges = $this->getSalaryChangesDuringPeriod(
            $employeePayroll->employee_id,
            $period->input_period_start->format('Y-m-d'),
            $period->input_period_end->format('Y-m-d')
        );

        // Create or update payroll record - handle duplicates more carefully
        $existingRecords = PayrollRecord::where('employee_id', $employeePayroll->employee_id)
            ->where('payroll_period_id', $period->id)
            ->withTrashed()
            ->get();

        // Delete all existing records for this employee and period
        foreach ($existingRecords as $existingRecord) {
            try {
                // Delete related details first
                PayrollRecordDetail::where('payroll_record_id', $existingRecord->id)->delete();
                // Force delete the main record
                $existingRecord->forceDelete();
            } catch (\Exception $e) {
                Log::warning("Failed to delete existing payroll record: " . $e->getMessage());
            }
        }

        // Also check for any records that might still exist due to database constraints
        DB::table('payroll_records')
            ->where('employee_id', $employeePayroll->employee_id)
            ->where('payroll_period_id', $period->id)
            ->delete();


        // Check for termination during this period for metadata
        $termination = \App\Models\Termination::where('terminate_to', $employeePayroll->employee_id)
            ->where('status', 2)
            ->where('termination_date', '>=', $period->input_period_start->format('Y-m-d'))
            ->where('termination_date', '<=', $period->input_period_end->format('Y-m-d'))
            ->first();

        // Collect detailed metadata for display
        $detailedMetadata = [
            'calculation_type' => $termination ? 'terminated_prorated' : ($salaryChanges->isEmpty() ? 'normal' : 'prorated'),
            'salary_changes_during_period' => $salaryChanges->count(),
            'salary_segments' => $this->getSalarySegmentsInfo($employeePayroll, $period),
            'pension_details' => $this->getPensionDetails($employeePayroll, $pensionablePay),
            'tax_breakdown' => $this->getTaxBreakdown($taxableIncome, $insuranceRelief_all),
            'reliefs_applied' => $this->getReliefsApplied($insuranceRelief_all, $employeePayroll),
            'taxable_amounts' => $this->getTaxableAmounts($grossSalary, $allEarnings, $nssfContribution, $shifContribution, $housingLevy, $pensionContribution),
            'termination_info' => $termination ? [
                'termination_id' => $termination->termination_id,
                'termination_date' => $termination->termination_date,
                'termination_type' => $termination->termination_type,
                'days_worked' => $this->getWorkingDaysInPeriod(Carbon::parse($period->input_period_start), Carbon::parse($termination->termination_date)),
                'total_working_days' => $this->getWorkingDaysInPeriod(Carbon::parse($period->input_period_start), Carbon::parse($period->input_period_end))
            ] : null
        ];

        // Check if there's an approval workflow for PayrollRecord
        $hasApprovalWorkflow = ApprovalWorkflow::forModel(PayrollRecord::class) !== null;

        // If no approval workflow exists, save as approved at all levels
        if (!$hasApprovalWorkflow) {
            $approvalStatus = ApprovalStatus::APPROVED;
            $payrollRecordStatus = PayrollStatus::APPROVED;
            $dateApproved = now();
            $approvedBy = auth()->id();
        } else {
            // Keep current behavior when approval workflow exists
            $approvalStatus = ApprovalStatus::DRAFT;
            $payrollRecordStatus = PayrollStatus::CALCULATED;
            $dateApproved = null;
            $approvedBy = null;
        }

        $payrollRecord = PayrollRecord::create(

            [
                'employee_payroll_id' => $employeePayroll->id,
                'payroll_period_id' => $period->id,
                'basic_salary' => $basicSalary,
                'employee_id' => $employeePayroll->employee_id,
                'total_allowances' => $totalAllowances,
                'gross_salary' => $grossSalary,
                'total_deductions' => $totalDeductions,
                'statutory_deductions' => $statutoryDeductions,
                'non_statutory_deductions' => $totalNonStatutoryDeductions,
                'claim_recoveries' => $totalClaimRecoveries,
                'loan_deductions' => $totalLoanDeductions,
                'paye_tax' => $payeTax,
                'nssf_contribution' => $nssfContribution,
                'nssf_tier1_contribution' => $nssfTier1,
                'nssf_tier2_contribution' => $nssfTier2,
                'shif_contribution' => $shifContribution,
                'housing_levy' => $housingLevy,
                'pension_contribution' => $pensionContribution,
                'net_salary' => $netSalary,
                'payment_method' => $employeePayroll->payment_method,
                'status' => PayrollRecord::STATUS_CALCULATED,
                'created_by' => auth()->id(),
                'approval_status' => $approvalStatus,
                'payroll_record_status' => $payrollRecordStatus,
                'date_approved' => $dateApproved,
                'approved_by' => $approvedBy,
                'industrial_training_levy' => $industrialTrainingLevy,
                'nssf_tier1_company_contribution' => $nssfTier1Company,
                'nssf_tier2_company_contribution' => $nssfTier2Company,
                'housing_levy_company_contribution' => $housingLevyCompany,
                'employer_pension_contribution' => $employerPensionContribution,
                'shif_company_contribution' => $shifCompanyContribution,
                'unpaid_amount' => 0,
                'metadata' => json_encode($detailedMetadata)
            ]
        );

        // Determine calculation method
        $calculationMethod = $termination ? 'terminated_prorated' : ($salaryChanges->isEmpty() ? 'normal' : 'prorated_with_changes');

        // Add basic income to details
        $basicIncomeDetail = [
            'name' => $termination ? 'Basic Income (Terminated - Prorated)' : 'Basic Income',
            'type' => 'earning',
            'code' => 'basic_income',
            'amount' => $basicSalary,
            'units' => $this->getBasicIncomeUnits($employeePayroll, $period),
            'calculation_basis' => $employeePayroll->basic_salary,
            'rate' => 1,
            'is_taxable' => true,
            'is_pensionable' => true,
            'metadata' => [
                'frequency' => $employeePayroll->income_frequency,
                'original_salary' => $employeePayroll->basic_salary,
                'calculation_method' => $calculationMethod,
                'termination_info' => $termination ? [
                    'termination_date' => $termination->termination_date,
                    'days_worked' => $detailedMetadata['termination_info']['days_worked'] ?? null,
                    'total_working_days' => $detailedMetadata['termination_info']['total_working_days'] ?? null
                ] : null
            ],
        ];

        // Add basic income to allowances
        array_unshift($allEarnings, $basicIncomeDetail);

        // Save payroll record details
        $this->savePayrollRecordDetails($payrollRecord, $allEarnings, $nonStatutoryDeductions, $claimRecoveries, $loanDeductions, [
            'paye' => $payeTax,
            'nssf' => $nssfContribution,
            'nssf_tier1' => $nssfTier1,
            'nssf_tier2' => $nssfTier2,
            'shif' => $shifContribution,
            'housing_levy' => $housingLevy,
            'pension' => $pensionContribution
        ], [
            'industrial_training_levy' => $industrialTrainingLevy,
            'nssf_tier1_company' => $nssfTier1Company,
            'nssf_tier2_company' => $nssfTier2Company,
            'housing_levy_company' => $housingLevyCompany,
            'employer_pension' => $employerPensionContribution,
            'shif_company' => $shifCompanyContribution
        ]);

        return $payrollRecord;
    }



    private function calculateProratedBasicSalary(EmployeePayroll $employeePayroll, PayrollPeriod $period)
    {
        $salaryChanges = $this->getSalaryChangesDuringPeriod(
            $employeePayroll->employee_id,
            $period->input_period_start->format('Y-m-d'),
            $period->input_period_end->format('Y-m-d')
        );

        // Check if employee was terminated during this payroll period
        $termination = \App\Models\Termination::where('terminate_to', $employeePayroll->employee_id)
            ->where('status', 2) // Approved terminations
            ->where('termination_date', '>=', $period->input_period_start->format('Y-m-d'))
            ->where('termination_date', '<=', $period->input_period_end->format('Y-m-d'))
            ->first();

        // If employee was terminated during this period, calculate prorated salary
        if ($termination) {
            return $this->calculateTerminatedEmployeeSalary($employeePayroll, $period, $termination);
        }

        if ($salaryChanges->isEmpty()) {
            return $this->calculateBasicSalaryByFrequency($employeePayroll, $period);
        }

        $periodStart = Carbon::parse($period->input_period_start);
        $periodEnd = Carbon::parse($period->input_period_end);

        // Get total working days in the period
        $totalWorkingDays = $this->getWorkingDaysInPeriod($periodStart, $periodEnd);

        $totalSalary = 0;

        // Get the salary that was effective BEFORE any changes in this period
        // This should be the salary that was active at the period start
        $currentSalary = $this->payrollChangeService->getSalaryOnDate(
            $employeePayroll->employee_id,
            $period->input_period_start->format('Y-m-d')
        );



        $currentSegmentStart = $periodStart;
        $segments = [];

        foreach ($salaryChanges as $change) {
            $changeDate = Carbon::parse($change->effective_date);

            // Skip changes that happened before our period start
            if ($changeDate->lt($periodStart)) {
                // But update the current salary to reflect this pre-period change
                $currentSalary = $change->new_salary;
                continue;
            }

            if ($changeDate->gt($periodEnd)) {
                break;
            }

            // Calculate working days for current salary (before change)
            $segmentWorkingDays = $this->getWorkingDaysInPeriod($currentSegmentStart, $changeDate->copy()->subDay());

            if ($segmentWorkingDays > 0) {
                $segmentSalary = $this->calculateSegmentSalary(
                    $currentSalary, // This should be the OLD salary for this segment
                    $segmentWorkingDays,
                    $employeePayroll->income_frequency,
                    $totalWorkingDays,
                    $period
                );

                $totalSalary += $segmentSalary;

                $segments[] = [
                    'salary' => $currentSalary,
                    'working_days' => $segmentWorkingDays,
                    'segment_salary' => $segmentSalary,
                    'start_date' => $currentSegmentStart->format('Y-m-d'),
                    'end_date' => $changeDate->copy()->subDay()->format('Y-m-d'),
                    'salary_type' => 'OLD_SALARY' // Debug flag
                ];
            }

            // NOW update to the new salary for the next segment
            $currentSalary = $change->new_salary;
            $currentSegmentStart = $changeDate;
        }

        // Add final segment with the NEW salary
        $finalSegmentWorkingDays = $this->getWorkingDaysInPeriod($currentSegmentStart, $periodEnd);

        if ($finalSegmentWorkingDays > 0) {
            $finalSegmentSalary = $this->calculateSegmentSalary(
                $currentSalary, // This should be the NEW salary for this segment
                $finalSegmentWorkingDays,
                $employeePayroll->income_frequency,
                $totalWorkingDays,
                $period
            );

            $totalSalary += $finalSegmentSalary;

            $segments[] = [
                'salary' => $currentSalary,
                'working_days' => $finalSegmentWorkingDays,
                'segment_salary' => $finalSegmentSalary,
                'start_date' => $currentSegmentStart->format('Y-m-d'),
                'end_date' => $periodEnd->format('Y-m-d'),
                'salary_type' => 'NEW_SALARY' // Debug flag
            ];
        }

        return $totalSalary;
    }

    /**
     * Calculate prorated salary for terminated employee based on days worked
     */
    private function calculateTerminatedEmployeeSalary(EmployeePayroll $employeePayroll, PayrollPeriod $period, $termination)
    {
        $periodStart = Carbon::parse($period->input_period_start);
        $periodEnd = Carbon::parse($period->input_period_end);
        $terminationDate = Carbon::parse($termination->termination_date);

        // Calculate working days from period start to termination date (inclusive)
        $daysWorked = $this->getWorkingDaysInPeriod($periodStart, $terminationDate);

        // Get total working days in the period
        $totalWorkingDays = $this->getWorkingDaysInPeriod($periodStart, $periodEnd);

        // Get the employee's basic salary
        $basicSalary = $employeePayroll->basic_salary;

        // Calculate prorated salary based on frequency
        $proratedSalary = 0;

        switch ($employeePayroll->income_frequency) {
            case 'daily':
                // For daily wage employees: daily rate × days worked
                $proratedSalary = $basicSalary * $daysWorked;
                break;

            case 'weekly':
                // For weekly: (weekly salary / 5 working days) × days worked
                $dailyRate = $basicSalary / 5;
                $proratedSalary = $dailyRate * $daysWorked;
                break;

            case 'monthly':
                // For monthly: (monthly salary / total working days) × days worked
                $standardMonthlyWorkingDays = $this->getStandardWorkingDaysInMonth($period);
                if ($standardMonthlyWorkingDays > 0) {
                    $dailyRate = $basicSalary / $standardMonthlyWorkingDays;
                    $proratedSalary = $dailyRate * $daysWorked;
                } else {
                    $proratedSalary = $basicSalary; // Fallback
                }
                break;

            default:
                // Default proration
                if ($totalWorkingDays > 0) {
                    $proratedSalary = ($basicSalary / $totalWorkingDays) * $daysWorked;
                } else {
                    $proratedSalary = $basicSalary;
                }
                break;
        }

        \Log::info('Terminated employee salary calculation', [
            'employee_id' => $employeePayroll->employee_id,
            'termination_date' => $terminationDate->format('Y-m-d'),
            'period_start' => $periodStart->format('Y-m-d'),
            'period_end' => $periodEnd->format('Y-m-d'),
            'days_worked' => $daysWorked,
            'total_working_days' => $totalWorkingDays,
            'basic_salary' => $basicSalary,
            'prorated_salary' => round($proratedSalary, 2)
        ]);

        return round($proratedSalary, 2);
    }

    /**
     * Calculate salary for a specific segment/period
     */
    /**
     * Calculate salary for a specific segment/period
     */
    private function calculateSegmentSalary($salary, $workingDaysInSegment, $frequency, $totalWorkingDaysInPeriod, PayrollPeriod $period)
    {
        switch ($frequency) {
            case 'daily':
                return $salary * $workingDaysInSegment;

            case 'weekly':
                $segmentWeeks = $workingDaysInSegment / 5; // Assuming 5 working days per week
                return $salary * $segmentWeeks;

            case 'monthly':
                // For monthly salary, prorate based on working days ratio
                // Get standard working days in a full month for this period type
                $standardMonthlyWorkingDays = $this->getStandardWorkingDaysInMonth($period);

                // If we can't determine standard working days, fallback to the actual working days in period
                if ($standardMonthlyWorkingDays <= 0) {
                    $standardMonthlyWorkingDays = $totalWorkingDaysInPeriod;
                }

                // Calculate prorated salary: (salary / standard_monthly_working_days) * working_days_in_segment
                $proratedSalary = ($salary / $standardMonthlyWorkingDays) * $workingDaysInSegment;
                return $proratedSalary;

            default:
                // Default proration based on working days in period
                return ($salary / $totalWorkingDaysInPeriod) * $workingDaysInSegment;
        }
    }

    /**
     * Get standard working days in a month for the payroll period
     */
    private function getStandardWorkingDaysInMonth(PayrollPeriod $period)
    {
        // Try to get working days from the attendance repository first
        $periodStart = Carbon::parse($period->input_period_start);
        $periodEnd = Carbon::parse($period->input_period_end);

        $workingDays = $this->attendanceRepository->new_number_of_working_days_date($periodStart, $periodEnd);

        if (is_array($workingDays) && count($workingDays) > 0) {
            return count($workingDays);
        }

        // Fallback: calculate typical working days (excluding weekends)
        // For a typical month, assume around 21-22 working days
        $daysInMonth = $periodStart->daysInMonth;
        $weekends = 0;
        $current = $periodStart->copy()->startOfMonth();

        while ($current->month == $periodStart->month) {
            if ($current->isWeekend()) {
                $weekends++;
            }
            $current->addDay();
        }

        return $daysInMonth - $weekends;
    }

    /**
     * Get salary changes during a specific period
     */
    private function getSalaryChangesDuringPeriod($employeeId, $startDate, $endDate)
    {
        return $this->payrollChangeService->getSalaryChangesDuringPeriod($employeeId, $startDate, $endDate);
    }


    private function getSalarySegmentsInfo(EmployeePayroll $employeePayroll, PayrollPeriod $period)
    {
        $salaryChanges = $this->getSalaryChangesDuringPeriod(
            $employeePayroll->employee_id,
            $period->input_period_start->format('Y-m-d'),
            $period->input_period_end->format('Y-m-d')
        );

        $segments = [];
        $periodStart = Carbon::parse($period->input_period_start);
        $periodEnd = Carbon::parse($period->input_period_end);

        // Get salary at the START of the period
        $currentSalary = $this->payrollChangeService->getSalaryOnDate(
            $employeePayroll->employee_id,
            $period->input_period_start->format('Y-m-d')
        );

        $currentSegmentStart = $periodStart;

        foreach ($salaryChanges as $change) {
            $changeDate = Carbon::parse($change->effective_date);

            // Skip changes before period start
            if ($changeDate->lt($periodStart)) {
                $currentSalary = $change->new_salary;
                continue;
            }

            // Break if change is after period end
            if ($changeDate->gt($periodEnd)) {
                break;
            }

            $segmentWorkingDays = $this->getWorkingDaysInPeriod($currentSegmentStart, $changeDate->copy()->subDay());

            if ($segmentWorkingDays > 0) {
                $segments[] = [
                    'salary' => $currentSalary,
                    'start_date' => $currentSegmentStart->format('Y-m-d'),
                    'end_date' => $changeDate->copy()->subDay()->format('Y-m-d'),
                    'working_days' => $segmentWorkingDays,
                    'calendar_days' => $currentSegmentStart->diffInDays($changeDate)
                ];
            }

            $currentSalary = $change->new_salary;
            $currentSegmentStart = $changeDate;
        }

        // Add final segment
        $finalSegmentWorkingDays = $this->getWorkingDaysInPeriod($currentSegmentStart, $periodEnd);
        if ($finalSegmentWorkingDays > 0) {
            $segments[] = [
                'salary' => $currentSalary,
                'start_date' => $currentSegmentStart->format('Y-m-d'),
                'end_date' => $periodEnd->format('Y-m-d'),
                'working_days' => $finalSegmentWorkingDays,
                'calendar_days' => $currentSegmentStart->diffInDays($periodEnd) + 1
            ];
        }

        return $segments;
    }
    /**
     * Calculate basic salary based on frequency (original method - used when no changes)
     */
    private function calculateBasicSalaryByFrequency(EmployeePayroll $employeePayroll, PayrollPeriod $period)
    {
        $originalBasicSalary = $employeePayroll->basic_salary;

        // Check the income frequency
        switch ($employeePayroll->income_frequency) {
            case 'daily':
                // For daily frequency, multiply basic salary by number of working days
                $workingDaysInPeriod = $this->getWorkingDaysInMonth($period);
                return $originalBasicSalary * $workingDaysInPeriod;

            case 'weekly':
                // For weekly frequency, multiply basic salary by number of weeks in payroll period
                $weeksInPeriod = $this->getWeeksInPeriod($period);
                return $originalBasicSalary * $weeksInPeriod;

            case 'monthly':
                // For monthly frequency, multiply basic salary by number of months in payroll period
                $monthsInPeriod = $this->getMonthsInPeriod($period);
                return $originalBasicSalary * $monthsInPeriod;

            default:
                // Fallback to original basic salary if frequency is not recognized
                return $originalBasicSalary;
        }
    }

    /**
     * Get units for basic income based on frequency
     */
    /**
     * Get units for basic income based on frequency
     */
    private function getBasicIncomeUnits(EmployeePayroll $employeePayroll, PayrollPeriod $period)
    {
        // Check the income frequency
        switch ($employeePayroll->income_frequency) {
            case 'daily':
                return $this->getWorkingDaysInPeriod(
                    Carbon::parse($period->input_period_start),
                    Carbon::parse($period->input_period_end)
                );

            case 'weekly':
                $workingDays = $this->getWorkingDaysInPeriod(
                    Carbon::parse($period->input_period_start),
                    Carbon::parse($period->input_period_end)
                );
                return round($workingDays / 5, 2); // Assuming 5 working days per week

            case 'monthly':
                return $this->getMonthsInPeriod($period);

            default:
                return 1;
        }
    }

    /**
     * Calculate number of weeks in the payroll period
     */
    private function getWeeksInPeriod(PayrollPeriod $period)
    {
        $start = Carbon::parse($period->input_period_start)->startOfDay();
        $end = Carbon::parse($period->input_period_end)->endOfDay();

        // Calculate the number of days and convert to weeks (rounded to 2 decimal places)
        $totalDays = $start->diffInDays($end) + 1; // +1 to include both start and end dates
        $weeks = round($totalDays / 7, 2);

        // Ensure minimum of 1 week for short periods
        return max($weeks, 1);
    }

    /**
     * Calculate number of months in the payroll period
     */
    private function getMonthsInPeriod(PayrollPeriod $period)
    {
        $start = Carbon::parse($period->input_period_start)->startOfDay();
        $end = Carbon::parse($period->input_period_end)->endOfDay();

        // Calculate months difference
        $months = $start->diffInMonths($end);

        // Ensure minimum of 1 month for periods less than a month
        return max($months, 1);
    }

    /**
     * Calculate allowances for an employee
     */
    private function getAHLRelief($housingLevy)
    {
        $ahlRelief = 0.15 *  $housingLevy;
        return $ahlRelief;
    }

    public static function calculateInsuranceRelief_SHIF($SHIFAmount)
    {
        $insuranceRelief = 0.15 * $SHIFAmount;
        return $insuranceRelief;
    }

    public static function calculateInsuranceRelief_All($insuranceDeductions)
    {
        $insuranceRelief = 0;
        $totalAmount = array_sum(array_column($insuranceDeductions, 'amount'));
        $insuranceRelief = 0.15 * $totalAmount;


        if ($insuranceRelief > 5000) {
            $insuranceRelief = 5000;
        }

        return $insuranceRelief;
    }

    private function calculateAllowances(EmployeePayroll $employeePayroll)
    {
        $allowances = [];
        $activeAllowances = $employeePayroll->getAllActiveEarnings();

        foreach ($activeAllowances as $allowance) {
            $amount = $allowance->calculateEarningAmount($employeePayroll->basic_salary);

            // For daily_rate calculations, use the actual units (days), otherwise default to 1
            $units = ($allowance->calculation_type === 'daily_rate') ? ($allowance->units ?? 1) : 1;
            // For daily_rate, the rate is the daily rate; for percentage, use the percentage
            $rate = ($allowance->calculation_type === 'daily_rate') ? ($allowance->rate ?? 0) : ($allowance->percentage ?? 0);

            $allowances[] = [
                'name' => $allowance->payrollEarningType ? $allowance->payrollEarningType->name : 'N/A',
                'code' => $allowance->allowanceType->code ?? 'custom',
                'type_id' => $allowance->payroll_earning_type_id ?? null,
                'amount' => $amount,
                'units' => $units,
                'is_taxable' => $allowance->is_taxable,
                'is_pensionable' => $allowance->is_pensionable,
                'calculation_basis' => $employeePayroll->basic_salary,
                'rate' => $rate,
                'metadata' => [
                    'allowance_type' => $allowance->allowanceType->code ?? 'custom',
                    'percentage' => $allowance->percentage ?? 0,
                    'calculation_type' => $allowance->calculation_type,
                    'units' => $allowance->units ?? null,
                    'rate' => $allowance->rate ?? null
                ]
            ];
        }

        return $allowances;
    }

    private function calculateOvertimeEarnings(EmployeePayroll $employeePayroll, PayrollPeriod $period)
    {
        $overtimeEarnings = [];
        $basicSalary = $employeePayroll->basic_salary;

        $workingDaysInMonth = $this->getWorkingDaysInMonth($period);
        $dailyRate = $basicSalary / $workingDaysInMonth;
        $hourlyRate = $dailyRate / 8;

        // Format rates for display
        $formattedDailyRate = number_format($dailyRate, 2);
        $formattedHourlyRate = number_format($hourlyRate, 2);

        // Get overtime records
        $overtimeRecords = EmployeeOvertime::where('employee_id', $employeePayroll->employee_id)
            ->where('month_year', $period->start_date->format('Y-m'))
            ->get();

        foreach ($overtimeRecords as $overtime) {
            // Weekday Overtime - Separate entries for days and hours
            if ($weekdayDays = $overtime->weekday_days_total ?? 0) {
                $amount = $dailyRate * ($employeePayroll->overtime_rate_normal ?? 1.5) * $weekdayDays;
                $rate = $employeePayroll->overtime_rate_normal ?? 1.5;
                $overtimeEarnings[] = $this->buildOvertimeArray(
                    "Weekday Overtime -Days( {$weekdayDays} day(s) × {$rate}x rate",
                    'overtime_weekday_days',
                    $amount,
                    $rate,
                    $weekdayDays,
                    'weekday',
                    'days',
                    [
                        'hours' => 0,
                        'days' => $weekdayDays,
                        'daily_rate' => $dailyRate,
                        'hourly_rate' => $hourlyRate,
                        'calculation_note' => "{$weekdayDays} day(s) × {$formattedDailyRate} × {$rate}x"
                    ]
                );
            }

            if ($weekdayHours = $overtime->weekday_hours_total ?? 0) {
                $amount = $hourlyRate * ($employeePayroll->overtime_rate_normal ?? 1.5) * $weekdayHours;
                $rate = $employeePayroll->overtime_rate_normal ?? 1.5;
                $overtimeEarnings[] = $this->buildOvertimeArray(
                    "Weekday Overtime - {$weekdayHours} hour(s) × {$rate}x rate",
                    'overtime_weekday_hours',
                    $amount,
                    $rate,
                    $weekdayHours,
                    'weekday',
                    'hours',
                    [
                        'hours' => $weekdayHours,
                        'days' => 0,
                        'daily_rate' => $dailyRate,
                        'hourly_rate' => $hourlyRate,
                        'calculation_note' => "{$weekdayHours} hour(s) × {$formattedHourlyRate} × {$rate}x"
                    ]
                );
            }

            // Weekend Overtime - Separate entries for days and hours
            if ($weekendDays = $overtime->weekend_days_totals ?? 0) {
                $amount = $dailyRate * ($employeePayroll->overtime_rate_weekend ?? 2.0) * $weekendDays;
                $rate = $employeePayroll->overtime_rate_weekend ?? 2.0;
                $overtimeEarnings[] = $this->buildOvertimeArray(
                    "Weekend Overtime - {$weekendDays} day(s) × {$rate}x rate",
                    'overtime_weekend_days',
                    $amount,
                    $rate,
                    $weekendDays,
                    'weekend',
                    'days',
                    [
                        'hours' => 0,
                        'days' => $weekendDays,
                        'daily_rate' => $dailyRate,
                        'hourly_rate' => $hourlyRate,
                        'calculation_note' => "{$weekendDays} day(s) × {$formattedDailyRate} × {$rate}x"
                    ]
                );
            }

            if ($weekendHours = $overtime->weekend_hours_totals ?? 0) {
                $amount = $hourlyRate * ($employeePayroll->overtime_rate_weekend ?? 2.0) * $weekendHours;
                $rate = $employeePayroll->overtime_rate_weekend ?? 2.0;
                $overtimeEarnings[] = $this->buildOvertimeArray(
                    "Weekend Overtime - {$weekendHours} hour(s) × {$rate}x rate",
                    'overtime_weekend_hours',
                    $amount,
                    $rate,
                    $weekendHours,
                    'weekend',
                    'hours',
                    [
                        'hours' => $weekendHours,
                        'days' => 0,
                        'daily_rate' => $dailyRate,
                        'hourly_rate' => $hourlyRate,
                        'calculation_note' => "{$weekendHours} hour(s) × {$formattedHourlyRate} × {$rate}x"
                    ]
                );
            }

            // Holiday Overtime - Separate entries for days and hours
            if ($holidayDays = $overtime->public_holiday_days_totals ?? 0) {
                $amount = $dailyRate * ($employeePayroll->overtime_rate_holiday ?? 2.0) * $holidayDays;
                $rate = $employeePayroll->overtime_rate_holiday ?? 2.0;
                $overtimeEarnings[] = $this->buildOvertimeArray(
                    "Holiday Overtime - {$holidayDays} day(s) × {$rate}x rate",
                    'overtime_holiday_days',
                    $amount,
                    $rate,
                    $holidayDays,
                    'holiday',
                    'days',
                    [
                        'hours' => 0,
                        'days' => $holidayDays,
                        'daily_rate' => $dailyRate,
                        'hourly_rate' => $hourlyRate,
                        'calculation_note' => "{$holidayDays} day(s) × {$formattedDailyRate} × {$rate}x"
                    ]
                );
            }

            if ($holidayHours = $overtime->public_holiday_hours_totals ?? 0) {
                $amount = $hourlyRate * ($employeePayroll->overtime_rate_holiday ?? 2.0) * $holidayHours;
                $rate = $employeePayroll->overtime_rate_holiday ?? 2.0;
                $overtimeEarnings[] = $this->buildOvertimeArray(
                    "Holiday Overtime - {$holidayHours} hour(s) × {$rate}x rate",
                    'overtime_holiday_hours',
                    $amount,
                    $rate,
                    $holidayHours,
                    'holiday',
                    'hours',
                    [
                        'hours' => $holidayHours,
                        'days' => 0,
                        'daily_rate' => $dailyRate,
                        'hourly_rate' => $hourlyRate,
                        'calculation_note' => "{$holidayHours} hour(s) × {$formattedHourlyRate} × {$rate}x"
                    ]
                );
            }
        }

        // Process manual overtime entries
        foreach ($employeePayroll->getAllActiveEarnings()->filter($this->isOvertimeEarning()) as $earning) {
            $days = $earning->days ?? 0;
            $hours = $earning->hours ?? 0;
            $rate = $earning->units ?? 0;

            // Create separate entries for days and hours if both exist
            if ($days > 0) {
                $daysAmount = $dailyRate * $rate * $days;
                $overtimeEarnings[] = [
                    'name' => $this->getOvertimeName($earning->calculation_type) . " - {$days} day(s) × {$rate}x rate",
                    'code' => $earning->calculation_type . '_days',
                    'amount' => $daysAmount,
                    'units' => $days,
                    'is_taxable' => $earning->is_taxable,
                    'is_pensionable' => $earning->is_pensionable,
                    'calculation_basis' => $basicSalary,
                    'rate' => $rate,
                    'type' => 'manual',
                    'calculation_type' => 'days',
                    'metadata' => [
                        'overtime_type' => 'manual',
                        'calculation_basis' => 'days',
                        'days' => $days,
                        'hours' => 0,
                        'daily_rate' => $dailyRate,
                        'hourly_rate' => $hourlyRate,
                        'calculation_note' => "{$days} day(s) × {$formattedDailyRate} × {$rate}x"
                    ]
                ];
            }

            if ($hours > 0) {
                $hoursAmount = $hourlyRate * $rate * $hours;
                $overtimeEarnings[] = [
                    'name' => $this->getOvertimeName($earning->calculation_type) . " - {$hours} hour(s) × {$rate}x rate",
                    'code' => $earning->calculation_type . '_hours',
                    'amount' => $hoursAmount,
                    'units' => $hours,
                    'is_taxable' => $earning->is_taxable,
                    'is_pensionable' => $earning->is_pensionable,
                    'calculation_basis' => $basicSalary,
                    'rate' => $rate,
                    'type' => 'manual',
                    'calculation_type' => 'hours',
                    'metadata' => [
                        'overtime_type' => 'manual',
                        'calculation_basis' => 'hours',
                        'days' => 0,
                        'hours' => $hours,
                        'daily_rate' => $dailyRate,
                        'hourly_rate' => $hourlyRate,
                        'calculation_note' => "{$hours} hour(s) × {$formattedHourlyRate} × {$rate}x"
                    ]
                ];
            }
        }

        return $overtimeEarnings;
    }

    // Updated helper function to include calculation type
    private function buildOvertimeArray($name, $code, $amount, $rate, $quantity, $type, $calculationType = 'days')
    {
        return [
            'name' => $name,
            'code' => $code,
            'amount' => $amount,
            'units' => $quantity,
            'is_taxable' => true,
            'is_pensionable' => true,
            'calculation_basis' => null,
            'rate' => $rate,
            'type' => $type,
            'calculation_type' => $calculationType,
            'metadata' => [
                'overtime_type' => $type,
                'calculation_basis' => $calculationType,
                'quantity' => $quantity
            ]
        ];
    }

    private function isOvertimeEarning()
    {
        return function ($earning) {
            return in_array($earning->calculation_type, [
                OvertimeCalculationType::OVERTIME_1,
                OvertimeCalculationType::OVERTIME_1_5,
                OvertimeCalculationType::OVERTIME_2
            ]);
        };
    }

    private function getOvertimeName($calculationType)
    {
        return match ($calculationType) {
            OvertimeCalculationType::OVERTIME_1 => 'Overtime 1x',
            OvertimeCalculationType::OVERTIME_1_5 => 'Overtime 1.5x',
            OvertimeCalculationType::OVERTIME_2 => 'Overtime 2x',
            default => 'Overtime'
        };
    }

    /**
     * Get working days in a month for overtime calculations
     */
    private function getWorkingDaysInMonth(PayrollPeriod $period)
    {
        $start = Carbon::parse($period->input_period_start)->startOfDay();
        $end = Carbon::parse($period->input_period_end)->endOfDay();

        // Validate dates
        if ($start->gt($end)) {
            return 22; // Fallback if dates are invalid
        }

        $workingDays11 = $this->attendanceRepository->new_number_of_working_days_date($start, $end);
        return $workingDays11 > 0 ? count($workingDays11) : 22;
    }

    /**
     * Calculate taxable income
     */
    private function calculateTaxableIncome($grossSalary, $allowances, $nssfContribution, $shifContribution, $housingLevy, $pensionContribution)
    {
        $nonTaxableAmount = 0;
        $taxablePay = 0;
        $totalPensionContribution = $nssfContribution + $pensionContribution;

        foreach ($allowances as $allowance) {
            if (!$allowance['is_taxable']) {
                $nonTaxableAmount += $allowance['amount'];
            }
        }

        // Cap pension contribution at 30,000
        $cappedPensionContribution = min($totalPensionContribution, 30000);

        $taxablePay = $grossSalary - ($housingLevy + $shifContribution + $nonTaxableAmount + $cappedPensionContribution);
        return $taxablePay;
    }

    /**
     * Calculate pensionable pay
     */
    private function calculatePensionablePay($basicSalary, $allowances)
    {
        $pensionableAmount = 0;
        //removed to ensure only basic salary is considered for pensionable pay

        // foreach ($allowances as $allowance) {
        //     if ($allowance['is_pensionable']) {
        //         $pensionableAmount += $allowance['amount'];
        //     }
        // }
        return $pensionableAmount + $basicSalary;
    }

    /**
     * Calculate PAYE tax according to KRA rates
     */
    private function calculatePayeTax($taxableIncome, EmployeePayroll $employeePayroll, $insuranceRelief_all)
    {


        if ($employeePayroll->tax_status === 'exempt') {
            return 0;
        }
        $total_tax = 0;
        $personalRelief = PayrollConfiguration::getPersonalRelief();
        $totalRelief = $personalRelief  + $insuranceRelief_all;
        $payeBands = PayrollConfiguration::getPayeBands();

        $remainingIncome = $taxableIncome;

        foreach ($payeBands as $band) {
            $bandMin = $band['min'];
            $bandMax = $band['max'] ?? PHP_INT_MAX;
            $rate = $band['rate'];

            if ($remainingIncome <= 0) {
                break;
            }

            $bandWidth = $bandMax - $bandMin + 1;
            $taxableInBand = min($remainingIncome, $bandWidth);

            if ($taxableIncome > $bandMin) {
                $total_tax += $taxableInBand * $rate;
                $remainingIncome -= $taxableInBand;
            }
        }
        // Apply personal relief
        if ($total_tax < ($totalRelief)) {
            $total_tax = 0;
        } else {
            $total_tax = $total_tax - ($totalRelief);
        }

        // Apply disability exemption if applicable
        if ($employeePayroll->disability_exemption) {
            $total_tax = max(0, $total_tax - 2400);
        }
        return round($total_tax, 2);
    }


    /**
     * Calculate NSSF contribution with tier breakdown
     */
    private function calculateNssfContribution($employeeDetails, $grossSalary, $payrollPeriod = null)
    {
        $payrollPeriodStart = $payrollPeriod ? $payrollPeriod->start_date->format('Y-m-d') : null;
        // Check if employee is over 60 years old at the start of payroll period
        if (isset($employeeDetails->date_of_birth) && !empty($employeeDetails->date_of_birth)) {
            $dateOfBirth = new DateTime($employeeDetails->date_of_birth);

            // Use payroll period start date if provided, otherwise use current date
            if ($payrollPeriodStart) {
                $referenceDate = new DateTime($payrollPeriodStart);
            } else {
                $referenceDate = new DateTime();
            }

            $age = $referenceDate->diff($dateOfBirth)->y;

            if ($age > 60) {
                return [
                    'total' => '0.00',
                    'tier1' => '0.00',
                    'tier2' => '0.00'
                ];
            }
        }

        $nssf_tier1 = 0;
        $nssf_tier2 = 0;
        $total_nssf = 0;

        if ($employeeDetails->nssf_rate_type == '2') {
            if ($grossSalary >= 72000) {
                $nssf_tier1 = 480;
                $nssf_tier2 = 3840.00;
                $total_nssf = $nssf_tier1 + $nssf_tier2;
            } elseif (8000 < $grossSalary && $grossSalary < 72000) {
                $nssf_tier1 = 480;
                $nssf_tier2 = (0.06 * $grossSalary) - 480;
                $total_nssf = $nssf_tier1 + $nssf_tier2;
            } else {
                $total_nssf = (0.06 * $grossSalary);
                // For lower incomes, all goes to tier 1
                $nssf_tier1 = $total_nssf;
                $nssf_tier2 = 0;
            }
        } elseif ($employeeDetails->nssf_rate_type == '1') {
            $total_nssf = 200;
            $nssf_tier1 = 200;
            $nssf_tier2 = 0;
        } elseif ($employeeDetails->nssf_rate_type == '3') {
            $total_nssf = 480;
            $nssf_tier1 = 480;
            $nssf_tier2 = 0;
        } else {
            //no deduction
            $total_nssf = 0;
            $nssf_tier1 = 0;
            $nssf_tier2 = 0;
        }

        return [
            'total' => number_format((float)$total_nssf, 2, '.', ''),
            'tier1' => number_format((float)$nssf_tier1, 2, '.', ''),
            'tier2' => number_format((float)$nssf_tier2, 2, '.', '')
        ];
    }

    /**
     * Calculate SHIF contribution
     */
    private function calculateShifContribution($grossSalary)
    {
        return $this->calculateSHIF($grossSalary);
    }

    /**
     * Calculate Housing Levy
     */
    private function calculateHousingLevy($grossSalary)
    {
        $housingLevyRate = PayrollConfiguration::getHousingLevyRate();
        $HousingLevy = ($housingLevyRate * $grossSalary);
        if ($HousingLevy < 300) {
            $HousingLevy = 300;
        }
        return round($grossSalary * $housingLevyRate, 2);
    }

    /**
     * Calculate pension contribution
     */
    private function calculatePensionContribution(EmployeePayroll $employeePayroll, $pensionablePay)
    {
        $totalContribution = 0;

        // Check if employee has multiple pension schemes
        if ($employeePayroll->pensionSchemes->count() > 0) {
            foreach ($employeePayroll->pensionSchemes as $scheme) {
                $employeeRate = $scheme->pivot->employee_rate / 100;
                $contribution = $pensionablePay * $employeeRate;

                // Apply min/max limits from scheme
                if ($scheme->minimum_contribution && $contribution < $scheme->minimum_contribution) {
                    $contribution = $scheme->minimum_contribution;
                }
                if ($scheme->maximum_contribution && $contribution > $scheme->maximum_contribution) {
                    $contribution = $scheme->maximum_contribution;
                }

                $totalContribution += $contribution;
            }
        } elseif ($employeePayroll->pensionScheme) {
            // Fallback to legacy single scheme
            // $totalContribution = $employeePayroll->pensionScheme->calculateEmployeeContribution($pensionablePay);
        } else {
            $totalContribution = 0;
        }

        return $totalContribution;
    }

    /**
     * Calculate employer pension contribution
     */
    private function calculateEmployerPensionContribution(EmployeePayroll $employeePayroll, $pensionablePay)
    {
        $totalContribution = 0;

        // Check if employee has multiple pension schemes
        if ($employeePayroll->pensionSchemes->count() > 0) {
            foreach ($employeePayroll->pensionSchemes as $scheme) {
                $employerRate = $scheme->pivot->employer_rate / 100;
                $contribution = $pensionablePay * $employerRate;

                // Apply min/max limits from scheme
                if ($scheme->minimum_contribution && $contribution < $scheme->minimum_contribution) {
                    $contribution = $scheme->minimum_contribution;
                }
                if ($scheme->maximum_contribution && $contribution > $scheme->maximum_contribution) {
                    $contribution = $scheme->maximum_contribution;
                }

                $totalContribution += $contribution;
            }
        } elseif ($employeePayroll->pensionScheme) {
            // Fallback to legacy single scheme
            // $totalContribution = $employeePayroll->pensionScheme->calculateEmployerContribution($pensionablePay);
        } else {
            $totalContribution = 0;
        }

        return $totalContribution;
    }

    /**
     * Calculate Industrial Training Levy (Company Contribution)
     */
    private function calculateIndustrialTrainingLevy($employeePayroll)
    {
        $deduction = $employeePayroll->deductions()
            ->whereHas('payrollDeductionType', function ($query) {
                $query->where('name', 'NITA')
                    ->orWhere('code', 'NITA');
            })
            ->first();

        if ($deduction) {
            return round($deduction->amount, 2);
        }

        return 0;
    }

    /**
     * Calculate NSSF Tier 1 Company Contribution
     */
    private function calculateNssfTier1Company($employeeDetails, $pensionablePay, $payrollPeriod = null)
    {
        $payrollPeriodStart = $payrollPeriod ? $payrollPeriod->start_date->format('Y-m-d') : null;
        // Check if employee is over 60 years old at the start of payroll period
        if (isset($employeeDetails->date_of_birth) && !empty($employeeDetails->date_of_birth)) {
            $dateOfBirth = new DateTime($employeeDetails->date_of_birth);

            // Use payroll period start date if provided, otherwise use current date
            if ($payrollPeriodStart) {
                $referenceDate = new DateTime($payrollPeriodStart);
            } else {
                $referenceDate = new DateTime();
            }

            $age = $referenceDate->diff($dateOfBirth)->y;

            if ($age > 60) {
                return '0.00';
            }
        }
        $tier1Rate = 0.06;
        $nssf_tier1 = 0;
        $nssf_tier2 = 0;
        $total_nssf = 0;

        if ($employeeDetails->nssf_rate_type == '2') {
            if ($pensionablePay >= 72000) {
                $nssf_tier1 = 480;
                $nssf_tier2 = 3840.00;
                $total_nssf = $nssf_tier1 + $nssf_tier2;
            } elseif (8000 < $pensionablePay && $pensionablePay < 72000) {
                $nssf_tier1 = 480;
                $nssf_tier2 = (0.06 * $pensionablePay) - 480;
                $total_nssf = $nssf_tier1 + $nssf_tier2;
            } else {
                $nssf_tier1 = 480;
                $total_nssf = (0.06 * $pensionablePay);
            }
        } elseif ($employeeDetails->nssf_rate_type == '1') {
            $total_nssf = 200;
        } elseif ($employeeDetails->nssf_rate_type == '3') {
            $total_nssf = 480;
        } else {
            //no deduction
            $total_nssf = 0;
        }

        return round($nssf_tier1, 2);
    }

    /**
     * Calculate NSSF Tier 2 Company Contribution
     */
    private function calculateNssfTier2Company($employeeDetails, $grossSalary, $payrollPeriod = null)
    {
        $pensionablePay = $grossSalary;
        $payrollPeriodStart = $payrollPeriod ? $payrollPeriod->start_date->format('Y-m-d') : null;
        // Check if employee is over 60 years old at the start of payroll period
        if (isset($employeeDetails->date_of_birth) && !empty($employeeDetails->date_of_birth)) {
            $dateOfBirth = new DateTime($employeeDetails->date_of_birth);

            // Use payroll period start date if provided, otherwise use current date
            if ($payrollPeriodStart) {
                $referenceDate = new DateTime($payrollPeriodStart);
            } else {
                $referenceDate = new DateTime();
            }

            $age = $referenceDate->diff($dateOfBirth)->y;

            if ($age > 60) {
                return '0.00';
            }
        }
        $tier1Rate = 0.06;
        $nssf_tier1 = 0;
        $nssf_tier2 = 0;
        $total_nssf = 0;

        if ($employeeDetails->nssf_rate_type == '2') {
            if ($pensionablePay >= 72000) {
                $nssf_tier1 = 480;
                $nssf_tier2 = 3840.00;
                $total_nssf = $nssf_tier1 + $nssf_tier2;
            } elseif (8000 < $pensionablePay && $pensionablePay < 72000) {
                $nssf_tier1 = 480;
                $nssf_tier2 = (0.06 * $pensionablePay) - 480;
                $total_nssf = $nssf_tier1 + $nssf_tier2;
            } else {
                $total_nssf = (0.06 * $pensionablePay);
            }
        } elseif ($employeeDetails->nssf_rate_type == '1') {
            $total_nssf = 200;
        } elseif ($employeeDetails->nssf_rate_type == '3') {
            $total_nssf = 480;
        } else {
            //no deduction
            $total_nssf = 0;
        }

        return round($nssf_tier2, 2);
    }

    /**
     * Calculate Housing Levy Company Contribution
     */
    private function calculateHousingLevyCompany($grossSalary)
    {
        $housingLevyRate = 0.015;
        return round($grossSalary * ($housingLevyRate), 2);
    }

    /**
     * Calculate non-statutory deductions
     */
    private function calculateNonStatutoryDeductions(EmployeePayroll $employeePayroll, $grossSalary)
    {
        if ($grossSalary <= 0) {
            return [];
        }
        $deductions = [];
        $activeDeductions = $employeePayroll->getActiveDeductions(getCurrentPayrollPeriod());

        foreach ($activeDeductions as $deduction) {
            if (!($deduction->is_statutory)  || !($deduction->payrollDeductionType->name === 'Helb' || $deduction->payrollDeductionType->code === 'Helb')) {
                $isStatutory = $deduction->is_statutory;
                $isNita = $deduction->payrollDeductionType->name === 'NITA' ||
                    $deduction->payrollDeductionType->code === 'NITA';

                if ($isStatutory || $isNita) {
                    continue;
                }
                $amount = $deduction->calculateDeductionAmount($employeePayroll->basic_salary, $grossSalary);
                $deductions[] = [
                    'name' => $deduction->payrollDeductionType->name,
                    'code' => $deduction->deductionType->code ?? 'custom',
                    'type_id' => $deduction->payroll_deduction_type_id ?? null,
                    'amount' => $amount,
                    'units' => 1,
                    'calculation_basis' => $employeePayroll->basic_salary,
                    'rate' => $deduction->percentage ?? 0,
                    'metadata' => [
                        'deduction_type' => $deduction->deductionType->code ?? 'custom',
                        'percentage' => $deduction->percentage ?? 0
                    ]
                ];
            }
        }

        return $deductions;
    }

    private function getAllInsurances(EmployeePayroll $employeePayroll, $grossSalary)
    {
        if ($grossSalary <= 0) {
            return [];
        }
        $deductions = [];
        $activeDeductions = $employeePayroll->getActiveDeductions(getCurrentPayrollPeriod());

        foreach ($activeDeductions as $deduction) {
            // Check if deduction name contains "insurance" (case-insensitive)
            $deductionName = strtolower($deduction->payrollDeductionType->name);
            $hasInsuranceKeyword = str_contains($deductionName, 'insurance');

            // Only process deductions with "insurance" in the name
            if (!$hasInsuranceKeyword) {
                continue;
            }

            // Skip statutory deductions and specific types as before
            if (!($deduction->is_statutory) || !($deduction->payrollDeductionType->name === 'Helb' || $deduction->payrollDeductionType->code === 'Helb')) {
                $isStatutory = $deduction->is_statutory;
                $isNita = $deduction->payrollDeductionType->name === 'NITA' ||
                    $deduction->payrollDeductionType->code === 'NITA';

                if ($isStatutory || $isNita) {
                    continue;
                }

                $amount = $deduction->calculateDeductionAmount($employeePayroll->basic_salary, $grossSalary);
                $deductions[] = [
                    'name' => $deduction->payrollDeductionType->name,
                    'code' => $deduction->deductionType->code ?? 'custom',
                    'type_id' => $deduction->payroll_deduction_type_id ?? null,
                    'amount' => $amount,
                    'units' => 1,
                    'calculation_basis' => $employeePayroll->basic_salary,
                    'rate' => $deduction->percentage ?? 0,
                    'metadata' => [
                        'deduction_type' => $deduction->deductionType->code ?? 'custom',
                        'percentage' => $deduction->percentage ?? 0
                    ]
                ];
            }
        }

        return $deductions;
    }
    /**
     * Calculate loan deductions for current payroll period
     */
    private function calculateLoanDeductions(EmployeePayroll $employeePayroll, PayrollPeriod $period)
    {
        // If gross salary is zero, return empty deductions
        $basicEarnings = $employeePayroll->earnings()->whereHas('payrollEarningType', function ($query) {
            $query->where('earning_category', 'salary');
        })->first();

        // Only return empty if BOTH conditions fail
        if (!$basicEarnings && $employeePayroll->basic_salary <= 0) {
            return [];
        }
        $loanDeductions = [];

        // Get automatic loan deductions scheduled for this payroll period
        $scheduledLoanDeductions = LoanDeduction::where('employee_id', $employeePayroll->employee_id)
            ->where('payroll_period_id', $period->id)
            ->whereHas('loan', function ($query) {
                $query->where('approval_status', 'approved')
                      ->where('balance', '>', 0);
            })
            ->with('loan', 'loan.loanType')
            ->get();

        foreach ($scheduledLoanDeductions as $deduction) {
            $loanDeductions[] = [
                'name' => 'Loan - ' . ($deduction->loan->loanType->name ?? 'General'),
                'code' => 'loan_deduction',
                'amount' => $deduction->amount,
                'units' => 1,
                'loan_id' => $deduction->loan_id,
                'deduction_id' => $deduction->id,
                'calculation_basis' => $deduction->amount,
                'rate' => 0,
                'loan_type' => $deduction->loan->loanType->name ?? 'General',
                'deduction_date' => $deduction->deduction_date,
                'metadata' => [
                    'loan_id' => $deduction->loan_id,
                    'deduction_id' => $deduction->id,
                    'loan_type' => $deduction->loan->loanType->name ?? 'General',
                    'deduction_date' => $deduction->deduction_date->format('Y-m-d'),
                    'notes' => $deduction->notes
                ]
            ];
        }

        // Get manual loan deductions for this payroll period
        $manualLoanDeductions = ManualLoanDeduction::where('employee_id', $employeePayroll->employee_id)
            ->where('deduction_date', '>=', $period->start_date)
            ->where('deduction_date', '<=', $period->end_date)
            ->whereHas('loan', function ($query) {
                $query->where('approval_status', 'approved')
                      ->where('balance', '>', 0);
            })
            ->with('loan', 'loan.loanType')
            ->get();

        foreach ($manualLoanDeductions as $deduction) {
            $loanDeductions[] = [
                'name' => 'Loan (Manual) - ' . ($deduction->loan->loanType->name ?? 'General'),
                'code' => 'loan_deduction_manual',
                'amount' => $deduction->amount,
                'units' => 1,
                'loan_id' => $deduction->loan_id,
                'deduction_id' => $deduction->id,
                'calculation_basis' => $deduction->amount,
                'rate' => 0,
                'loan_type' => $deduction->loan->loanType->name ?? 'General',
                'deduction_date' => $deduction->deduction_date,
                'metadata' => [
                    'loan_id' => $deduction->loan_id,
                    'deduction_id' => $deduction->id,
                    'loan_type' => $deduction->loan->loanType->name ?? 'General',
                    'deduction_date' => $deduction->deduction_date->format('Y-m-d'),
                    'notes' => $deduction->notes,
                    'is_manual' => true
                ]
            ];
        }

        return $loanDeductions;
    }

    /**
     * Calculate claim recoveries for current payroll period
     */
    private function calculateClaimRecoveries(EmployeePayroll $employeePayroll, PayrollPeriod $period)
    {
        // If gross salary is zero, return empty recoveries
        if ($employeePayroll->basic_salary <= 0) {
            return [];
        }

        $recoveries = [];
        // Get pending recoveries for this employee and period
        $pendingRecoveries = PayrollClaimRecovery::with(['payrollClaim'])
            ->where('employee_id', $employeePayroll->employee_id)
            ->where('recovery_year', $period->year)
            ->where('recovery_month', $period->month)
            ->where('status', 'pending')
            ->get();

        foreach ($pendingRecoveries as $recovery) {
            $recoveries[] = [
                'name' => 'Claim Recovery - ' . $recovery->payrollClaim->claim_title,
                'code' => 'claim_recovery',
                'amount' => $recovery->scheduled_amount,
                'units' => 1,
                'recovery_id' => $recovery->id,
                'claim_id' => $recovery->payroll_claim_id,
                'installment_number' => $recovery->installment_number,
                'calculation_basis' => $recovery->scheduled_amount,
                'rate' => 0,
                'metadata' => [
                    'recovery_id' => $recovery->id,
                    'claim_id' => $recovery->claim_id,
                    'installment_number' => $recovery->installment_number
                ]
            ];
        }

        return $recoveries;
    }

    /**
     * Save payroll record details
     */
    private function savePayrollRecordDetails(PayrollRecord $payrollRecord, $allowances, $deductions, $claimRecoveries, $loanDeductions, $statutoryDeductions, $companyContributions = [])
    {
        // Clear existing details
        $payrollRecord->details()->delete();

        // Save allowances
        foreach ($allowances as $allowance) {
            PayrollRecordDetail::create([
                'payroll_record_id' => $payrollRecord->id,
                'type' => PayrollRecordDetail::TYPE_EARNING,
                'name' => $allowance['name'],
                'code' => $allowance['code'],
                'type_id' => $allowance['type_id'] ?? null,
                'amount' => $allowance['amount'],
                'units' => $allowance['units'] ?? 1,
                'calculation_basis' => $allowance['calculation_basis'],
                'rate' => $allowance['rate'],
                'is_taxable' => $allowance['is_taxable'],
                'is_pensionable' => $allowance['is_pensionable'],
                'metadata' => isset($allowance['metadata']) ? json_encode($allowance['metadata']) : null,
                'employee_id' => $payrollRecord->employee_id,
                'payroll_period_id' => $payrollRecord->payroll_period_id
            ]);
        }

        // Save non-statutory deductions
        foreach ($deductions as $deduction) {
            PayrollRecordDetail::create([
                'payroll_record_id' => $payrollRecord->id,
                'type' => PayrollRecordDetail::TYPE_DEDUCTION,
                'name' => $deduction['name'],
                'code' => $deduction['code'],
                'type_id' => $deduction['type_id'] ?? null,
                'amount' => $deduction['amount'],
                'units' => $deduction['units'] ?? 1,
                'calculation_basis' => $deduction['calculation_basis'],
                'rate' => $deduction['rate'],
                'is_taxable' => false,
                'is_pensionable' => false,
                'metadata' => isset($deduction['metadata']) ? json_encode($deduction['metadata']) : null,
                'employee_id' => $payrollRecord->employee_id,
                'payroll_period_id' => $payrollRecord->payroll_period_id
            ]);
        }

        // Save claim recoveries
        foreach ($claimRecoveries as $recovery) {
            PayrollRecordDetail::create([
                'payroll_period_id' => $payrollRecord->payroll_period_id,
                'payroll_record_id' => $payrollRecord->id,
                'type' => PayrollRecordDetail::TYPE_DEDUCTION,
                'name' => $recovery['name'],
                'code' => $recovery['code'],
                'amount' => $recovery['amount'],
                'units' => $recovery['units'] ?? 1,
                'calculation_basis' => $recovery['calculation_basis'],
                'rate' => $recovery['rate'],
                'is_taxable' => false,
                'is_pensionable' => false,
                'metadata' => isset($recovery['metadata']) ? json_encode($recovery['metadata']) : json_encode([
                    'recovery_id' => $recovery['recovery_id'],
                    'claim_id' => $recovery['claim_id'],
                    'installment_number' => $recovery['installment_number']
                ])
            ]);

            // Process the recovery automatically
            $this->processClaimRecovery($recovery['recovery_id'], $recovery['amount'], $payrollRecord->id);
        }

        // Save loan deductions
        foreach ($loanDeductions as $loan) {
            PayrollRecordDetail::create([
                'employee_id' => $payrollRecord->employee_id,
                'payroll_period_id' => $payrollRecord->payroll_period_id,
                'payroll_record_id' => $payrollRecord->id,
                'type' => PayrollRecordDetail::TYPE_DEDUCTION,
                'name' => $loan['name'],
                'code' => $loan['code'], // 'loan_deduction' or 'loan_deduction_manual'
                'amount' => $loan['amount'],
                'units' => $loan['units'] ?? 1,
                'calculation_basis' => $loan['calculation_basis'],
                'rate' => $loan['rate'],
                'is_taxable' => false,
                'is_pensionable' => false,
                'metadata' => isset($loan['metadata']) ? json_encode($loan['metadata']) : json_encode([
                    'loan_id' => $loan['loan_id'],
                    'deduction_id' => $loan['deduction_id'],
                    'loan_type' => $loan['loan_type'],
                    'deduction_date' => $loan['deduction_date']
                ])
            ]);

            // Process loan deduction - update loan balance
            $this->processLoanDeduction($loan['loan_id'], $loan['amount'], $payrollRecord->id);
        }

        // Save statutory deductions
        $statutoryItems = [
            'paye' => 'PAYE Tax',
            'nssf' => 'NSSF Contribution',
            'nssf_tier1' => 'NSSF Tier I',
            'nssf_tier2' => 'NSSF Tier II',
            'shif' => 'SHIF Contribution',
            'housing_levy' => 'Housing Levy',
            'pension' => 'Pension Contribution'
        ];

        foreach ($statutoryDeductions as $code => $amount) {
            if ($amount > 0) {
                PayrollRecordDetail::create([
                    'employee_id' => $payrollRecord->employee_id,
                    'payroll_period_id' => $payrollRecord->payroll_period_id,
                    'payroll_record_id' => $payrollRecord->id,
                    'type' => PayrollRecordDetail::TYPE_STATUTORY_DEDUCTION,
                    'name' => $statutoryItems[$code],
                    'code' => $code,
                    'amount' => $amount,
                    'units' => 1,
                    'is_taxable' => false,
                    'is_pensionable' => false,
                    'metadata' => json_encode([
                        'statutory_type' => $code,
                        'calculation_type' => 'statutory'
                    ])
                ]);
            }
        }

        // Save company contributions
        $companyContributionItems = [
            'industrial_training_levy' => 'Industrial Training Levy',
            'nssf_tier1_company' => 'NSSF Tier I (Company)',
            'nssf_tier2_company' => 'NSSF Tier II (Company)',
            'housing_levy_company' => 'Affordable Housing Levy (Company)',
            'employer_pension' => 'Pension (Employer)',
            'shif_company' => 'SHIF (Company)'
        ];

        foreach ($companyContributions as $code => $amount) {
            if ($amount > 0) {
                PayrollRecordDetail::create([
                    'employee_id' => $payrollRecord->employee_id,
                    'payroll_period_id' => $payrollRecord->payroll_period_id,
                    'payroll_record_id' => $payrollRecord->id,
                    'type' => PayrollRecordDetail::TYPE_COMPANY_CONTRIBUTION,
                    'name' => $companyContributionItems[$code],
                    'code' => $code,
                    'amount' => $amount,
                    'units' => 1,
                    'is_taxable' => false,
                    'is_pensionable' => false,
                    'metadata' => json_encode([
                        'contribution_type' => $code,
                        'calculation_type' => 'company_contribution'
                    ])
                ]);
            }
        }
    }

    /**
     * Calculate payroll for all employees in a period
     */



    public function calculatePeriodPayroll(PayrollPeriod $period, $request)
    {
        // Get active employees who HAVE an employeePayroll relation
        $employeesWithPayroll = Employee::where('status', GeneralStatus::ACTIVE)
            ->whereHas('employeePayroll', function ($query) {
                $query->where('status', GeneralStatus::ACTIVE);
            })
            ->with('employeePayroll')
            ->get();

        // Also get terminated employees who were terminated during this period
        // and haven't had their final arrears paid yet
        $terminatedEmployees = Employee::where('status', GeneralStatus::TERMINATED)
            ->whereHas('employeePayroll', function ($query) {
                $query->where('status', GeneralStatus::ACTIVE);
            })
            ->whereHas('terminations', function ($query) use ($period) {
                $query->where('status', 2) // Approved terminations
                      ->where('arrears_paid', 0) // Not yet paid
                      ->where('termination_date', '>=', $period->input_period_start->format('Y-m-d'))
                      ->where('termination_date', '<=', $period->input_period_end->format('Y-m-d'));
            })
            ->with(['employeePayroll', 'terminations' => function ($query) use ($period) {
                $query->where('status', 2)
                      ->where('arrears_paid', 0)
                      ->where('termination_date', '>=', $period->input_period_start->format('Y-m-d'))
                      ->where('termination_date', '<=', $period->input_period_end->format('Y-m-d'));
            }])
            ->get();

        // Merge the collections - use employee_id as key to avoid duplicates
        $allEmployees = $employeesWithPayroll->keyBy('employee_id');
        foreach ($terminatedEmployees as $terminatedEmployee) {
            if (!$allEmployees->has($terminatedEmployee->employee_id)) {
                $allEmployees->put($terminatedEmployee->employee_id, $terminatedEmployee);
            }
        }
        $allEmployees = $allEmployees->values();

        $results = [];

        if ($request->recalculate_existing) {
            // Get IDs of records to delete
            $recordIds = PayrollRecord::where('payroll_record_status', '!=', PayrollStatus::PAID)
                ->where('payroll_period_id', $period->id)
                ->withTrashed()
                ->pluck('id');

            if ($recordIds->isNotEmpty()) {
                // Delete related details first
                PayrollRecordDetail::whereIn('payroll_record_id', $recordIds)->delete();

                // Then delete the main records
                PayrollRecord::whereIn('id', $recordIds)->forceDelete();
            }
        }

        foreach ($allEmployees as $employee) {
            $existingRecord = PayrollRecord::where('employee_id', $employee->employee_id)
                ->where('payroll_record_status', '!=', PayrollStatus::PAID)
                ->where('payroll_period_id', $period->id)->withTrashed()
                ->first();

            //check if the existing record is approved or paid before proceeding. 
            if ($existingRecord && in_array($existingRecord->payroll_record_status, [PayrollStatus::APPROVED, PayrollStatus::PAID])) {
                $results[] = [
                    'employee_id' => $employee->id,
                    'status' => 'skipped',
                    'message' => 'Payroll already approved or paid for this period.',
                ];
                continue;
            }

            if (!$request->recalculate_existing && $existingRecord) {
                continue;
            }

            try {
                $payrollRecord = $this->calculateEmployeePayroll($employee->employeePayroll, $period);

                $results[] = [
                    'employee_id' => $employee->id,
                    'status' => 'success',
                    'payroll_record_id' => $payrollRecord->id
                ];
            } catch (\Exception $e) {
                Log::error("Payroll calculation failed for Employee ID {$employee->id}: " . $e->getMessage());
                $results[] = [
                    'employee_id' => $employee->id,
                    'status' => 'error',
                    'message' => 'Message',
                ];
            }
        }

        return $results;
    }

    public function calculatePeriodPayrollForOneEmployee(PayrollPeriod $period, $employeeID)
    {
        // Get active employees who HAVE an employeePayroll relation
        $employee = Employee::where('status', GeneralStatus::ACTIVE)
            ->has('employeePayroll')
            ->with('employeePayroll')
            ->where('employee_id', $employeeID)
            ->first();

        $payrollRecord = $this->calculateEmployeePayroll($employee->employeePayroll, $period);

        $results = [
            'employee_id' => $employee->employee_id,
            'status' => 'success',
            'payroll_record_id' => $payrollRecord->id,
            'employee_id' => $employee->employee_id,
            'message' => 'Message',
        ];

        return $results;
    }

    public function calculateSHIF($gross_amount)
    {
        $SHIFAmount = (2.75 / 100) * $gross_amount;
        if ($SHIFAmount < 300) {
            $SHIFAmount = 300;
        }
        return $SHIFAmount;
    }

    /**
     * Process claim recovery automatically when payroll is calculated
     */
    private function processClaimRecovery($recoveryId, $actualAmount, $payrollRecordId)
    {
        try {
            $recovery = PayrollClaimRecovery::findOrFail($recoveryId);

            $recovery->update([
                'actual_amount' => $actualAmount,
                'status' => 'processed',
                'processed_at' => now(),
                'payroll_reference' => 'PAYROLL_' . $payrollRecordId,
                'notes' => 'Automatically processed during payroll calculation',
                'updated_by' => auth()->id() ?? 1
            ]);

            // Update the parent claim's recovery amount
            $recovery->payrollClaim->increment('amount_recovered', $actualAmount);

            // Check if claim is fully recovered
            if ($recovery->payrollClaim->is_fully_recovered) {
                $recovery->payrollClaim->update([
                    'status' => PayrollClaim::STATUS_FULLY_RECOVERED,
                    'recovery_completion_date' => now()
                ]);
            } else {
                $recovery->payrollClaim->update([
                    'status' => PayrollClaim::STATUS_PARTIALLY_RECOVERED
                ]);
            }
        } catch (\Exception $e) {
            // Don't throw the error to avoid breaking payroll processing
            // Log it for manual intervention
        }
    }

    /**
     * Process loan deduction when payroll is calculated
     */
    private function processLoanDeduction($loanId, $actualAmount, $payrollRecordId)
    {
        try {
            $loan = Loan::findOrFail($loanId);

            // Update the loan balance
            $newBalance = max(0, $loan->balance - $actualAmount);
            $loan->update([
                'balance' => $newBalance,
                'updated_by' => auth()->id() ?? 1
            ]);

            // If loan is fully paid, update status
            if ($newBalance <= 0) {
                $loan->update([
                    'status' => \App\Lib\Enumerations\GeneralStatus::COMPLETED
                ]);
            }
        } catch (\Exception $e) {
            // Don't throw the error to avoid breaking payroll processing
            // Log the error for manual intervention
            Log::warning("Failed to process loan deduction for Loan ID {$loanId}: " . $e->getMessage());
        }
    }

    private function createZeroPayrollRecord(EmployeePayroll $employeePayroll, PayrollPeriod $period, $basicSalary, $totalAllowances, $allEarnings)
    {
        // Check if there's an approval workflow for PayrollRecord
        $hasApprovalWorkflow = ApprovalWorkflow::forModel(PayrollRecord::class) !== null;

        // If no approval workflow exists, save as approved at all levels
        if (!$hasApprovalWorkflow) {
            $approvalStatus = ApprovalStatus::APPROVED;
            $payrollRecordStatus = PayrollStatus::APPROVED;
            $dateApproved = now();
            $approvedBy = auth()->id();
        } else {
            // Keep current behavior when approval workflow exists
            $approvalStatus = ApprovalStatus::DRAFT;
            $payrollRecordStatus = PayrollStatus::CALCULATED;
            $dateApproved = null;
            $approvedBy = null;
        }

        // Create payroll record with all zeros for deductions using updateOrCreate
        $payrollRecord = PayrollRecord::updateOrCreate(
            [
                'employee_payroll_id' => $employeePayroll->id,
                'payroll_period_id' => $period->id,
                'employee_id' => $employeePayroll->employee_id,
            ],
            [
                'basic_salary' => $basicSalary,
                'total_allowances' => $totalAllowances,
                'gross_salary' => 0,
                'total_deductions' => 0,
                'statutory_deductions' => 0,
                'non_statutory_deductions' => 0,
                'claim_recoveries' => 0,
                'loan_deductions' => 0,
                'paye_tax' => 0,
                'nssf_contribution' => 0,
                'shif_contribution' => 0,
                'housing_levy' => 0,
                'pension_contribution' => 0,
                'net_salary' => 0,
                'payment_method' => $employeePayroll->payment_method,
                'status' => PayrollRecord::STATUS_CALCULATED,
                'created_by' => auth()->id(),
                'approval_status' => $approvalStatus,
                'payroll_record_status' => $payrollRecordStatus,
                'date_approved' => $dateApproved,
                'approved_by' => $approvedBy,
                'industrial_training_levy' => 0,
                'nssf_tier1_company_contribution' => 0,
                'nssf_tier2_company_contribution' => 0,
                'housing_levy_company_contribution' => 0,
                'employer_pension_contribution' => 0,
                'shif_company_contribution' => 0,
                'unpaid_amount' => 0,
                'nssf_tier1_contribution' => 0,
                'nssf_tier2_contribution' => 0,
            ]
        );

        // Delete existing details first
        PayrollRecordDetail::where('payroll_record_id', $payrollRecord->id)->delete();

        // Add basic income to details (even though it's zero)
        $basicIncomeDetail = [
            'name' => 'Basic Income',
            'type' => 'earning',
            'code' => 'basic_income',
            'amount' => $basicSalary,
            'units' => $this->getBasicIncomeUnits($employeePayroll, $period),
            'calculation_basis' => $employeePayroll->basic_salary,
            'rate' => 1,
            'is_taxable' => true,
            'is_pensionable' => true,
            'metadata' => [
                'frequency' => $employeePayroll->income_frequency,
                'original_salary' => $employeePayroll->basic_salary,
                'note' => 'Gross salary is zero - all deductions set to zero'
            ]
        ];

        // Add basic income to allowances
        array_unshift($allEarnings, $basicIncomeDetail);

        // Save payroll record details with zero deductions
        $this->savePayrollRecordDetails($payrollRecord, $allEarnings, [], [], [], [
            'paye' => 0,
            'nssf' => 0,
            'nssf_tier1' => 0,
            'nssf_tier2' => 0,
            'shif' => 0,
            'housing_levy' => 0,
            'pension' => 0
        ], [
            'industrial_training_levy' => 0,
            'nssf_tier1_company' => 0,
            'nssf_tier2_company' => 0,
            'housing_levy_company' => 0,
            'employer_pension' => 0,
            'shif_company' => 0
        ]);

        return $payrollRecord;
    }
    /**
     * Get working days between two dates (excluding weekends and public holidays)
     */
    private function getWorkingDaysInPeriod(Carbon $startDate, Carbon $endDate)
    {
        // Use the existing attendance repository method to get working days
        $workingDays = $this->attendanceRepository->new_number_of_working_days_date($startDate, $endDate);

        // If the repository method returns an array of dates, count them
        if (is_array($workingDays)) {
            return count($workingDays);
        }

        // Fallback: calculate working days excluding weekends
        return $this->calculateWorkingDaysExcludingWeekends($startDate, $endDate);
    }

    /**
     * Fallback method to calculate working days excluding weekends
     */
    private function calculateWorkingDaysExcludingWeekends(Carbon $startDate, Carbon $endDate)
    {
        $start = $startDate->copy()->startOfDay();
        $end = $endDate->copy()->endOfDay();

        if ($start->gt($end)) {
            return 0;
        }

        $workingDays = 0;
        $current = $start->copy();

        while ($current->lte($end)) {
            // Check if it's a weekend (Saturday = 6, Sunday = 0)
            if (!$current->isWeekend()) {
                $workingDays++;
            }
            $current->addDay();
        }

        return $workingDays;
    }

    /**
     * Get pension details for metadata
     */
    private function getPensionDetails(EmployeePayroll $employeePayroll, $pensionablePay)
    {
        $pensionDetails = [];

        if ($employeePayroll->pensionSchemes->count() > 0) {
            foreach ($employeePayroll->pensionSchemes as $scheme) {
                $employeeRate = $scheme->pivot->employee_rate / 100;
                $contribution = $pensionablePay * $employeeRate;

                // Apply min/max limits
                if ($scheme->minimum_contribution && $contribution < $scheme->minimum_contribution) {
                    $contribution = $scheme->minimum_contribution;
                }
                if ($scheme->maximum_contribution && $contribution > $scheme->maximum_contribution) {
                    $contribution = $scheme->maximum_contribution;
                }

                $pensionDetails[] = [
                    'scheme_name' => $scheme->name,
                    'employee_rate_percent' => $scheme->pivot->employee_rate,
                    'employee_rate_decimal' => $employeeRate,
                    'pensionable_pay' => $pensionablePay,
                    'contribution_amount' => $contribution,
                    'minimum_contribution' => $scheme->minimum_contribution,
                    'maximum_contribution' => $scheme->maximum_contribution
                ];
            }
        }

        return $pensionDetails;
    }

    /**
     * Get tax breakdown for metadata
     */
    private function getTaxBreakdown($taxableIncome, $insuranceRelief)
    {

        $taxBreakdown = [];
        $remainingIncome = $taxableIncome;
        $personalRelief = PayrollConfiguration::getPersonalRelief();
        $totalRelief = $personalRelief + $insuranceRelief;
        $payeBands = PayrollConfiguration::getPayeBands();

        foreach ($payeBands as $band) {
            $bandMin = $band['min'];
            $bandMax = $band['max'] ?? PHP_INT_MAX;
            $rate = $band['rate'];

            if ($remainingIncome <= 0) {
                break;
            }

            $bandWidth = $bandMax - $bandMin + 1;
            $taxableInBand = min($remainingIncome, $bandWidth);

            if ($taxableIncome > $bandMin) {
                $taxAmount = $taxableInBand * $rate;
                $taxBreakdown[] = [
                    'band_min' => $bandMin,
                    'band_max' => $bandMax,
                    'rate_percent' => $rate * 100,
                    'rate_decimal' => $rate,
                    'taxable_in_band' => $taxableInBand,
                    'tax_amount' => $taxAmount
                ];
                $remainingIncome -= $taxableInBand;
            }
        }

        return [
            'taxable_income' => $taxableIncome,
            'personal_relief' => $personalRelief,
            'insurance_relief' => $insuranceRelief,
            'total_relief' => $totalRelief,
            'bands' => $taxBreakdown
        ];
    }

    /**
     * Get reliefs applied for metadata
     */
    private function getReliefsApplied($insuranceRelief, EmployeePayroll $employeePayroll)
    {
        $reliefs = [
            [
                'name' => 'Personal Relief',
                'amount' => PayrollConfiguration::getPersonalRelief(),
                'type' => 'standard'
            ],
            [
                'name' => 'Insurance Relief',
                'amount' => $insuranceRelief,
                'type' => 'insurance'
            ]
        ];

        // Disability exemption
        if ($employeePayroll->disability_exemption) {
            $reliefs[] = [
                'name' => 'Disability Exemption',
                'amount' => 2400,
                'type' => 'disability'
            ];
        }

        return $reliefs;
    }

    /**
     * Get taxable amounts breakdown for metadata
     */
    private function getTaxableAmounts($grossSalary, $allowances, $nssfContribution, $shifContribution, $housingLevy, $pensionContribution)
    {
        $nonTaxableAmount = 0;
        $taxableAllowances = [];

        foreach ($allowances as $allowance) {
            if (!$allowance['is_taxable']) {
                $nonTaxableAmount += $allowance['amount'];
            } else {
                $taxableAllowances[] = $allowance;
            }
        }

        $totalPensionContribution = $nssfContribution + $pensionContribution;
        $cappedPensionContribution = min($totalPensionContribution, 30000);

        $taxableIncome = $grossSalary - ($housingLevy + $shifContribution + $nonTaxableAmount + $cappedPensionContribution);

        return [
            'gross_salary' => $grossSalary,
            'non_taxable_allowances' => $nonTaxableAmount,
            'taxable_allowances' => $taxableAllowances,
            'nssf_contribution' => $nssfContribution,
            'shif_contribution' => $shifContribution,
            'housing_levy' => $housingLevy,
            'pension_contribution' => $pensionContribution,
            'total_pension_contribution' => $totalPensionContribution,
            'capped_pension_contribution' => $cappedPensionContribution,
            'taxable_income' => $taxableIncome
        ];
    }
}