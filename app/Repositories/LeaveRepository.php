<?php
/*
 * Copyright (c) 2023/9/6 sw@stawitech
 */

namespace App\Repositories;

use App\LeaveRollover;
use App\Lib\Enumerations\LeaveStatus;
use Illuminate\Support\Facades\DB;

use App\Models\LeaveApplication;
use App\Models\EarnLeaveRule;
use App\Models\LeaveType;
use App\Models\Employee;
use App\Models\FinancialYear;
use App\Models\Holiday;
use App\Models\HolidayDetails;
use App\Models\AdvancedLeaveRecord;
use App\Models\LeaveGroupSetting;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToArray;
use PhpOffice\PhpSpreadsheet\Calculation\Financial;

class LeaveRepository
{

    public function calculateTotalNumberOfLeaveDays($application_from_date, $application_to_date, $leaveTypeId, $employeeId = null)
    {
        // If employee_id is provided (e.g., when applying on behalf), use that employee
        // Otherwise, use the logged-in user's employee details (for self-application)
        if ($employeeId) {
            $employee = Employee::where('employee_id', $employeeId)->first();
        } else {
            $user = Auth::user();
            $employee = $user->employeeDetails;
        }

        if (!$employee) {
            return 0;
        }

        return $employee->appliedLeaveDays($application_from_date, $application_to_date, $leaveTypeId);
    }

    /**
     * Calculate the actual leave days taken within a financial year
     * respecting working days vs calendar days settings
     */
    private function calculateActualLeaveDaysTaken($leaveApplication, $fiscal_year)
    {
        $employee = Employee::find($leaveApplication->employee_id);
        if (!$employee) {
            return 0;
        }

        return $this->calculateLeaveDaysInPeriod(
            $employee,
            $leaveApplication->application_from_date,
            $leaveApplication->application_to_date,
            $leaveApplication->leave_type_id,
            $fiscal_year->start_date,
            $fiscal_year->end_date
        );
    }

    /**
     * Calculate leave days in a specific period
     * Excludes weekends and holidays when leave type is set to working_days
     */
    public function calculateLeaveDaysInPeriod($employee, $leaveStartDate, $leaveEndDate, $leaveTypeId, $fiscalYearStart, $fiscalYearEnd)
    {
        $leaveStart = Carbon::parse($leaveStartDate);
        $leaveEnd = Carbon::parse($leaveEndDate);
        $fiscalStart = Carbon::parse($fiscalYearStart);
        $fiscalEnd = Carbon::parse($fiscalYearEnd);

        // Determine the overlap period between leave and fiscal year
        $overlapStart = $leaveStart->greaterThan($fiscalStart) ? $leaveStart : $fiscalStart;
        $overlapEnd = $leaveEnd->lessThan($fiscalEnd) ? $leaveEnd : $fiscalEnd;

        // If no overlap, return 0
        if ($overlapStart->greaterThan($overlapEnd)) {
            return 0;
        }

        // Get leave group settings to determine how to count days
        $leaveGroup = $employee->leaveGroup;
        if (!$leaveGroup) {
            return 0;
        }

        $settings = LeaveGroupSetting::where('leave_group_id', $leaveGroup->id)
            ->where('leave_type_id', $leaveTypeId)
            ->first();

        if (!$settings) {
            return 0;
        }

        // If calendar_days, count all days in the overlap period
        if ($settings->applicable_on === 'calendar_days') {
            return $overlapStart->diffInDays($overlapEnd) + 1;
        }

        // For working_days, exclude weekends and holidays
        $affectingHolidays = $leaveGroup->publicHolidays->pluck('holiday_id')->toArray();
        $holidays = HolidayDetails::whereIn('holiday_id', $affectingHolidays)
            ->where('status', 1)
            ->get()
            ->flatMap(function ($holiday) {
                return Carbon::parse($holiday->from_date)->toPeriod($holiday->to_date)->toArray();
            })
            ->map(fn($date) => $date->format('Y-m-d'))
            ->toArray();

        $weekendDays = $leaveGroup->weeklyHolidays->pluck('day_name')->map(function ($day) {
            return strtolower($day);
        })->toArray();

        $leaveDays = 0;
        $countedDates = []; // Track what we're counting

        for ($date = $overlapStart->copy(); $date->lte($overlapEnd); $date->addDay()) {
            $dayName = strtolower($date->format('l'));
            $dateStr = $date->format('Y-m-d');

            $isHoliday = in_array($dateStr, $holidays);
            $isWeekend = in_array($dayName, $weekendDays);
            $shouldCount = !$isHoliday && !$isWeekend;

            if ($shouldCount) {
                $leaveDays++;
                $countedDates[] = [
                    'date' => $dateStr,
                    'day' => $dayName,
                    'is_holiday' => $isHoliday,
                    'is_weekend' => $isWeekend
                ];
            }
        }


        return $leaveDays;
    }

