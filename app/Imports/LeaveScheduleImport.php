<?php

namespace App\Imports;

use App\Models\LeaveSchedule;
use App\Models\LeaveAdjustment;
use App\Models\Employee;
use App\Models\LeaveType;
use App\Models\FinancialYear;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Validators\ValidationException;

class LeaveScheduleImport implements ToModel, WithHeadingRow, WithValidation
{
    private $errors = [];
    private $successCount = 0;
    private $adjustmentCount = 0;
    private $rowNumber = 0;
    private $processedEmployees = []; // Track employees to avoid duplicate adjustments

    public function model(array $row)
    {
        $this->rowNumber++;

        // Normalize column names (handle both formats)
        $row = $this->normalizeColumnNames($row);

        // Skip empty rows
        if (empty($row['staff_no']) || empty($row['leave_start_date']) || empty($row['leave_end_date'])) {
            return null;
        }

        // Find employee by payroll number
        $employee = Employee::where('payroll_number', $row['staff_no'])->first();
        if (!$employee) {
            $this->errors[] = "Row {$this->rowNumber}: Employee with staff number '{$row['staff_no']}' not found.";
            return null;
        }

        // Get default leave type (Annual Leave) or find by name
        $leaveType = LeaveType::where('leave_type_name', 'like', '%Annual%')->first();
        if (!$leaveType) {
            $leaveType = LeaveType::first(); // Fallback to first available leave type
        }

        if (!$leaveType) {
            $this->errors[] = "Row {$this->rowNumber}: No leave type found in the system.";
            return null;
        }

        try {
            // Convert dates
            $fromDate = $this->parseDate($row['leave_start_date']);
            $toDate = $this->parseDate($row['leave_end_date']);
        } catch (\Exception $e) {
            $this->errors[] = "Row {$this->rowNumber}: Invalid date format. Use DD/MM/YYYY. Error: " . $e->getMessage();
            return null;
        }

        // Calculate working days for schedule
        $numberOfDays = $this->calculateLeaveDays($fromDate, $toDate);

        // Create leave schedule
        $schedule = new LeaveSchedule([
            'employee_id' => $employee->employee_id,
            'leave_type_id' => $leaveType->leave_type_id,
            'scheduled_from_date' => $fromDate,
            'scheduled_to_date' => $toDate,
            'number_of_days' => $numberOfDays,
            'purpose' => $row['remarks'] ?? 'Scheduled leave',
            'remarks' => $row['remarks'] ?? null,
            'created_by' => Auth::user()->id,
            'status' => 'scheduled',
        ]);

        // Process available days - create leave adjustment if not already done for this employee
        if (isset($row['available_days']) && is_numeric($row['available_days']) && $row['available_days'] > 0) {
            $this->createLeaveAdjustment($employee, $leaveType, $row['available_days']);
        }

        $this->successCount++;

        return $schedule;
    }

    /**
     * Normalize column names to handle different formats
     */
    private function normalizeColumnNames(array $row): array
    {
        $normalized = [];
        
        foreach ($row as $key => $value) {
            $key = strtolower(trim($key));
            $key = str_replace(['.', ' '], '_', $key); // Replace dots and spaces with underscores
            
            // Map various column name variations
            switch ($key) {
                case 'staff_no':
                case 'staff_no_':
                case 'staffno':
                case 'payroll_number':
                case 'employee_id':
                    $normalized['staff_no'] = $value;
                    break;
                    
                case 'staff_name':
                case 'employee_name':
                case 'name':
                    $normalized['staff_name'] = $value;
                    break;
                    
                case 'job_title':
                case 'designation':
                    $normalized['job_title'] = $value;
                    break;
                    
                case 'section':
                case 'department':
                    $normalized['section'] = $value;
                    break;
                    
                case 'date_of_employment':
                case 'employment_date':
                case 'join_date':
                    $normalized['date_of_employment'] = $value;
                    break;
                    
                case 'leave_start_date':
                case 'from_date':
                case 'start_date':
                case 'scheduled_from':
                    $normalized['leave_start_date'] = $value;
                    break;
                    
                case 'leave_end_date':
                case 'to_date':
                case 'end_date':
                case 'scheduled_to':
                    $normalized['leave_end_date'] = $value;
                    break;
                    
                case 'no_of_days':
                case 'no__of_days':
                case 'number_of_days':
                case 'days':
                    $normalized['no_of_days'] = $value;
                    break;
                    
                case 'available_days':
                case 'available':
                case 'balance_days':
                case 'leave_balance':
                    $normalized['available_days'] = $value;
                    break;
                    
                case 'balance':
                case 'remaining':
                case 'remaining_days':
                    $normalized['balance'] = $value;
                    break;
                    
                case 'remarks':
                case 'remark':
                case 'comments':
                case 'comment':
                case 'notes':
                case 'note':
                    $normalized['remarks'] = $value;
                    break;
                    
                default:
                    $normalized[$key] = $value;
            }
        }
        
        return $normalized;
    }

