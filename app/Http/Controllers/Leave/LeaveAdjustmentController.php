<?php

namespace App\Http\Controllers\Leave;

use App\Http\Controllers\Controller;
use App\Models\LeaveAdjustment;
use App\Models\Employee;
use App\Models\LeaveType;
use App\Models\FinancialYear;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class LeaveAdjustmentController extends Controller
{
    /**
     * Display a listing of leave adjustments
     */
    public function index(Request $request)
    {
        try {


            $query = LeaveAdjustment::with(['employee', 'leaveType', 'financialYear', 'creator', 'approver'])
                ->orderBy('created_at', 'desc');

            // Apply filters
            if ($request->filled('employee_id')) {
                $query->where('employee_id', $request->employee_id);
            }

            if ($request->filled('leave_type_id')) {
                $query->where('leave_type_id', $request->leave_type_id);
            }

            if ($request->filled('financial_year_id')) {
                $query->where('financial_year_id', $request->financial_year_id);
            }

            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            $adjustments = $query->paginate(500);
            $employees = Employee::where('status', 1)->orderBy('first_name')->get();
            $leaveTypes = LeaveType::where('status', 1)->get();
            $financialYears = FinancialYear::orderBy('start_date', 'desc')->get();



            return view('admin.leave.adjustments.index', compact(
                'adjustments',
                'employees',
                'leaveTypes',
                'financialYears'
            ));
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()], 500);
        }
    }

    /**
     * Show the form for creating a new adjustment
     */
    public function create()
    {
        $employees = Employee::where('status', 1)->orderBy('first_name')->get();
        $leaveTypes = LeaveType::where('status', 1)->get();
        $financialYears = FinancialYear::orderBy('start_date', 'desc')->get();
        $currentFinancialYear = getCurrentFinancialYear();

        return view('admin.leave.adjustments.create', compact(
            'employees',
            'leaveTypes',
            'financialYears',
            'currentFinancialYear'
        ));
    }

    /**
     * Store a newly created adjustment
     */
    public function store(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employee,employee_id',
            'leave_type_id' => 'required|exists:leave_type,leave_type_id',
            'financial_year_id' => 'required|exists:financial_years,id',
            'adjustment_type' => 'required|in:add,deduct',
            'days' => 'required|numeric|min:0.01|max:365',
            'reason' => 'required|string',
        ]);

        try {
            DB::beginTransaction();

            $adjustment = LeaveAdjustment::create([
                'employee_id' => $request->employee_id,
                'leave_type_id' => $request->leave_type_id,
                'financial_year_id' => $request->financial_year_id,
                'adjustment_type' => $request->adjustment_type,
                'adjustment_days' => $request->days,
                'reason' => $request->reason,
                'created_by' => Auth::id(),
                'status' => 'approved', // Auto-approve for now
                'approved_by' => Auth::id(),
                'approved_at' => now(),
            ]);

            DB::commit();

            return redirect()->route('leave.adjustments.index')
                ->with('success', 'Leave adjustment created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Leave adjustment creation failed: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to create leave adjustment. Please try again.');
        }
    }

    /**
     * Display the specified adjustment
     */
    public function show($id)
    {
        $adjustment = LeaveAdjustment::with(['employee', 'leaveType', 'financialYear', 'creator', 'approver'])
            ->findOrFail($id);

        return view('admin.leave.adjustments.show', compact('adjustment'));
    }

    /**
     * Show the form for editing the specified adjustment
     */
    public function edit($id)
    {
        $adjustment = LeaveAdjustment::findOrFail($id);

        // Only allow editing pending adjustments
        if ($adjustment->status !== 'pending') {
            return redirect()->route('leave.adjustments.index')
                ->with('error', 'Only pending adjustments can be edited.');
        }

        $employees = Employee::where('status', 1)->orderBy('first_name')->get();
        $leaveTypes = LeaveType::where('status', 1)->get();
        $financialYears = FinancialYear::orderBy('start_date', 'desc')->get();

        return view('admin.leave.adjustments.edit', compact(
            'adjustment',
            'employees',
            'leaveTypes',
            'financialYears'
        ));
    }

    /**
     * Update the specified adjustment
     */
    public function update(Request $request, $id)
    {
        $adjustment = LeaveAdjustment::findOrFail($id);

        // Only allow updating pending adjustments
        if ($adjustment->status !== 'pending') {
            return redirect()->route('leave.adjustments.index')
                ->with('error', 'Only pending adjustments can be updated.');
        }

        $request->validate([
            'employee_id' => 'required|exists:employee,employee_id',
            'leave_type_id' => 'required|exists:leave_type,leave_type_id',
            'financial_year_id' => 'required|exists:financial_years,id',
            'adjustment_type' => 'required|in:add,deduct',
            'days' => 'required|numeric|min:0.01|max:365',
            'reason' => 'required|string',
        ]);

        try {
            DB::beginTransaction();

            $adjustment->update([
                'employee_id' => $request->employee_id,
                'leave_type_id' => $request->leave_type_id,
                'financial_year_id' => $request->financial_year_id,
                'adjustment_type' => $request->adjustment_type,
                'days' => $request->days,
                'reason' => $request->reason,
            ]);

            DB::commit();

            return redirect()->route('leave.adjustments.index')
                ->with('success', 'Leave adjustment updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Leave adjustment update failed: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to update leave adjustment. Please try again.');
        }
    }

    /**
     * Remove the specified adjustment
     */
    public function destroy($id)
    {
        try {
            $adjustment = LeaveAdjustment::findOrFail($id);
            $adjustment->forceDelete();

            return redirect()->route('leave.adjustments.index')
                ->with('success', 'Leave adjustment deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Leave adjustment deletion failed: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to delete leave adjustment. Please try again.' . $e->getMessage());
        }
    }

    /**
     * Bulk delete leave adjustments
     */
    public function bulkDestroy(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:leave_adjustments,id'
        ]);

        try {
            $count = LeaveAdjustment::whereIn('id', $request->ids)->forceDelete();

            return redirect()->route('leave.adjustments.index')
                ->with('success', "{$count} leave adjustment(s) deleted successfully.");
        } catch (\Exception $e) {
            Log::error('Leave adjustment bulk deletion failed: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to delete leave adjustments. Please try again.');
        }
    }

    /**
     * Download template for bulk upload
     */
    public function downloadTemplate()
    {
        try {
            return \Maatwebsite\Excel\Facades\Excel::download(
                new \App\Exports\LeaveAdjustmentTemplateExport(),
                'leave_adjustments_template.xlsx'
            );
        } catch (\Exception $e) {
            Log::error('Template download error: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Error downloading template: ' . $e->getMessage());
        }
    }

    /**
     * Show import form
     */
    public function showImportForm()
    {
        return view('admin.leave.adjustments.import');
    }

    /**
     * Import leave adjustments from Excel
     */
    public function import(Request $request)
    {
        $request->validate([
            'upload_file' => 'required|file|mimes:xlsx,xls,csv|max:10240'
        ]);

        try {
            $import = new \App\Imports\LeaveAdjustmentImport();
            \Maatwebsite\Excel\Facades\Excel::import($import, $request->file('upload_file'));

            $failures = $import->failures();

            if ($failures->count() > 0) {
                $errorMessages = [];
                foreach ($failures as $failure) {
                    $errorMessages[] = "Row {$failure->row()}: " . implode(', ', $failure->errors());
                }

                return redirect()->route('leave.adjustments.index')
                    ->with('warning', 'Import completed with some errors: ' . implode('<br>', $errorMessages));
            }

            return redirect()->route('leave.adjustments.index')
                ->with('success', 'Leave adjustments imported successfully.');
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();
            $errorMessages = [];

            foreach ($failures as $failure) {
                $errorMessages[] = "Row {$failure->row()}: " . implode(', ', $failure->errors());
            }

            return redirect()->back()
                ->withInput()
                ->with('error', 'Import validation errors: ' . implode('<br>', $errorMessages));
        } catch (\Exception $e) {
            Log::error('Leave adjustment import error: ' . $e->getMessage());
            Log::error($e->getTraceAsString());

            return redirect()->back()
                ->withInput()
                ->with('error', 'Error importing leave adjustments: ' . $e->getMessage());
        }
    }

    /**
     * Get employee's current leave balance for AJAX
     */
    public function getEmployeeBalance(Request $request)
    {
        $employeeId = $request->employee_id;
        $leaveTypeId = $request->leave_type_id;
        $financialYearId = $request->financial_year_id;

        \Log::info('Balance Request:', [
            'employee_id' => $employeeId,
            'leave_type_id' => $leaveTypeId,
            'financial_year_id' => $financialYearId
        ]);

        if (!$employeeId || !$leaveTypeId || !$financialYearId) {
            \Log::error('Missing parameters');
            return response()->json(['error' => 'Missing required parameters'], 400);
        }

        try {
            $employee = Employee::findOrFail($employeeId);
            $financialYear = FinancialYear::findOrFail($financialYearId);

            \Log::info('Found employee and financial year', [
                'employee' => $employee->fullname(),
                'financial_year' => $financialYear->name
            ]);

            // Calculate current balance
            $balance = $this->calculateEmployeeLeaveBalance($employee, $leaveTypeId, $financialYear);

            \Log::info('Calculated balance: ' . $balance);

            return response()->json([
                'success' => true,
                'balance' => $balance
            ]);
        } catch (\Exception $e) {
            \Log::error('Balance calculation error: ' . $e->getMessage());
            \Log::error($e->getTraceAsString());
            return response()->json(['error' => 'Failed to fetch balance: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Calculate employee's leave balance including adjustments
     * Uses the same logic as ReportController::leaveBalances
     */
    private function calculateEmployeeLeaveBalance($employee, $leaveTypeId, $financialYear)
    {
        try {

            $fiscal_start_date = $financialYear->start_date;
            $fiscal_end_date = $financialYear->end_date;

            // Calculate leave used - get all approved leaves and sum only days within fiscal year
            $leaveApplications = DB::table('leave_application')
                ->where('employee_id', $employee->employee_id)
                ->where('final_status', 2) // Approved
                ->where('leave_type_id', $leaveTypeId)
                ->where(function ($query) use ($fiscal_start_date, $fiscal_end_date) {
                    // Get leaves that overlap with the fiscal year
                    $query->where(function ($q) use ($fiscal_start_date, $fiscal_end_date) {
                        $q->whereBetween('application_from_date', [date($fiscal_start_date), date($fiscal_end_date)])
                            ->orWhereBetween('application_to_date', [date($fiscal_start_date), date($fiscal_end_date)])
                            ->orWhere(function ($q2) use ($fiscal_start_date, $fiscal_end_date) {
                                $q2->where('application_from_date', '<=', date($fiscal_start_date))
                                    ->where('application_to_date', '>=', date($fiscal_end_date));
                            });
                    });
                })
                ->get();

            $leaveUsed = 0;
            foreach ($leaveApplications as $application) {
                $leaveUsed += $this->calculateLeaveDaysInPeriod(
                    $employee,
                    $application->application_from_date,
                    $application->application_to_date,
                    $leaveTypeId,
                    $fiscal_start_date,
                    $fiscal_end_date
                );
            }


            // Get total earned leave days based on joining date relative to financial year
            $totalDays = $employee->getEarnedLeaveDays($leaveTypeId, $financialYear->id);


            // Get rollover days
            $rolloverDays = DB::table('leave_rollovers')
                ->where('employee_id', $employee->employee_id)
                ->where('final_status', 2) // Approved
                ->where('financial_year_id', $financialYear->id)
                ->where('leave_type_id', $leaveTypeId)
                ->value('days_requested') ?? 0;


            // Get adjustments for this financial year
            $adjustmentTotal = 0;
            $adjustments = LeaveAdjustment::approved()
                ->forEmployee($employee->employee_id)
                ->forLeaveType($leaveTypeId)
                ->forFinancialYear($financialYear->id)
                ->get();

            foreach ($adjustments as $adjustment) {
                if ($adjustment->adjustment_type === 'add') {
                    $adjustmentTotal += $adjustment->days;
                } else {
                    $adjustmentTotal -= $adjustment->days;
                }
            }

            // Calculate balance: Entitlement + Rollover + Adjustments - Consumed
            $balance = ($totalDays + $rolloverDays + $adjustmentTotal) - $leaveUsed;


            return round($balance, 2);
        } catch (\Exception $e) {
            \Log::error('Error in calculateEmployeeLeaveBalance: ' . $e->getMessage());
            \Log::error($e->getTraceAsString());
            throw $e;
        }
    }

    /**
     * Calculate leave days in a specific period (copied from ReportController)
     */
    private function calculateLeaveDaysInPeriod($employee, $leaveStartDate, $leaveEndDate, $leaveTypeId, $fiscalYearStart, $fiscalYearEnd)
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

        $settings = \App\Models\LeaveGroupSetting::where('leave_group_id', $leaveGroup->id)
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
        $holidays = \App\Models\HolidayDetails::whereIn('holiday_id', $affectingHolidays)
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
        for ($date = $overlapStart->copy(); $date->lte($overlapEnd); $date->addDay()) {
            $dayName = strtolower($date->format('l'));

            // Count only if not a weekend or holiday
            if (!in_array($date->format('Y-m-d'), $holidays) && !in_array($dayName, $weekendDays)) {
                $leaveDays++;
            }
        }

        return $leaveDays;
    }
}