    /**
     * Calculate total leave days used by an employee across all leave types
     * for a specific fiscal year period
     */
    public function calculateTotalLeaveDaysUsed($employeeId, $fiscalStartDate, $fiscalEndDate)
    {
        $employee = Employee::where('employee_id', $employeeId)->first();

        if (!$employee) {
            return 0;
        }

        // Get approved leaves for all leave types with fiscal year overlap
        $approvedLeaves = LeaveApplication::where('employee_id', $employeeId)
            ->where('final_status', LeaveStatus::APPROVE)
            ->where(function ($query) use ($fiscalStartDate, $fiscalEndDate) {
                $query->whereBetween('application_from_date', [$fiscalStartDate, $fiscalEndDate])
                    ->orWhereBetween('application_to_date', [$fiscalStartDate, $fiscalEndDate])
                    ->orWhere(function ($q) use ($fiscalStartDate, $fiscalEndDate) {
                        $q->where('application_from_date', '<=', $fiscalStartDate)
                            ->where('application_to_date', '>=', $fiscalEndDate);
                    });
            })
            ->get();

        // Calculate actual leave days using the accurate method
        $totalDaysUsed = 0;
        foreach ($approvedLeaves as $leave) {
            $totalDaysUsed += $this->calculateLeaveDaysInPeriod(
                $employee,
                $leave->application_from_date,
                $leave->application_to_date,
                $leave->leave_type_id,
                $fiscalStartDate,
                $fiscalEndDate
            );
        }

        return $totalDaysUsed;
    }