    /**
     * Parse date from various formats
     */
    private function parseDate($dateValue): string
    {
        // If it's an Excel date serial number
        if (is_numeric($dateValue)) {
            return \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($dateValue)->format('Y-m-d');
        }
        
        // Try DD/MM/YYYY format
        try {
            return \Carbon\Carbon::createFromFormat('d/m/Y', $dateValue)->format('Y-m-d');
        } catch (\Exception $e) {
            // Try other formats
            try {
                return \Carbon\Carbon::parse($dateValue)->format('Y-m-d');
            } catch (\Exception $e) {
                throw new \Exception("Cannot parse date: {$dateValue}");
            }
        }
    }

    /**
     * Create leave adjustment for migrated balance
     */
    private function createLeaveAdjustment($employee, $leaveType, $availableDays)
    {
        // Create a unique key for this employee+leave type to avoid duplicates in same import
        $key = $employee->employee_id . '_' . $leaveType->leave_type_id;
        
        if (in_array($key, $this->processedEmployees)) {
            return; // Skip if already processed
        }
        
        $this->processedEmployees[] = $key;

        // Get active financial year
        $financialYear = FinancialYear::where('status', 1)->first();
        
        if (!$financialYear) {
            $financialYear = FinancialYear::orderBy('start_date', 'desc')->first();
        }

        try {
            // Check if an adjustment already exists for this employee with same reason
            $existingAdjustment = LeaveAdjustment::where('employee_id', $employee->employee_id)
                ->where('leave_type_id', $leaveType->leave_type_id)
                ->where('reason', 'like', '%Migrated leave days balance%')
                ->first();

            if (!$existingAdjustment) {
                LeaveAdjustment::create([
                    'employee_id' => $employee->employee_id,
                    'leave_type_id' => $leaveType->leave_type_id,
                    'financial_year_id' => $financialYear ? $financialYear->id : null,
                    'adjustment_type' => 'add',
                    'adjustment_days' => $availableDays,
                    'reason' => 'Migrated leave days balance',
                    'created_by' => Auth::user()->id,
                    'adjusted_by' => Auth::user()->id,
                    'adjustment_date' => now(),
                    'status' => 'approved',
                    'approved_by' => Auth::user()->id,
                    'approved_at' => now(),
                ]);
                
                $this->adjustmentCount++;
            }
        } catch (\Exception $e) {
            // Log error but don't stop the import
            \Illuminate\Support\Facades\Log::error('Failed to create leave adjustment for employee ' . $employee->employee_id . ': ' . $e->getMessage());
        }
    }

    public function rules(): array
    {
        return [
            'staff_no' => 'required',
            'leave_start_date' => 'required',
            'leave_end_date' => 'required',
        ];
    }

    public function getErrors()
    {
        return $this->errors;
    }

    public function getSuccessCount()
    {
        return $this->successCount;
    }

    public function getAdjustmentCount()
    {
        return $this->adjustmentCount;
    }

    /**
     * Calculate number of leave days excluding weekends and holidays.
     */
    private function calculateLeaveDays($fromDate, $toDate)
    {
        $holidays = DB::select(DB::raw('call SP_getHoliday("' . $fromDate . '","' . $toDate . '")'));
        $public_holidays = [];
        foreach ($holidays as $holiday) {
            $start_date = $holiday->from_date;
            $end_date = $holiday->to_date;
            while (strtotime($start_date) <= strtotime($end_date)) {
                $public_holidays[] = $start_date;
                $start_date = date("Y-m-d", strtotime("+1 day", strtotime($start_date)));
            }
        }

        $weeklyHolidays = DB::select(DB::raw('call SP_getWeeklyHoliday()'));
        $weeklyHolidayArray = [];
        foreach ($weeklyHolidays as $weeklyHoliday) {
            $weeklyHolidayArray[] = $weeklyHoliday->day_name;
        }

        $target = strtotime($fromDate);
        $countDay = 0;
        while ($target <= strtotime($toDate)) {
            $value = date("Y-m-d", $target);
            $target += (60 * 60 * 24);

            $timestamp = strtotime($value);
            $dayName = date("l", $timestamp);

            if (!in_array($value, $public_holidays) && !in_array($dayName, $weeklyHolidayArray)) {
                $countDay++;
            }
        }

        return $countDay;
    }
}
