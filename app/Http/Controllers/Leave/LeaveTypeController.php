<?php
/*
 * Copyright (c) 2023/9/6 sw@stawitech
 */

namespace App\Http\Controllers\Leave;

use App\Http\Controllers\Controller;
use App\Http\Requests\AddRolloverLeaveRequest;
use App\Http\Requests\LeaveTypeRequest;
use App\LeaveRollover;
use App\Lib\Enumerations\LeaveStatus;
use App\Models\Employee;
use App\Models\EmployeeLeavegroup;
use App\Models\FinancialYear;
use App\Models\LeaveAdjustment;
use App\Models\LeaveApplication;
use App\Models\LeaveType;
use App\Models\User;
use App\Models\LeaveGroupSetting;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use App\Helpers\LeaveCalculator;

class LeaveTypeController extends Controller
{
    use LeaveCalculator;
    protected const BATCH_SIZE = 100;
    // Chunk size for database queries
    protected const CHUNK_SIZE = 500;

    public function index()
    {
        $results             = LeaveType::OrderBy('leave_type_id', 'asc')->where('status', 1)->get();
        $signed_in_user_role = User::select('role_id')->where('id', session('logged_session_data.id'))->pluck('role_id')->first();

        return view('admin.leave.leaveType.index', ['results' => $results, 'signed_in_user_role' => $signed_in_user_role]);
    }

    public function create()
    {
        $signed_in_user_role = User::select('role_id')->where('id', session('logged_session_data.id'))->pluck('role_id')->first();

        return view('admin.leave.leaveType.form', ['signed_in_user_role' => $signed_in_user_role]);
    }

    public function store(LeaveTypeRequest $request)
    {
        $input = $request->all();
        try {
            LeaveType::create($input);
            $bug = 0;
        } catch (\Exception $e) {
            $bug = $e->getMessage();
        }

        if ($bug == 0) {
            return redirect()->route('leaveType.index')->with('success', 'Leave Type successfully saved.');
        } else {
            return redirect()->route('leaveType.index')->with('error', 'An error occured, please try again. If the problem persists, contact Support for assistance. ');
        }
    }

    public function edit($id)
    {
        $signed_in_user_role = User::select('role_id')->where('id', session('logged_session_data.id'))->pluck('role_id')->first();
        $editModeData        = LeaveType::findOrFail($id);
        return view('admin.leave.leaveType.form', ['editModeData' => $editModeData, 'signed_in_user_role' => $signed_in_user_role]);
    }

    public function update(LeaveTypeRequest $request, $id)
    {
        $data  = LeaveType::findOrFail($id);
        $input = $request->all();
        try {
            $data->update($input);
            $bug = 0;
        } catch (\Exception $e) {
            $bug = $e->getMessage();
        }

        if ($bug == 0) {
            return redirect()->back()->with('success', 'Leave Type successfully updated.');
        } else {
            return redirect()->back()->with('error', 'An error occured, please try again. If the problem persists, contact Support for assistance. ');
        }
    }

    public function destroy($id)
    {

        $count = LeaveApplication::where('leave_type_id', '=', $id)->count();

        if ($count > 0) {
            return "hasForeignKey";
        }

        try {
            $data = LeaveType::findOrFail($id);
            $data->delete();
            $bug = 0;
        } catch (\Exception $e) {
            $bug = $e->getMessage();
        }

        if ($bug == 0) {
            echo "success";
        } elseif ($bug == 1451) {
            echo 'hasForeignKey';
        } else {
            echo 'error';
        }
    }

    public function rolloverLeavesIndex()
    {

        $signed_in_user_role = User::select('role_id')->where('id', session('logged_session_data.id'))->pluck('role_id')->first();
        $rolloverLeaves      = LeaveRollover::with('employee', 'fiscalYear', 'leaveType')
            ->get();

        return view('admin.leave.leaveType.rolloverLeaves', [
            'rolloverLeaves' => $rolloverLeaves,
            'signed_in_user_role'                                                 => $signed_in_user_role,
        ]);
    }