    public function calculateEmployeeLeaveBalance($leave_type_id, $employee_id)
    {
        // fiscal year calculation here
        $today = date('Y-m-d');
        $fiscal_year = FinancialYear::where('start_date', '<=', $today)
            ->where('end_date', '>=', $today)
            ->first();

        if (!$fiscal_year) {
            return 0;
        }

        $fiscal_start_date = $fiscal_year->start_date;
        $fiscal_end_date = $fiscal_year->end_date;

        // Get all approved leaves for this employee and leave type
        // IMPORTANT: Filter based on leave dates overlapping with fiscal year, NOT approval date
        $approvedLeaves = LeaveApplication::where('employee_id', $employee_id)
            ->where('final_status', LeaveStatus::APPROVE)
            ->where('leave_type_id', $leave_type_id)
            ->where(function ($query) use ($fiscal_start_date, $fiscal_end_date) {
                $query->whereBetween('application_from_date', [$fiscal_start_date, $fiscal_end_date])
                    ->orWhereBetween('application_to_date', [$fiscal_start_date, $fiscal_end_date])
                    ->orWhere(function ($q) use ($fiscal_start_date, $fiscal_end_date) {
                        $q->where('application_from_date', '<=', $fiscal_start_date)
                            ->where('application_to_date', '>=', $fiscal_end_date);
                    });
            })
            ->get();

        // Calculate actual leave days taken based on settings
        $employee = Employee::where('employee_id', $employee_id)->first();
        $leaveTaken = 0;

        foreach ($approvedLeaves as $leave) {
            $leaveTaken += $this->calculateLeaveDaysInPeriod(
                $employee,
                $leave->application_from_date,
                $leave->application_to_date,
                $leave_type_id,
                $fiscal_start_date,
                $fiscal_end_date
            );
        }

        $earnedDays = $employee->getEarnedLeaveDays($leave_type_id, $fiscal_year->id);

        // Get rolled over leaves
        $rolled_over_leaves = LeaveRollover::where('employee_id', $employee_id)
            ->where('final_status', '2')
            ->where('financial_year_id', $fiscal_year->id)
            ->where('leave_type_id', $leave_type_id)
            ->value('days_requested') ?? 0;

        // Get leave adjustments for this financial year
        $adjustmentTotal = 0;
        $adjustments = \App\Models\LeaveAdjustment::where('status', 'approved')
            ->where('employee_id', $employee_id)
            ->where('leave_type_id', $leave_type_id)
            ->where('financial_year_id', $fiscal_year->id)
            ->get();

        foreach ($adjustments as $adjustment) {
            if ($adjustment->adjustment_type === 'add') {
                $adjustmentTotal += $adjustment->adjustment_days;
            } else {
                $adjustmentTotal -= $adjustment->adjustment_days;
            }
        }

        // Calculate total available leave including adjustments
        $totalAvailable = $earnedDays + $rolled_over_leaves + $adjustmentTotal;
        $currentBalance = $totalAvailable - $leaveTaken;

        return $currentBalance;
    }

    public function calculateEmployeeLeaveBalanceWithAdvanced($leave_type_id, $employee_id)
    {
        // Get regular leave balance (which now includes adjustments)
        $regularBalance = $this->calculateEmployeeLeaveBalance($leave_type_id, $employee_id);

        // Get available advanced leave
        $availableAdvanced = $this->calculateAdvancedLeaveAvailable($leave_type_id, $employee_id);
        $advanceDaysAllowed = $this->calculateTotalAdvanceDaysByPeriod($leave_type_id, $employee_id);

        // Get adjustment details for response
        $today = date('Y-m-d');
        $fiscal_year = FinancialYear::where('start_date', '<=', $today)
            ->where('end_date', '>=', $today)
            ->first();

        $adjustmentDetails = $this->getAdjustmentDetails($leave_type_id, $employee_id, $fiscal_year->id);

        return [
            'regular_balance' => (float) $regularBalance,
            'advance_available' => (float) $availableAdvanced,
            'total_available' => (float) ($regularBalance + $availableAdvanced),
            'is_advance_period' => $this->isWithinAdvancePeriod($leave_type_id, $employee_id),
            'advance_days_allowed' => (float) $advanceDaysAllowed,
            'has_adjustments' => $adjustmentDetails['has_adjustments'] ?? false,
            'adjustment_additions' => (float) ($adjustmentDetails['total_additions'] ?? 0),
            'adjustment_deductions' => (float) ($adjustmentDetails['total_deductions'] ?? 0),
            'net_adjustment' => (float) ($adjustmentDetails['net_adjustment'] ?? 0),
            'adjustment_details' => $adjustmentDetails,
        ];
    }

    /**
     * Get detailed adjustment information for a leave type
     */
    private function getAdjustmentDetails($leave_type_id, $employee_id, $financial_year_id)
    {
        $adjustments = \App\Models\LeaveAdjustment::where('status', 'approved')
            ->where('employee_id', $employee_id)
            ->where('leave_type_id', $leave_type_id)
            ->where('financial_year_id', $financial_year_id)
            ->get();

        $totalAdditions = 0;
        $totalDeductions = 0;

        foreach ($adjustments as $adjustment) {
            if ($adjustment->adjustment_type === 'add') {
                $totalAdditions += $adjustment->adjustment_days;
            } else {
                $totalDeductions += $adjustment->adjustment_days;
            }
        }

        return [
            'total_additions' => $totalAdditions,
            'total_deductions' => $totalDeductions,
            'net_adjustment' => $totalAdditions - $totalDeductions,
            'has_adjustments' => count($adjustments) > 0,
            'adjustment_count' => count($adjustments)
        ];
    }



