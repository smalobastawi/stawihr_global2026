<?php

namespace App\Services\Payroll;

use App\Models\Payroll\EmployeePayroll;
use App\Models\Payroll\EmployeeSalaryHistory;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PayrollChangeService
{
    public function updateBasicSalary($employeeId, $newSalary, $effectiveDate, $changeType, $reason, $changedBy, $metadata = [])
    {
        DB::transaction(function () use ($employeeId, $newSalary, $effectiveDate, $changeType, $reason, $changedBy, $metadata) {

            $employeePayroll = EmployeePayroll::where('employee_id', $employeeId)->firstOrFail();
            $oldSalary = $employeePayroll->basic_salary;

            // Calculate change details
            $changeAmount = $newSalary - $oldSalary;
            $changePercentage = $oldSalary > 0 ? ($changeAmount / $oldSalary) * 100 : 0;

            // 1. Create history record
            EmployeeSalaryHistory::create([
                'employee_id' => $employeeId,
                'previous_salary' => $oldSalary,
                'new_salary' => $newSalary,
                'salary_change_amount' => $changeAmount,
                'salary_change_percentage' => $changePercentage,
                'effective_date' => $effectiveDate,
                'change_type' => $changeType,
                'change_reason' => $reason,
                'changed_by' => $changedBy,
                'metadata' => $metadata
            ]);

            // 2. Update current payroll record
            $employeePayroll->update([
                'previous_basic_salary' => $oldSalary,
                'basic_salary' => $newSalary,
                'last_salary_change_date' => $effectiveDate,
                'updated_by' => $changedBy
            ]);
        });
    }

    public function getSalaryHistory($employeeId, $limit = null)
    {
        $query = EmployeeSalaryHistory::with('changedBy')
            ->where('employee_id', $employeeId)
            ->orderBy('effective_date', 'desc')
            ->orderBy('created_at', 'desc');

        if ($limit) {
            $query->limit($limit);
        }

        return $query->get();
    }

    public function getSalaryOnDate($employeeId, $date)
    {
        // Find the most recent salary change that happened ON OR BEFORE the given date
        $historyBeforeDate = EmployeeSalaryHistory::where('employee_id', $employeeId)
            ->where('effective_date', '<=', $date)
            ->orderBy('effective_date', 'desc')
            ->first();

        if ($historyBeforeDate) {
            return $historyBeforeDate->new_salary;
        }

        // If no changes found before/on the date, check if there are changes AFTER the date
        $firstChangeAfterDate = EmployeeSalaryHistory::where('employee_id', $employeeId)
            ->where('effective_date', '>', $date)
            ->orderBy('effective_date', 'asc')
            ->first();

        // If there's a change after the date, return the previous_salary from that change
        // This gives us the salary that was active BEFORE the change
        if ($firstChangeAfterDate) {
            return $firstChangeAfterDate->previous_salary;
        }

        // Final fallback: get current salary from employee payroll
        return EmployeePayroll::where('employee_id', $employeeId)->value('basic_salary') ?? 0;
    }

    public function getSalaryChangesDuringPeriod($employeeId, $startDate, $endDate)
    {
        return EmployeeSalaryHistory::where('employee_id', $employeeId)
            ->whereBetween('effective_date', [$startDate, $endDate])
            ->orderBy('effective_date')
            ->get();
    }
}