    public function addRolloverLeave()
    {
        $signed_in_user_role = User::select('role_id')->where('id', session('logged_session_data.id'))->pluck('role_id')->first();
        $employee            = Employee::with('userName')->get();
        $leaveTypes          = LeaveType::all();
        $currentDate          = now();
        $financialYears = FinancialYear::where('start_date', '<=', $currentDate)
            ->where('end_date', '>=', $currentDate)
            ->get();

        return view(
            'admin.leave.leaveType.createRolloverLeave',
            [
                'signed_in_user_role' => $signed_in_user_role,
                'employees'            => $employee,
                'leaveTypes' => $leaveTypes,
                'financialYears'       => $financialYears,
            ]
        );
    }

    public function storeRolloverLeave(AddRolloverLeaveRequest $request)
    {


        $fiscal_year = FinancialYear::whereId($request->fiscal_year)->first();

        $leave_type = LeaveType::where('leave_type_id', $request->leave_type)->first();
        $employee_exist = LeaveRollover::with('employee')
            ->where('employee_id', $request->employee)
            ->where('leave_type_id', $leave_type->leave_type_id)
            ->where('financial_year_id', $fiscal_year->id)
            ->get();

        if (count($employee_exist) > 0) {
            return Redirect::route('rolloverLeaves')->with('error', 'Rollover already exist.');
        }
        $data                        = $request->all();
        $data['employee_id']         = $request->employee;
        $data['days_requested']      = $request->no_of_days;
        $data['final_status']        = 2;
        $data['supervisor_approval'] = 2;
        $data['hr_approval']         = 2;
        $data['financial_year_id']   = $fiscal_year->id;
        $data['leave_type_id']       = $leave_type->leave_type_id;
        $data['ceo_approval']        = 2;
        $data['date_approved']       = date('Y-m-d');

        try {
            LeaveRollover::create($data);
            $bug = 0;
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            \Log::info($e->getMessage());
        }

        if ($bug == 0) {
            return Redirect::route('rolloverLeaves')->with('success', 'Rollover added successfully.');
        } else {
            return Redirect::route('rolloverLeaves')->with('error', 'Something error found !, Please try again.');
        }
    }

    public function editRolloverLeave($id)
    {
        //
    }

    public function updateRolloverLeave($Request)
    {
        //
    }

    public function destroyRollover($id)
    {

        $count = LeaveRollover::where('id', '=', $id)->count();
        try {
            $data = LeaveRollover::findOrFail($id);
            $data->delete();
            $bug = 0;
        } catch (\Exception $e) {
            $bug = $e->getMessage();
        }

        if ($bug == 0) {
            echo "success";
        } elseif ($bug == 1451) {
            echo 'hasForeignKey';
        } else {
            echo 'error';
        }
    }