    public function calculateEmployeeEarnLeave($leave_type_id, $employee_id, $action = false)
    {
        // fiscal year calculation here
        $today = date('Y-m-d');
        $fiscal_year = FinancialYear::where('start_date', '<=', $today)
            ->where('end_date', '>=', $today)
            ->first();
        $fiscal_start_date = $fiscal_year->start_date;
        $fiscal_end_date = $fiscal_year->end_date;

        $employeeInfo = Employee::where('employee_id', $employee_id)->first();
        $joiningYearAndMonth = explode('-', $employeeInfo->date_of_joining);
        $joiningYear = $joiningYearAndMonth[0];
        $joiningMonth = (int)$joiningYearAndMonth[1];

        $totalMonth = 0;
        $date1 = date_create(date('Y-m-d'));
        $date2 = date_create($fiscal_start_date);
        $diff = date_diff($date1, $date2);
        $diff2 = - ($diff->format("%R%m"));
        $totalMonth = $diff2;

        // Get approved leaves based on leave dates overlapping with fiscal year
        $employee = Employee::where('employee_id', $employee_id)->first();
        $approvedLeaves = LeaveApplication::where('employee_id', $employee_id)
            ->where('leave_type_id', $leave_type_id)
            ->where('final_status', LeaveStatus::APPROVE)
            ->where(function ($query) use ($fiscal_start_date, $fiscal_end_date) {
                $query->whereBetween('application_from_date', [$fiscal_start_date, $fiscal_end_date])
                    ->orWhereBetween('application_to_date', [$fiscal_start_date, $fiscal_end_date])
                    ->orWhere(function ($q) use ($fiscal_start_date, $fiscal_end_date) {
                        $q->where('application_from_date', '<=', $fiscal_start_date)
                            ->where('application_to_date', '>=', $fiscal_end_date);
                    });
            })
            ->get();

        $leaveConsumed = 0;
        foreach ($approvedLeaves as $leave) {
            $leaveConsumed += $this->calculateLeaveDaysInPeriod(
                $employee,
                $leave->application_from_date,
                $leave->application_to_date,
                $leave_type_id,
                $fiscal_start_date,
                $fiscal_end_date
            );
        }

        $earnLeaveRule = EarnLeaveRule::first();

        if ($action == 'getEarnLeaveBalanceAndExpenseBalance') {
            $totalNumberOfDays = $totalMonth * $earnLeaveRule->day_of_earn_leave;
            $data = [
                'totalEarnLeave' => round($totalMonth * $earnLeaveRule->day_of_earn_leave),
                'leaveConsume' => $leaveConsumed,
                'currentBalance' => round($totalNumberOfDays - $leaveConsumed),
            ];
            return $data;
        }

        $totalNumberOfDays = $totalMonth * $earnLeaveRule->day_of_earn_leave;
        return round($totalNumberOfDays - $leaveConsumed);
    }

    public function calculateEmployeeEarnLeaveForDisplay($leave_type_id, $employee_id, $action = false)
    {
        // fiscal year calculation here
        $today = date('Y-m-d');
        $fiscal_year = FinancialYear::where('start_date', '<=', $today)
            ->where('end_date', '>=', $today)
            ->first();
        $fiscal_start_date = $fiscal_year->start_date;
        $fiscal_end_date = $fiscal_year->end_date;

        $employeeInfo = Employee::where('employee_id', $employee_id)->first();
        $joiningYearAndMonth = explode('-', $employeeInfo->date_of_joining);
        $joiningYear = $joiningYearAndMonth[0];
        $joiningMonth = (int)$joiningYearAndMonth[1];

        $totalMonth = 0;

        $date1 = date_create(date('Y-m-d'));
        $date2 = date_create($fiscal_start_date);
        $diff = date_diff($date1, $date2);
        $diff2 = - ($diff->format("%R%m"));
        $totalMonth = $diff2;

        // Get approved leaves based on leave dates overlapping with fiscal year
        $employee = Employee::where('employee_id', $employee_id)->first();
        $approvedLeaves = LeaveApplication::where('employee_id', $employee_id)
            ->where('leave_type_id', $leave_type_id)
            ->where('final_status', LeaveStatus::APPROVE)
            ->where(function ($query) use ($fiscal_start_date, $fiscal_end_date) {
                $query->whereBetween('application_from_date', [$fiscal_start_date, $fiscal_end_date])
                    ->orWhereBetween('application_to_date', [$fiscal_start_date, $fiscal_end_date])
                    ->orWhere(function ($q) use ($fiscal_start_date, $fiscal_end_date) {
                        $q->where('application_from_date', '<=', $fiscal_start_date)
                            ->where('application_to_date', '>=', $fiscal_end_date);
                    });
            })
            ->get();

        $leaveConsumed = 0;
        foreach ($approvedLeaves as $leave) {
            $leaveConsumed += $this->calculateLeaveDaysInPeriod(
                $employee,
                $leave->application_from_date,
                $leave->application_to_date,
                $leave_type_id,
                $fiscal_start_date,
                $fiscal_end_date
            );
        }

        $earnLeaveRule = EarnLeaveRule::first();

        if ($action == 'getEarnLeaveBalanceAndExpenseBalance') {
            $totalNumberOfDays = $totalMonth * $earnLeaveRule->day_of_earn_leave;
            $data = [
                'totalEarnLeave' => round($totalMonth * $earnLeaveRule->day_of_earn_leave),
                'leaveConsume' => $leaveConsumed,
                'currentBalance' => round($totalNumberOfDays - $leaveConsumed),
            ];
            return $data;
        }

        $totalNumberOfDays = $totalMonth * $earnLeaveRule->day_of_earn_leave;
        $totalCumulative = round($totalMonth * $earnLeaveRule->day_of_earn_leave);

        return [
            'totalBalance' => round($totalNumberOfDays),
            'usedDays' => $leaveConsumed,
            'totalCumulative' => $totalCumulative
        ];
    }

    /**
     * Calculate the available advanced leave for an employee
     */
    public function calculateAdvancedLeaveAvailable($leave_type_id, $employee_id)
    {
        $employee = Employee::where('employee_id', $employee_id)->first();
        if (!$employee || !$employee->leaveGroup) {
            return 0;
        }

        $setting = $employee->leaveGroup->settings()
            ->where('leave_type_id', $leave_type_id)
            ->first();

        if (!$setting || !$setting->allow_advanced_leave) {
            return 0;
        }

        // Calculate advance days based on period and accrual rate
        $advanceMonths = $setting->advanced_period_months;
        $accrualRate = (float)$setting->earning_rate;

        if ($advanceMonths <= 0 || $accrualRate <= 0) {
            return 0;
        }

        $totalAdvanceAllowed = $advanceMonths * $accrualRate;

        // Apply absolute limit if set
        if ($setting->advanced_limit_days && $setting->advanced_limit_days > 0) {
            $totalAdvanceAllowed = min($totalAdvanceAllowed, $setting->advanced_limit_days);
        }

        return round($totalAdvanceAllowed, 2);
    }