    public function automaticLeaveRollover()
    {
        $startTime = microtime(true);
        $memoryStart = memory_get_usage(true);

        // Get current financial year
        $currentDate = now();
        $currentFinancialYear = FinancialYear::where('start_date', '<=', $currentDate)
            ->where('end_date', '>=', $currentDate)
            ->first();

        if (!$currentFinancialYear) {
            return Redirect::back()->with('error', 'Current financial year not configured');
        }

        // Get previous financial year
        $previousFinancialYear = FinancialYear::where('end_date', '<', $currentFinancialYear->start_date)
            ->orderByDesc('end_date')
            ->first();

        if (!$previousFinancialYear) {
            return Redirect::back()->with('error', 'No previous financial year configured');
        }

        // Clean up existing entries with 0 days in current financial year
        $this->cleanUpZeroDayRolloverEntries($currentFinancialYear->id);

        // Pre-load all necessary data to avoid N+1 queries
        $preloadedData = $this->preloadRolloverData(
            $currentFinancialYear->id,
            $previousFinancialYear->id,
            $previousFinancialYear->start_date,
            $previousFinancialYear->end_date
        );

        $processedCount = 0;
        $createdCount = 0;
        $skippedCount = 0;

        // Process employees in chunks using cursor for memory efficiency
        Employee::with(['leaveGroup.settings', 'leaveGroup.publicHolidays', 'leaveGroup.weeklyHolidays'])
            ->where('status', 1)
            ->chunkById(self::CHUNK_SIZE, function ($employees) use (
                $currentFinancialYear,
                $previousFinancialYear,
                $preloadedData,
                &$processedCount,
                &$createdCount,
                &$skippedCount
            ) {
                $rolloverInserts = [];

                foreach ($employees as $employee) {
                    $processedCount++;

                    // Skip if no leave group
                    if (!$employee->leaveGroup) {
                        $skippedCount++;
                        continue;
                    }

                    $settings = $employee->leaveGroup->settings;
                    if ($settings->isEmpty()) {
                        $skippedCount++;
                        continue;
                    }

                    foreach ($settings as $setting) {
                        $result = $this->processEmployeeSetting(
                            $employee,
                            $setting,
                            $currentFinancialYear,
                            $previousFinancialYear,
                            $preloadedData
                        );

                        if ($result === null) {
                            $skippedCount++;
                            continue;
                        }

                        if ($result === 'exists') {
                            $skippedCount++;
                            continue;
                        }

                        // Queue for batch insert
                        $rolloverInserts[] = $result;

                        // Batch insert when we reach batch size
                        if (count($rolloverInserts) >= self::BATCH_SIZE) {
                            $this->batchInsertRollovers($rolloverInserts);
                            $createdCount += count($rolloverInserts);
                            $rolloverInserts = [];
                        }
                    }
                }

                // Insert remaining records in this chunk
                if (!empty($rolloverInserts)) {
                    $this->batchInsertRollovers($rolloverInserts);
                    $createdCount += count($rolloverInserts);
                }

                // Force garbage collection to free memory
                if ($processedCount % 1000 === 0) {
                    gc_collect_cycles();
                }
            });

        $duration = round(microtime(true) - $startTime, 2);
        $memoryUsed = round((memory_get_usage(true) - $memoryStart) / 1024 / 1024, 2);

        Log::info("Rollover completed: {$processedCount} processed, {$createdCount} created, {$skippedCount} skipped. Duration: {$duration}s, Memory: {$memoryUsed}MB");

        return Redirect::route('rolloverLeaves')->with(
            'success',
            "Leave rollovers processed successfully. Created: {$createdCount}, Skipped: {$skippedCount} (Duration: {$duration}s)"
        );
    }
    /**
     * Preload all data needed for calculations to avoid N+1 queries
     */
    /**
     * Preload all data needed for calculations to avoid N+1 queries
     */
    private function preloadRolloverData(
        int $currentFyId,
        int $previousFyId,
        string $prevStartDate,
        string $prevEndDate
    ): array {
        // Preload all existing rollovers for current FY (to check existence)
        $existingRollovers = LeaveRollover::where('financial_year_id', $currentFyId)
            ->select('employee_id', 'leave_type_id')
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->employee_id . '_' . $item->leave_type_id => true];
            })
            ->toArray();

        // Preload all approved rollovers from previous FY
        $previousRollovers = LeaveRollover::where('financial_year_id', $previousFyId)
            ->where('final_status', LeaveStatus::APPROVE)
            ->select('employee_id', 'leave_type_id', 'days_requested')
            ->get()
            ->groupBy('employee_id')
            ->map(function ($employeeGroup) {
                return $employeeGroup->keyBy('leave_type_id')
                    ->map(fn($item) => $item->days_requested)
                    ->toArray();
            })
            ->toArray();

        // Preload all leave adjustments for previous FY
        $adjustments = LeaveAdjustment::where('status', 'approved')
            ->where('financial_year_id', $previousFyId)
            ->select('employee_id', 'leave_type_id', 'adjustment_type', 'adjustment_days')
            ->get()
            ->groupBy('employee_id')
            ->map(function ($employeeGroup) {
                return $employeeGroup->groupBy('leave_type_id')
                    ->map(function ($typeGroup) {
                        $total = 0;
                        foreach ($typeGroup as $adj) {
                            $total += $adj->adjustment_type === 'add'
                                ? $adj->adjustment_days
                                : -$adj->adjustment_days;
                        }
                        return $total;
                    })
                    ->toArray();
            })
            ->toArray();

        // Preload all approved leave applications for previous FY
        $leaveApplications = LeaveApplication::where('final_status', LeaveStatus::APPROVE)
            ->where(function ($query) use ($prevStartDate, $prevEndDate) {
                $query->whereBetween('application_from_date', [$prevStartDate, $prevEndDate])
                    ->orWhereBetween('application_to_date', [$prevStartDate, $prevEndDate])
                    ->orWhere(function ($q) use ($prevStartDate, $prevEndDate) {
                        $q->where('application_from_date', '<=', $prevStartDate)
                            ->where('application_to_date', '>=', $prevEndDate);
                    });
            })
            ->select('employee_id', 'leave_type_id', 'application_from_date', 'application_to_date')
            ->get()
            ->groupBy('employee_id')
            ->map(function ($employeeGroup) {
                return $employeeGroup->groupBy('leave_type_id');
            });

        return [
            'existingRollovers' => $existingRollovers,
            'previousRollovers' => $previousRollovers,
            'adjustments' => $adjustments,
            'leaveApplications' => $leaveApplications,
            'prevStartDate' => $prevStartDate,
            'prevEndDate' => $prevEndDate,
        ];
    }

    /**
     * Process a single employee setting and return insert data or status
     * 
     * @return array|null|string Array for insert, null to skip, 'exists' if already exists
     */
    private function processEmployeeSetting(
        $employee,
        $setting,
        $currentFinancialYear,
        $previousFinancialYear,
        array $preloadedData
    ) {
        $leaveTypeId = $setting->leave_type_id;
        $annualEntitlement = $setting->annual_entitlement;
        $maxCarryover = $setting->max_carryover_days;

        // Skip if max_carryover_days is not greater than 0
        if (!$maxCarryover || $maxCarryover <= 0) {
            return null;
        }

        // Check if rollover already exists using preloaded data
        $key = $employee->employee_id . '_' . $leaveTypeId;
        if (isset($preloadedData['existingRollovers'][$key])) {
            return 'exists';
        }

        // Calculate used days using preloaded applications
        $usedDays = $this->calculateUsedDaysFromPreloaded(
            $employee,
            $leaveTypeId,
            $preloadedData
        );

        // Get previous rollover from preloaded data - updated path
        $previousRollover = $preloadedData['previousRollovers'][$employee->employee_id][$leaveTypeId] ?? 0;

        // Get net adjustment from preloaded data - updated path
        $netAdjustment = $preloadedData['adjustments'][$employee->employee_id][$leaveTypeId] ?? 0;

        // Calculate available days
        $availableDays = ($annualEntitlement + $previousRollover + $netAdjustment) - $usedDays;
        $availableDays = max(0, $availableDays);

        // Calculate final carryover
        $carryoverDays = min($availableDays, $maxCarryover);

        if ($carryoverDays <= 0) {
            return null;
        }

        return [
            'employee_id' => $employee->employee_id,
            'leave_type_id' => $leaveTypeId,
            'financial_year_id' => $currentFinancialYear->id,
            'previous_financial_year_id' => $previousFinancialYear->id,
            'days_requested' => $carryoverDays,
            'final_status' => LeaveStatus::APPROVE,
            'date_approved' => now(),
            'default_rollover' => $maxCarryover,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    /**
     * Calculate used days from preloaded leave applications
     */
    private function calculateUsedDaysFromPreloaded(
        $employee,
        int $leaveTypeId,
        array $preloadedData
    ): float {
        $applications = $preloadedData['leaveApplications'][$employee->employee_id][$leaveTypeId] ?? collect();

        if ($applications->isEmpty()) {
            return 0;
        }

        $totalDays = 0;
        $periodStart = Carbon::parse($preloadedData['prevStartDate']);
        $periodEnd = Carbon::parse($preloadedData['prevEndDate']);

        foreach ($applications as $leave) {
            $totalDays += $this->calculateLeaveDaysInPeriod(
                $employee,
                $leave->application_from_date,
                $leave->application_to_date,
                $leaveTypeId,
                $preloadedData['prevStartDate'],
                $preloadedData['prevEndDate']
            );
        }

        return $totalDays;
    }

    /**
     * Batch insert rollover records for better performance
     */
    private function batchInsertRollovers(array $records): void
    {
        if (empty($records)) {
            return;
        }

        try {
            // Use insert for better performance than individual creates
            LeaveRollover::insert($records);
        } catch (\Exception $e) {
            Log::error("Batch insert failed: " . $e->getMessage());

            // Fallback to individual inserts if batch fails
            foreach ($records as $record) {
                try {
                    LeaveRollover::create($record);
                } catch (\Exception $e2) {
                    Log::error("Individual rollover insert failed for employee {$record['employee_id']}: " . $e2->getMessage());
                }
            }
        }
    }

    /**
     * Get net leave adjustments - kept for compatibility, but preloading is preferred
     */
    private function getNetLeaveAdjustments($employeeId, $leaveTypeId, $financialYearId)
    {
        // This is now handled by preloading, but kept for other uses
        $adjustments = LeaveAdjustment::where('status', 'approved')
            ->where('employee_id', $employeeId)
            ->where('leave_type_id', $leaveTypeId)
            ->where('financial_year_id', $financialYearId)
            ->get();

        $total = 0;
        foreach ($adjustments as $adjustment) {
            $total += $adjustment->adjustment_type === 'add'
                ? $adjustment->adjustment_days
                : -$adjustment->adjustment_days;
        }

        return $total;
    }

    /**
     * Calculate used leave days in a period - kept for non-preloaded usage
     */
    private function calculateUsedLeaveDaysInPeriod($employee, $leaveTypeId, $periodStart, $periodEnd)
    {
        // Get approved leaves that overlap with the period
        $approvedLeaves = LeaveApplication::where('employee_id', $employee->employee_id)
            ->where('leave_type_id', $leaveTypeId)
            ->where('final_status', LeaveStatus::APPROVE)
            ->where(function ($query) use ($periodStart, $periodEnd) {
                $query->whereBetween('application_from_date', [$periodStart, $periodEnd])
                    ->orWhereBetween('application_to_date', [$periodStart, $periodEnd])
                    ->orWhere(function ($q) use ($periodStart, $periodEnd) {
                        $q->where('application_from_date', '<=', $periodStart)
                            ->where('application_to_date', '>=', $periodEnd);
                    });
            })
            ->get();

        $totalDaysUsed = 0;

        foreach ($approvedLeaves as $leave) {
            $totalDaysUsed += $this->calculateLeaveDaysInPeriod(
                $employee,
                $leave->application_from_date,
                $leave->application_to_date,
                $leaveTypeId,
                $periodStart,
                $periodEnd
            );
        }

        return $totalDaysUsed;
    }




    /**
     * Calculate used leave days in a specific period based on application dates
     * Uses proper overlap calculation similar to LeaveRepository
     *
     * @param Employee $employee
     * @param int $leaveTypeId
     * @param string $periodStart
     * @param string $periodEnd
     * @return float
     */


    private function countWorkingDays(Carbon $start, Carbon $end)
    {
        $days = 0;
        $current = $start->copy();

        while ($current->lessThanOrEqualTo($end)) {
            // Exclude Saturday (6) and Sunday (0)
            if (!in_array($current->dayOfWeek, [0, 6])) {
                $days++;
            }
            $current->addDay();
        }

        return $days;
    }

    /**
     * Clean up existing rollover entries with 0 days in the specified financial year
     */
    public function cleanUpZeroDayRolloverEntries($financialYearId)
    {
        try {
            LeaveRollover::where('financial_year_id', $financialYearId)
                ->where('days_requested', 0)
                ->delete();
        } catch (\Exception $e) {
            Log::error("Failed to clean up zero day rollover entries: " . $e->getMessage());
        }
    }
}