    /**
     * Calculate the total advance days allowed based on advance period (in months)
     */
    public function calculateTotalAdvanceDaysByPeriod($leave_type_id, $employee_id)
    {
        $employee = Employee::where('employee_id', $employee_id)->first();

        if (!$employee || !$employee->leaveGroup) {
            return 0.0;
        }

        // Get leave group setting for the leave type
        $setting = $employee->leaveGroup->settings()
            ->where('leave_type_id', $leave_type_id)
            ->first();

        if (!$setting || !$setting->allow_advanced_leave) {
            return 0.0;
        }

        // Check if advanced period is configured
        if (!$setting->advanced_period_months || $setting->advanced_period_months <= 0) {
            return 0.0;
        }

        // Get accrual rate from setting
        $accrualRate = (float)$setting->earning_rate;
        if ($accrualRate <= 0) {
            return 0.0;
        }

        // Calculate advance days based on period and accrual rate
        $advanceMonths = $setting->advanced_period_months;
        $advanceDays = $advanceMonths * $accrualRate;

        // Apply absolute limit if set
        if ($setting->advanced_limit_days && $setting->advanced_limit_days > 0) {
            $advanceDays = min($advanceDays, $setting->advanced_limit_days);
        }

        return round($advanceDays, 2);
    }

    /**
     * Check if current date is within the advance period
     */
    public function isWithinAdvancePeriod($leave_type_id, $employee_id)
    {
        $today = Carbon::today();

        // Get current fiscal year
        $fiscal_year = FinancialYear::where('start_date', '<=', $today)
            ->where('end_date', '>=', $today)
            ->first();

        if (!$fiscal_year) {
            return false;
        }

        $employee = Employee::where('employee_id', $employee_id)->first();

        if (!$employee || !$employee->leaveGroup) {
            return false;
        }

        // Get leave group setting
        $setting = $employee->leaveGroup->settings()
            ->where('leave_type_id', $leave_type_id)
            ->first();

        if (!$setting || !$setting->allow_advanced_leave) {
            return false;
        }

        // Check if advanced period is configured
        if (!$setting->advanced_period_months || $setting->advanced_period_months <= 0) {
            return false;
        }

        // Check if today is within the advanced period months before fiscal year end
        $fiscalYearEnd = Carbon::parse($fiscal_year->end_date);
        $advancePeriodStart = $fiscalYearEnd->copy()->subMonths($setting->advanced_period_months);

        return $today->gte($advancePeriodStart) && $today->lte($fiscalYearEnd);
    }

    /**
     * Get simple advance days information
     */
    public function getAdvanceDaysInfo($leave_type_id, $employee_id)
    {
        $employee = Employee::where('employee_id', $employee_id)->first();

        if (!$employee || !$employee->leaveGroup) {
            return [
                'total_advance_days' => 0.0,
                'is_within_period' => true,
                'available' => 0.0,
                'period_months' => 0,
                'accrual_rate' => 0.0,
                'max_limit' => null
            ];
        }

        // Get leave group setting
        $setting = $employee->leaveGroup->settings()
            ->where('leave_type_id', $leave_type_id)
            ->first();

        if (!$setting || !$setting->allow_advanced_leave) {
            return [
                'total_advance_days' => 0.0,
                'is_within_period' => true,
                'available' => 0.0,
                'period_months' => 0,
                'accrual_rate' => 0.0,
                'max_limit' => null
            ];
        }

        // Calculate total advance days based on period and accrual rate
        $totalAdvanceDays = $this->calculateTotalAdvanceDaysByPeriod($leave_type_id, $employee_id);

        return [
            'total_advance_days' => $totalAdvanceDays,
            'is_within_period' => true,
            'available' => $totalAdvanceDays,
            'period_months' => $setting->advanced_period_months,
            'accrual_rate' => (float)$setting->earning_rate,
            'max_limit' => $setting->advanced_limit_days,
            'annual_entitlement' => $setting->annual_entitlement
        ];
    }

    /**
     * Calculate elapsed months between two dates
     */
    private function calculateElapsedMonths($startDate, $endDate)
    {
        $start = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);

        $months = $end->diffInMonths($start);
        return max(1, $months); // At least 1 month
    }

    /**
     * Get advanced leave record or create new one
     */
    public function getOrCreateAdvancedRecord($leave_type_id, $employee_id)
    {
        $today = date('Y-m-d');
        $fiscal_year = FinancialYear::where('start_date', '<=', $today)
            ->where('end_date', '>=', $today)
            ->first();

        if (!$fiscal_year) {
            return null;
        }

        return AdvancedLeaveRecord::firstOrCreate(
            [
                'employee_id' => $employee_id,
                'leave_type_id' => $leave_type_id,
                'financial_year_id' => $fiscal_year->id,
            ],
            [
                'advanced_days' => 0,
                'recovered_days' => 0,
                'transactions' => [],
            ]
        );
    }

    /**
     * Record advanced leave usage
     */
    public function recordAdvancedLeaveUsage($leave_type_id, $employee_id, $days)
    {
        $record = $this->getOrCreateAdvancedRecord($leave_type_id, $employee_id);

        if (!$record) {
            return false;
        }

        // Check if advance is allowed
        $available = $this->calculateAdvancedLeaveAvailable($leave_type_id, $employee_id);
        if ($available < $days) {
            return false;
        }

        // Update advanced days
        $record->advanced_days += $days;

        // Add transaction record
        $transactions = $record->transactions ?? [];
        $transactions[] = [
            'type' => 'advance',
            'days' => $days,
            'date' => now()->toISOString(),
            'leave_application_id' => null, // Will be updated when leave is applied
        ];
        $record->transactions = $transactions;

        return $record->save();
    }

    /**
     * Record advanced leave recovery (when leave is taken)
     */
    public function recordAdvancedLeaveRecovery($leave_type_id, $employee_id, $days, $leave_application_id = null)
    {
        $record = $this->getOrCreateAdvancedRecord($leave_type_id, $employee_id);

        if (!$record) {
            return false;
        }

        // Update recovered days
        $record->recovered_days += $days;

        // Add transaction record
        $transactions = $record->transactions ?? [];
        $transactions[] = [
            'type' => 'recovery',
            'days' => $days,
            'date' => now()->toISOString(),
            'leave_application_id' => $leave_application_id,
        ];
        $record->transactions = $transactions;

        return $record->save();
    }
    /**
     * Calculate total leave days used for a specific leave type
     */
    public function calculateLeaveDaysUsedByType($employeeId, $leaveTypeId, $fiscalStartDate, $fiscalEndDate)
    {
        $employee = Employee::where('employee_id', $employeeId)->first();

        if (!$employee) {
            return 0;
        }

        // Get approved leaves for SPECIFIC leave type with fiscal year overlap
        $approvedLeaves = LeaveApplication::where('employee_id', $employeeId)
            ->where('leave_type_id', $leaveTypeId) // <-- Filter by leave type
            ->where('final_status', LeaveStatus::APPROVE)
            ->where(function ($query) use ($fiscalStartDate, $fiscalEndDate) {
                $query->whereBetween('application_from_date', [$fiscalStartDate, $fiscalEndDate])
                    ->orWhereBetween('application_to_date', [$fiscalStartDate, $fiscalEndDate])
                    ->orWhere(function ($q) use ($fiscalStartDate, $fiscalEndDate) {
                        $q->where('application_from_date', '<=', $fiscalStartDate)
                            ->where('application_to_date', '>=', $fiscalEndDate);
                    });
            })
            ->get();

        // Calculate actual leave days using the accurate method
        $totalDaysUsed = 0;
        foreach ($approvedLeaves as $leave) {
            $totalDaysUsed += $this->calculateLeaveDaysInPeriod(
                $employee,
                $leave->application_from_date,
                $leave->application_to_date,
                $leaveTypeId, // Pass the specific leave type ID
                $fiscalStartDate,
                $fiscalEndDate
            );
        }

        return $totalDaysUsed;
    }
}