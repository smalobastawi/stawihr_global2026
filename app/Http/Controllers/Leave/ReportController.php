<?php
/*
 * Copyright (c) 2023/9/6 sw@stawitech
 */

namespace App\Http\Controllers\Leave;

use Carbon\Carbon;
use App\Models\User;
use App\LeaveRollover;
use App\Models\Location;
use App\Models\Employee;
use App\Models\LeaveType;
use App\Models\WorkShift;
use App\Models\Department;
use App\Models\Designation;
use App\Models\EmployeeType;
use Illuminate\Http\Request;
use App\Models\LeaveApplication;
use App\Models\PrintHeadSetting;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Lib\Enumerations\GeneralStatus;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Lib\Enumerations\LeaveStatus;
use App\Models\FinancialYear;
use App\Models\LeaveGroupSetting;
use App\Models\HolidayDetails;
use App\Models\LeaveAdjustment;
use App\Repositories\LeaveRepository;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cache;

class ReportController extends Controller
{
    protected $leaveRepository;

    public function __construct(LeaveRepository $leaveRepository)
    {
        $this->leaveRepository = $leaveRepository;
    }

    private function getRolledOverLeaves($employeeIds, $fiscalStart, $fiscalEnd)
    {
        return LeaveRollover::whereIn('employee_id', $employeeIds)
            ->whereBetween('date_approved', [$fiscalStart, $fiscalEnd])
            ->where('final_status', 2)
            ->get()
            ->keyBy('employee_id');
    }


    private function calculateLeaveDaysInPeriod($employee, $leaveStartDate, $leaveEndDate, $leaveTypeId, $fiscalYearStart, $fiscalYearEnd)
    {
        $leaveStart = Carbon::parse($leaveStartDate);
        $leaveEnd = Carbon::parse($leaveEndDate);
        $fiscalStart = Carbon::parse($fiscalYearStart);
        $fiscalEnd = Carbon::parse($fiscalYearEnd);

        // Determine the overlap period between leave and fiscal year
        // This ensures we only count days that fall within the fiscal year
        $overlapStart = $leaveStart->greaterThan($fiscalStart) ? $leaveStart : $fiscalStart;
        $overlapEnd = $leaveEnd->lessThan($fiscalEnd) ? $leaveEnd : $fiscalEnd;

        // If no overlap, return 0 (no days within the fiscal year)
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
        // This includes weekends and holidays
        if ($settings->applicable_on === 'calendar_days') {
            return $overlapStart->diffInDays($overlapEnd) + 1;
        }

        // For working_days, exclude weekends and holidays
        // Get all public holidays that affect this leave group
        $affectingHolidays = $leaveGroup->publicHolidays->pluck('holiday_id')->toArray();

        // Fetch and expand holiday date ranges into individual dates
        $holidays = HolidayDetails::whereIn('holiday_id', $affectingHolidays)
            ->where('status', 1)
            ->get()
            ->flatMap(function ($holiday) {
                return Carbon::parse($holiday->from_date)->toPeriod($holiday->to_date)->toArray();
            })
            ->map(fn($date) => $date->format('Y-m-d'))
            ->toArray();

        // Get weekly holidays (weekends)
        $weekendDays = $leaveGroup->weeklyHolidays->pluck('day_name')->map(function ($day) {
            return strtolower($day);
        })->toArray();

        // Count individual days within the fiscal year overlap period
        $leaveDays = 0;
        for ($date = $overlapStart->copy(); $date->lte($overlapEnd); $date->addDay()) {
            $dayName = strtolower($date->format('l'));

            // Count only if it's a working day (not a weekend or holiday)
            if (!in_array($date->format('Y-m-d'), $holidays) && !in_array($dayName, $weekendDays)) {
                $leaveDays++;
            }
        }

        return $leaveDays;
    }

    private function getBaseLeaveQuery($employeeIds, $fiscalStart, $fiscalEnd)
    {
        return LeaveApplication::with(['employee', 'leaveType'])
            ->where('final_status', LeaveStatus::APPROVE)
            ->whereBetween('approve_date', [$fiscalStart, $fiscalEnd])
            ->whereIn('employee_id', $employeeIds);
    }

    private function getEmployeeHierarchyIds(?Employee $employee): array
    {
        if (!$employee) {
            return [];
        }

        $ids = [$employee->employee_id]; // Include the employee themselves

        // Get all subordinates (direct and indirect)
        $subordinateIds = $this->getAllSubordinateIds($employee);
        $ids = array_merge($ids, $subordinateIds);

        return array_unique($ids);
    }

    private function getAllSubordinateIds(Employee $employee): array
    {
        $subordinateIds = [];

        // Get direct subordinates
        $directSubordinates = $employee->subordinates()->pluck('employee_id')->toArray();
        $subordinateIds = array_merge($subordinateIds, $directSubordinates);

        // Recursively get indirect subordinates
        foreach ($employee->subordinates as $subordinate) {
            $subordinateIds = array_merge($subordinateIds, $this->getAllSubordinateIds($subordinate));
        }

        return array_unique($subordinateIds);
    }

    private function applyEmployeeFilters(Builder $query, Request $request)
    {
        if ($request->filled('location')) {
            $query->where('location_id', $request->location);
        }

        if ($request->filled('department')) {
            $query->where('department_id', $request->department);
        }

        if ($request->filled('designation')) {
            $query->where('designation_id', $request->designation);
        }

        if ($request->filled('employee_name')) {
            $searchName = $request->employee_name;
            $query->where(function ($q) use ($searchName) {
                $q->where('first_name', 'LIKE', "%{$searchName}%")
                    ->orWhere('middle_name', 'LIKE', "%{$searchName}%")
                    ->orWhere('last_name', 'LIKE', "%{$searchName}%");
            });
        }
    }

    private function prepareResponse(Request $request, $data, $additionalData = [])
    {
        return view('admin.leave.report.employeeLeaveReport', array_merge([
            'results' => $data,
            'employeeList' => Employee::where('status', 1)->get(),
            'from_date' => $request->from_date,
            'to_date' => $request->to_date,
            'employee_id' => $request->employee_id,
            'signed_in_user_role' => User::where('id', session('logged_session_data.id'))->value('role_id'),
        ], $additionalData));
    }

    public function employeeLeaveReport(Request $request)
    {
        $currentUser = Auth::user();
        $currentEmployee = $currentUser->employeeDetails;

        // Get fiscal year dates
        $currentFY = getCurrentFinancialYear();
        $fiscal_start_date = $currentFY->start_date;
        $fiscal_end_date = $currentFY->end_date;

        $signed_in_user_role = User::where('id', session('logged_session_data.id'))->value('role_id');

        // Base employee query - role based
        if ($currentUser->hasRole(['HR Administrator', 'SuperAdmin'])) {
            $employeeQuery = Employee::where('status', GeneralStatus::ACTIVE);
        } else {
            $employeeQuery = Employee::where('status', GeneralStatus::ACTIVE)
                ->where(function ($query) use ($currentEmployee) {
                    $query->where('supervisor_id', $currentEmployee->employee_id)
                        ->orWhere('employee_id', $currentEmployee->employee_id);
                });
        }

        // Apply filters if present
        if ($request->filled('employee_name') || $request->filled('department') || $request->filled('location') || $request->filled('designation')) {
            $this->applyEmployeeFilters($employeeQuery, $request);
        }

        $employeeList = $employeeQuery->orderBy('first_name', 'asc')->take(200)->get();

        // If POST request, handle download
        if ($request->isMethod('POST')) {
            return $this->downloadLeaveReport($request);
        }

        // GET request - show the form with optional filtered results
        $results = [];
        if ($request->filled('employee_id')) {
            $results = LeaveApplication::with(['employee', 'leaveType', 'approveBy'])
                ->where('final_status', LeaveStatus::APPROVE)
                ->where('employee_id', $request->employee_id)
                ->whereBetween('application_date', [
                    dateConvertFormtoDB($request->from_date),
                    dateConvertFormtoDB($request->to_date)
                ])
                ->orderBy('leave_application_id', 'DESC')
                ->get();
        }

        $departments = Department::all();
        $locations = Location::all();
        $designations = Designation::all();
        $leaveTypes = LeaveType::all();

        return view('admin.leave.report.employeeLeaveReport', [
            'results' => $results,
            'employeeList' => $employeeList,
            'from_date' => $request->from_date,
            'to_date' => $request->to_date,
            'employee_id' => $request->employee_id,
            'signed_in_user_role' => $signed_in_user_role,
            'departments' => $departments,
            'locations' => $locations,
            'designations' => $designations,
            'leaveTypes' => $leaveTypes,
        ]);
    }

    public function downloadLeaveReport(Request $request)
    {
        $signed_in_user_role = User::select('role_id')->where('id', session('logged_session_data.id'))->pluck('role_id')->first();


        $employeeInfo = Employee::with('department')->where('employee_id', $request->employee_id)->first();
        $printHead = PrintHeadSetting::first();
        $results = LeaveApplication::with(['employee', 'leaveType', 'approveBy'])
            ->where('final_status', LeaveStatus::APPROVE)
            ->where('employee_id', $request->employee_id)
            ->whereBetween('application_date', [dateConvertFormtoDB($request->from_date), dateConvertFormtoDB($request->to_date)])
            ->orderBy('leave_application_id', 'DESC')
            ->get();
        $data = [
            'results' => $results,
            'form_date' => dateConvertFormtoDB($request->from_date),
            'to_date' => dateConvertFormtoDB($request->to_date),
            'printHead' => $printHead,
            'employee_name' => $employeeInfo->first_name . ' ' . $employeeInfo->last_name,
            'department_name' => $employeeInfo->department->department_name,
        ];

        $pdf = Pdf::loadView('admin.leave.report.pdf.employeeLeaveReportPdf', $data);
        $pdf->setPaper('A4', 'landscape');
        $pageName = $employeeInfo->first_name . "-leave-report.pdf";
        return $pdf->download($pageName);
    }

    public function myLeaveReport(Request $request)
    {

        //fiscal year calculation here
        $currentFY = getCurrentFinancialYear();
        $fiscal_start_date = $currentFY->start_date;
        $fiscal_end_date = $currentFY->start_date;
        $signed_in_user_role = User::select('role_id')
            ->where('id', session('logged_session_data.id'))
            ->pluck('role_id')
            ->first();
        $employeeList = Employee::where('status', 1)
            ->where('employee_id', session('logged_session_data.employee_id'))
            ->get();
        if ($_POST) {
            $results = LeaveApplication::with(['employee', 'leaveType', 'approveBy'])
                ->where('final_status', LeaveStatus::APPROVE)
                ->where('employee_id', session('logged_session_data.employee_id'))
                ->whereBetween('application_date', [dateConvertFormtoDB($request->from_date), dateConvertFormtoDB($request->to_date)])
                ->orderBy('leave_application_id', 'DESC')
                ->get();
        } else {
            $results = LeaveApplication::with(['employee', 'leaveType', 'approveBy'])
                ->where('final_status', LeaveStatus::APPROVE)
                ->where('employee_id', session('logged_session_data.employee_id'))
                ->whereBetween('application_date', [date($fiscal_start_date), date('Y-m-d')])
                ->orderBy('leave_application_id', 'DESC')
                ->get();
        }
        return view('admin.leave.report.myLeaveReport', [
            'results' => $results,
            'employeeList' => $employeeList,
            'from_date' => $request->from_date,
            'to_date' => $request->to_date,
            'signed_in_user_role' => $signed_in_user_role,
            'fiscal_start_date' => $fiscal_start_date,
            'fiscal_end_date' => $fiscal_end_date,
        ]);
    }


    public function downloadMyLeaveReport(Request $request)
    {

        $employeeInfo = Employee::with('department')->where('employee_id', session('logged_session_data.employee_id'))->first();
        $printHead = PrintHeadSetting::first();
        $results = LeaveApplication::with(['employee', 'leaveType', 'approveBy'])
            ->where('final_status', LeaveStatus::APPROVE)
            ->where('employee_id', session('logged_session_data.employee_id'))
            ->whereBetween('application_date', [dateConvertFormtoDB($request->from_date), dateConvertFormtoDB($request->to_date)])
            ->orderBy('leave_application_id', 'DESC')
            ->get();
        $data = [
            'results' => $results,
            'form_date' => dateConvertFormtoDB($request->from_date),
            'to_date' => dateConvertFormtoDB($request->to_date),
            'printHead' => $printHead,
            'employee_name' => $employeeInfo->first_name . ' ' . $employeeInfo->last_name,
            'department_name' => $employeeInfo->department->department_name,
        ];

        $pdf = Pdf::loadView('admin.leave.report.pdf.myLeaveReportPdf', $data);
        $pdf->setPaper('A4', 'landscape');
        $pageName = "my-leave-report.pdf";
        return $pdf->download($pageName);
    }

    public function summaryReport(Request $request)
    {
        $currentUser = Auth::user();
        $currentEmployee = $currentUser->employeeDetails;

        // Get financial year based on selection or default to current
        $financial_year_id = $request->financial_year_id;
        $selectedFY = null;

        if ($financial_year_id) {
            $selectedFY = FinancialYear::find($financial_year_id);
        }

        if (!$selectedFY) {
            $selectedFY = getCurrentFinancialYear();
        }

        $fiscal_start_date = $selectedFY->start_date;
        $fiscal_end_date = $selectedFY->end_date;

        $logged_in_employee = employeeInfo();

        if ($logged_in_employee) {
            $employeesTocheck = Employee::where('supervisor_id', $currentEmployee->employee_id)
                ->orWhere('employee_id', $currentEmployee->employee_id)
                ->pluck('employee_id')
                ->toArray();

            $rolled_over_leaves = LeaveRollover::where('employee_id', $request->employee_id)
                ->where('financial_year_id', $selectedFY->id)
                ->where('final_status', '2')
                ->whereIn('employee_id', $employeesTocheck)
                ->pluck('days_requested')
                ->first();

            $employeeList = $currentEmployee->subordinates()->latest()->get();
        } else {
            $rolled_over_leaves = LeaveRollover::where('employee_id', $request->employee_id)
                ->where('financial_year_id', $selectedFY->id)
                ->where('final_status', '2')
                ->pluck('days_requested')
                ->first();

            $employeeList = Employee::where('status', 1)->get();
        }

        $signed_in_user_role = User::select('role_id')
            ->where('id', session('logged_session_data.id'))
            ->pluck('role_id')
            ->first();

        $result = [];
        if ($_POST) {
            $result = $this->summaryReportDataFormat(
                $request->employee_id,
                $request->from_date,
                $request->to_date,
                $selectedFY->id  // Pass financial year ID
            );
        }


        // Get all financial years for dropdown
        $financialYears = FinancialYear::orderBy('start_date', 'desc')->get();

        $data = [
            'results' => $result,
            'employeeList' => $employeeList,
            'from_date' => $request->from_date,
            'to_date' => $request->to_date,
            'employee_id' => $request->employee_id,
            'financial_year_id' => $selectedFY->id,
            'selected_financial_year' => $selectedFY,
            'financialYears' => $financialYears,
            'signed_in_user_role' => $signed_in_user_role,
            'rolled_over_leaves' => $rolled_over_leaves,
        ];

        return view('admin.leave.report.summaryReport', $data);
    }

    public function summaryReportDataFormat($employee_id, $from_date, $to_date, $financial_year_id = null)
    {
        $employee = Employee::where('employee_id', $employee_id)->first();

        if (!$employee) {
            return [];
        }

        $leaveTypes = $employee->applicableLeaveTypes();
        $result = [];

        // Get financial year for filtering
        if (!$financial_year_id) {
            $currentFY = getCurrentFinancialYear();
            $financial_year_id = $currentFY->id;
        }

        $financialYear = FinancialYear::find($financial_year_id);
        $fiscal_start_date = $financialYear->start_date;
        $fiscal_end_date = $financialYear->end_date;

        // Parse the report date range - CONVERT FROM DISPLAY FORMAT FIRST
        // The dates come in as DD/MM/YYYY format
        $report_start = Carbon::createFromFormat('d/m/Y', $from_date);
        $report_end = Carbon::createFromFormat('d/m/Y', $to_date);

        foreach ($leaveTypes as $leaveType) {
            // Get total entitled/earned days (this already includes the entitlement)
            $entitledDays = $employee->getEarnedLeaveDays($leaveType->leave_type_id, $financial_year_id);

            // Get the annual entitlement from leave group for reference (optional)
            $annualEntitlement = 0;
            if ($employee->leaveGroup) {
                $setting = $employee->leaveGroup->settings()
                    ->where('leave_type_id', $leaveType->leave_type_id)
                    ->first();
                $annualEntitlement = $setting ? $setting->annual_entitlement : 0;
            }

            // Get rolled over leaves for the selected financial year
            $rolledOverDays = LeaveRollover::where('employee_id', $employee_id)
                ->where('final_status', LeaveStatus::APPROVE)
                ->where('financial_year_id', $financial_year_id)
                ->where('leave_type_id', $leaveType->leave_type_id)
                ->value('days_requested') ?? 0;

            // Get leave adjustments for this leave type in the selected financial year
            $adjustmentDays = $this->calculateAdjustmentTotal($employee_id, $leaveType->leave_type_id, $financial_year_id);
            $adjustmentDetails = $this->getAdjustmentBreakdown($employee_id, $leaveType->leave_type_id, $financial_year_id);

            // Calculate total days available (entitled days + rolled over + adjustments)
            $totalDaysAvailable = $entitledDays + $rolledOverDays + $adjustmentDays;

            // Get leave consumed within the selected date range - USING THE CORRECT METHOD
            // Find all approved leaves that overlap with the report date range
            // Use the database format for the query (Y-m-d)
            $leaveApplications = LeaveApplication::where('employee_id', $employee_id)
                ->where('leave_type_id', $leaveType->leave_type_id)
                ->where('final_status', LeaveStatus::APPROVE)
                ->where(function ($query) use ($report_start, $report_end) {
                    $query->whereBetween('application_from_date', [$report_start->format('Y-m-d'), $report_end->format('Y-m-d')])
                        ->orWhereBetween('application_to_date', [$report_start->format('Y-m-d'), $report_end->format('Y-m-d')])
                        ->orWhere(function ($q) use ($report_start, $report_end) {
                            $q->where('application_from_date', '<=', $report_start->format('Y-m-d'))
                                ->where('application_to_date', '>=', $report_end->format('Y-m-d'));
                        });
                })
                ->get();

            $leaveConsumed = 0;
            foreach ($leaveApplications as $leave) {
                $leaveConsumed += $this->calculateLeaveDaysInPeriod(
                    $employee,
                    $leave->application_from_date,
                    $leave->application_to_date,
                    $leave->leave_type_id,
                    $report_start->format('Y-m-d'),
                    $report_end->format('Y-m-d')
                );
            }

            // Calculate current balance
            $currentBalance = $totalDaysAvailable - $leaveConsumed;

            $result[] = [
                'leave_type_id' => $leaveType->leave_type_id,
                'leave_type_name' => $leaveType->leave_type_name,
                'num_of_day' => $annualEntitlement,
                'earned_days' => $entitledDays,
                'rolled_over_leaves' => $rolledOverDays,
                'adjustment_days' => $adjustmentDays,
                'adjustment_details' => $adjustmentDetails,
                'total_days_available' => $totalDaysAvailable,
                'leave_consume' => $leaveConsumed,
                'current_balance' => $currentBalance,
                'calculation_breakdown' => [
                    'entitled' => $entitledDays,
                    'rolled_over' => $rolledOverDays,
                    'adjustment' => $adjustmentDays
                ]
            ];
        }

        return $result;
    }

    private function calculateAdjustmentTotal($employee_id, $leave_type_id, $financial_year_id)
    {
        $adjustments = \App\Models\LeaveAdjustment::where('status', 'approved')
            ->where('employee_id', $employee_id)
            ->where('leave_type_id', $leave_type_id)
            ->where('financial_year_id', $financial_year_id)
            ->get();

        $total = 0;
        foreach ($adjustments as $adjustment) {
            if ($adjustment->adjustment_type === 'add') {
                $total += $adjustment->adjustment_days;
            } else {
                $total -= $adjustment->adjustment_days;
            }
        }

        return $total;
    }

    /**
     * Get detailed breakdown of adjustments (additions vs deductions)
     */
    private function getAdjustmentBreakdown($employee_id, $leave_type_id, $financial_year_id)
    {
        $adjustments = \App\Models\LeaveAdjustment::where('status', 'approved')
            ->where('employee_id', $employee_id)
            ->where('leave_type_id', $leave_type_id)
            ->where('financial_year_id', $financial_year_id)
            ->get();

        $additions = 0;
        $deductions = 0;

        foreach ($adjustments as $adjustment) {
            if ($adjustment->adjustment_type === 'add') {
                $additions += $adjustment->adjustment_days;
            } else {
                $deductions += $adjustment->adjustment_days;
            }
        }

        return [
            'additions' => $additions,
            'deductions' => $deductions,
            'net' => $additions - $deductions,
            'count' => count($adjustments)
        ];
    }


    public function downloadSummaryReport(Request $request)
    {
        $signed_in_user_role = User::select('role_id')->where('id', session('logged_session_data.id'))->pluck('role_id')->first();
        $employeeInfo    = Employee::with('department')->where('employee_id', session('logged_session_data.employee_id'))->first();
        $printHead       = PrintHeadSetting::first();
        $results         = LeaveApplication::with(['employee', 'leaveType', 'approveBy'])
            ->where('status', LeaveStatus::APPROVE)
            ->where('employee_id', session('logged_session_data.employee_id'))
            ->whereBetween('application_date', [dateConvertFormtoDB($request->from_date), dateConvertFormtoDB($request->to_date)])
            ->orderBy('leave_application_id', 'DESC')
            ->get();
        $data = [
            'results'           =>  $results,
            'form_date'         =>  dateConvertFormtoDB($request->from_date),
            'to_date'           =>  dateConvertFormtoDB($request->to_date),
            'printHead'         =>  $printHead,
            'employee_name'     =>  $employeeInfo->first_name . ' ' . $employeeInfo->last_name,
            'department_name'   =>  $employeeInfo->department->department_name,
        ];

        $pdf = Pdf::loadView('admin.leave.report.pdf.myLeaveReportPdf', $data);
        $pdf->setPaper('A4', 'landscape');
        $pageName = "my-leave-report.pdf";
        return $pdf->download($pageName);
    }

    public function fullOrganizationReport(Request $request)
    {
        $cacheKey = 'active_employee_ids';
        $cacheDuration = 3600;
        $currentUser = Auth::user();
        $currentEmployee = $currentUser->employeeDetails;

        // Get base employee query
        if ($currentUser->hasRole(['HR Administrator', 'SuperAdmin'])) {
            $employeesQuery = Employee::where('status', GeneralStatus::ACTIVE);
        } else {
            $employeesQuery = Employee::where('status', GeneralStatus::ACTIVE)
                ->where(function ($query) use ($currentEmployee) {
                    $query->where('supervisor_id', $currentEmployee->employee_id)
                        ->orWhere('employee_id', $currentEmployee->employee_id);
                });
        }

        // Apply filters
        if ($request->filled('filtering')) {

            if ($request->filled('location_id') && !in_array('all', $request->location_id)) {
                $employeesQuery->whereIn('location_id', $request->location_id);
            }

            if ($request->filled('department_id') && !in_array('all', $request->department_id)) {
                $employeesQuery->whereIn('department_id', $request->department_id);
            }


            if ($request->filled('designation_id') && !in_array('all', $request->designation_id)) {
                $employeesQuery->whereIn('designation_id', $request->designation_id);
            }
        } else {
            return view('admin.leave.report.fullOrganizationReport', [
                'results' => [],
                'departments' => Department::all(),
                'locations' => Location::all(),
                'leaveTypes' => LeaveType::all(),
                'designations' => Designation::all(),
                'financialYears' => FinancialYear::all(),
            ]);
        }

        $employees = $employeesQuery->orderBy('first_name', 'asc')->get();
        $currentFY = getCurrentFinancialYear();
        $leaveTypesData = [];

        // Determine which financial year to use
        $fiscalYear = $currentFY;
        if ($request->filled('financial_year_id')) {
            $fiscalYear = FinancialYear::find($request->financial_year_id);
        }

        foreach ($employees as $employee) {
            // Skip employees who joined after the financial year ended
            if ($employee->date_of_joining && $fiscalYear) {
                $joiningDate = Carbon::parse($employee->date_of_joining);
                $fiscalYearEnd = Carbon::parse($fiscalYear->end_date);

                if ($joiningDate->isAfter($fiscalYearEnd)) {
                    continue; // Employee wasn't employed during this financial year
                }
            }

            $leaveTypes = $employee->applicableLeaveTypes();

            // Handle leave type filtering
            if ($request->filled('leave_type_id') && !empty($request->leave_type_id)) {
                if (!in_array('all', (array)$request->leave_type_id)) {
                    $leaveTypes = $leaveTypes->whereIn('leave_type_id', (array)$request->leave_type_id);
                }
            } else {
                continue;
            }

            if ($leaveTypes->isEmpty()) {
                continue;
            }

            foreach ($leaveTypes as $leaveType) {
                $fiscalDates = [$fiscalYear->start_date, $fiscalYear->end_date];

                // Calculate leave used - get all approved leaves and sum only days within fiscal year
                $leaveApplications = LeaveApplication::where('employee_id', $employee->employee_id)
                    ->where('final_status', 2)
                    ->where('leave_type_id', $leaveType->leave_type_id)
                    ->where(function ($query) use ($fiscalDates) {
                        // Get leaves that overlap with the fiscal year
                        $query->where(function ($q) use ($fiscalDates) {
                            $q->whereBetween('application_from_date', $fiscalDates)
                                ->orWhereBetween('application_to_date', $fiscalDates)
                                ->orWhere(function ($q2) use ($fiscalDates) {
                                    $q2->where('application_from_date', '<=', $fiscalDates[0])
                                        ->where('application_to_date', '>=', $fiscalDates[1]);
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
                        $leaveType->leave_type_id,
                        $fiscalYear->start_date,
                        $fiscalYear->end_date
                    );
                }

                // Get total earned leave days
                // Calculate based on joining date relative to financial year
                $totalDays = $employee->getEarnedLeaveDays($leaveType->leave_type_id, $fiscalYear->id);


                // Get rollover days
                $rolloverDays = LeaveRollover::where('employee_id', $employee->employee_id)
                    ->where('final_status', LeaveStatus::APPROVE)
                    ->where('financial_year_id', $fiscalYear->id)
                    ->where('leave_type_id', $leaveType->leave_type_id)
                    ->value('days_requested') ?? 0;

                // Get adjustments for this financial year
                $adjustmentTotal = 0;
                $totalAdditions = 0;
                $totalDeductions = 0;
                $adjustments = \App\Models\LeaveAdjustment::where('status', 'approved')
                    ->where('employee_id', $employee->employee_id)
                    ->where('leave_type_id', $leaveType->leave_type_id)
                    ->where('financial_year_id', $fiscalYear->id)
                    ->get();


                foreach ($adjustments as $adjustment) {
                    if ($adjustment->adjustment_type === 'add') {
                        $adjustmentTotal += $adjustment->adjustment_days;
                        $totalAdditions = $adjustmentTotal;
                    } else {
                        $adjustmentTotal -= $adjustment->adjustment_days;
                        $totalDeductions = abs($adjustmentTotal);
                    }
                }

                $leaveTypesData[] = [
                    'employee_name' => $employee->fullName(),
                    'payroll_number' => $employee->payroll_number ?? 'N/A',
                    'employee_location' => $employee->location?->location_name ?? 'N/A',
                    'employee_department' => $employee->department?->department_name ?? 'N/A',
                    'employee_designation' => $employee->designation?->designation_name ?? 'N/A',
                    'leave_type_name' => $leaveType->leave_type_name,
                    'leave_type_id' => $leaveType->leave_type_id,
                    'totalDays' => $totalDays,
                    'days_used' => $leaveUsed,
                    'roll_over_days' => $rolloverDays,
                    'totalBlance' => ($totalDays + $rolloverDays + $adjustmentTotal) - $leaveUsed,
                    'totalAdditions' => $totalAdditions,
                    'totalSubtracted' => $totalDeductions,
                ];
            }
        }

        return view('admin.leave.report.fullOrganizationReport', [
            'results' => $leaveTypesData,
            'departments' => Department::all(),
            'locations' => Location::all(),
            'leaveTypes' => LeaveType::all(),
            'designations' => Designation::all(),
            'financialYears' => FinancialYear::all(),
            'selectedFinancialYear' => $fiscalYear,
        ]);
    }

    public function generateReport()
    {
        // Get the fiscal year here
        $currentFY = getCurrentFinancialYear();
        $fiscal_start_date = $currentFY->start_date;
        $fiscal_end_date = $currentFY->start_date;
        $results = LeaveApplication::with(['employee', 'leaveType', 'approveBy'])
            ->where('final_status', LeaveStatus::APPROVE)
            ->whereBetween('approve_date', [date($fiscal_start_date), date($fiscal_end_date)])
            ->orderBy('leave_application_id', 'DESC')
            ->get();

        $details = [];
        // Collect details here
        foreach ($results as $result1) {
            $employee_first_name = $result1->employee->first_name;
            $employee_last_name = $result1->employee->last_name;
            $leaveType = $result1->leaveType->leave_type_name;
            $applied_date = dateConvertDBtoForm($result1->application_date);
            $application_from_date = dateConvertDBtoForm($result1->application_from_date);
            $application_to_date = dateConvertDBtoForm($result1->application_to_date);
            $approveDate = dateConvertDBtoForm($result1->hr_approval_date);
            $approvedBy1 = null;

            if ($result1->approveBy->first_name) {
                $approvedBy1 = $result1->approveBy->first_name . ' ' . $result1->approveBy->last_name;
            } else {
                $approvedBy1 = '-';
            }

            $days = $result1->number_of_day;

            $details[] = [
                'Staff Name' => $employee_first_name . ' ' . $employee_last_name,
                'leave Type' => $leaveType,
                'Date Applied' => $applied_date,
                'Start Date' => $application_from_date,
                'End Date' => $application_to_date,
                'Date Approved' => $approveDate,
                'Approved By' => $approvedBy1,
                'Number of days' => $days,
            ];
        }

        return Excel::download(new \App\Exports\ArrayDataExport($details, 'Organization leave report'), 'organization-leave-report.xlsx');
    }

    /*leave report per employee
    /
    */

    public function reportPerStaff(Request $request)
    {
        $employee_id = $request->employee_id;
        $from_date = $request->from_date;
        $to_date = $request->to_date;

        $results = LeaveApplication::with(['employee', 'leaveType', 'approveBy'])
            ->where('final_status', LeaveStatus::APPROVE)
            ->where('employee_id', $employee_id)
            ->whereDate('hr_approval_date', '>=', dateConvertFormtoDB($from_date))
            ->whereDate('hr_approval_date', '<=', dateConvertFormtoDB($to_date))
            ->orderBy('leave_application_id', 'DESC')
            ->get();

        // Collect details here
        $details = [];
        foreach ($results as $result1) {
            $employee_first_name = $result1->employee->first_name;
            $employee_last_name = $result1->employee->last_name;
            $leaveType = $result1->leaveType->leave_type_name;
            $applied_date = dateConvertDBtoForm($result1->application_date);
            $application_from_date = dateConvertDBtoForm($result1->application_from_date);
            $application_to_date = dateConvertDBtoForm($result1->application_to_date);
            $approveDate = dateConvertDBtoForm($result1->hr_approval_date);
            $approvedBy = $result1->approveBy->first_name . ' ' . $result1->approveBy->last_name;
            $days = $result1->number_of_day;

            $details[] = [
                'Staff Name' => $employee_first_name . ' ' . $employee_last_name,
                'leave Type' => $leaveType,
                'Date Applied' => $applied_date,
                'Start Date' => $application_from_date,
                'End Date' => $application_to_date,
                'Date Approved' => $approveDate,
                'Approved By' => $approvedBy,
                'Number of days' => $days,
            ];
        }

        return Excel::download(
            new \App\Exports\ArrayDataExport($details, 'Employee leave report'),
            'employee-leave-report.xlsx'
        );
    }



    public function downloadLeaveBalances(Request $request) {}

    public function leaveBalances(Request $request)
    {

        //fiscal year calculation here
        $currentUser         = Auth::user();
        $currentEmployee = $currentUser->employeeDetails;

        if ($currentUser->hasRole(['HR Administrator', 'SuperAdmin'])) {
            $employeesTocheck = Employee::where('status', 1)->pluck('employee_id')->toArray();
        } else {
            $employeesTocheck = Employee::where('supervisor_id', $currentEmployee->employee_id)->orWhere('employee_id', $currentEmployee->employee_id)->pluck('employee_id')->toArray();
        }
        $currentFY = getCurrentFinancialYear();
        $fiscal_start_date = $currentFY->start_date;
        $fiscal_end_date = $currentFY->start_date;
        $rolled_over_leaves = LeaveRollover::where('employee_id', $request->employee_id)
            ->whereBetween('date_approved', [date($fiscal_start_date), date($fiscal_end_date)])
            ->where('final_status', '2')
            ->pluck('days_requested')->first();
        //

        $signed_in_user_role = User::select('role_id')->where('id', session('logged_session_data.id'))->pluck('role_id')->first();
        // $employeeList = Employee::where('status', 1)->take(200)->get();
        $departments = Department::all();
        $employees = Employee::whereIn('employee_id', $employeesTocheck)->whereIn('employee_id', $employeesTocheck)->orderBy('first_name', 'asc')->where('status', 1)->take(200)->get();
        $result = [];
        if ($request->filled('filtering')) {
            $employeeList = Employee::where('status', 1);
            if ($request->filled('location')) {
                $employeeList = $employeeList->where('location_id', $request->location);
            }
            if ($request->filled('department')) {
                $employeeList = $employeeList->where('department_id', $request->department);
            }
            if ($request->filled('designation')) {
                $employeeList = $employeeList->where('designation_id', $request->designation);
            }

            if ($request->filled('employee_name')) {
                $searchName = $request->employee_name;
                $employeeList = $employeeList->where(function ($query) use ($searchName) {
                    $query->where('first_name', 'LIKE', "%{$searchName}%")
                        ->orWhere('middle_name', 'LIKE', "%{$searchName}%")
                        ->orWhere('last_name', 'LIKE', "%{$searchName}%");
                });
            }

            $employees = $employeeList->orderBy('first_name', 'asc')->take(200)->get();
        }


        $leaveTyesData = [];


        foreach ($employees as $employee) {
            $leaveTypes = $employee->applicableLeaveTypes();
            if ($request->filled('leave_type')) {
                $leaveTypes = $employee->applicableLeaveTypes()->where('leave_type_id', $request->leave_type);
            }



            foreach ($leaveTypes as $leaveType) {
                // Calculate leave used - get all approved leaves and sum only days within fiscal year
                $leaveApplications = LeaveApplication::where('employee_id', $employee->employee_id)
                    ->where('final_status', 2)
                    ->where('leave_type_id', $leaveType->leave_type_id)
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
                        $leaveType->leave_type_id,
                        $fiscal_start_date,
                        $fiscal_end_date
                    );
                }

                // Get total earned leave days based on joining date relative to financial year
                $totalDays = $employee->getEarnedLeaveDays($leaveType->leave_type_id, $currentFY->id);

                $rolloverDays = LeaveRollover::where('employee_id', $employee->employee_id)
                    ->where('final_status', LeaveStatus::APPROVE)
                    ->where('financial_year_id', $currentFY->id)
                    ->where('leave_type_id', $leaveType->leave_type_id)
                    ->value('days_requested') ?? 0;

                // Get adjustments for this financial year
                $adjustmentTotal = 0;
                $adjustments = \App\Models\LeaveAdjustment::where('status', 'approved')
                    ->where('employee_id', $employee->employee_id)
                    ->where('leave_type_id', $leaveType->leave_type_id)
                    ->where('financial_year_id', $currentFY->id)
                    ->get();

                foreach ($adjustments as $adjustment) {
                    if ($adjustment->adjustment_type === 'add') {
                        $adjustmentTotal += $adjustment->adjustment_days;
                    } else {
                        $adjustmentTotal -= $adjustment->adjustment_days;
                    }
                }

                $leaveTyesData[] = [
                    'employee_name' => $employee->fullName(),
                    'employee_location' => $employee->location ? $employee->location->location_name : 'N/A',
                    'employee_department' => $employee->department ? $employee->department->department_name : 'N/A',
                    'employee_designation' => $employee->designation ? $employee->designation->designation_name : 'N/A',
                    'leave_type_name' => $leaveType->leave_type_name,
                    'leave_type_id' => $leaveType->leave_type_id,
                    'totalDays' => $totalDays,
                    'days_used' => $leaveUsed,
                    'roll_over_days' => $rolloverDays,
                    'totalBlance' => ($totalDays + $rolloverDays + $adjustmentTotal) - $leaveUsed,
                ];
            }
        }



        $locations = Location::all();
        $designations = Designation::all();
        $leaveTypes = LeaveType::all();

        if ($request->filled('filtering')) {
            return view('admin.leave.report.filtered_balances')->with([
                'results' => $leaveTyesData,
            ]);
        }


        $data = [
            'results' => $leaveTyesData,
            'departments' => $departments,
            // 'employeeList' => $employeeList,
            'from_date' => $request->from_date,
            'to_date' => $request->to_date,
            'employee_id' => $request->employee_id,
            'signed_in_user_role' => $signed_in_user_role,
            'rolled_over_leaves' => $rolled_over_leaves,
            'locations' => $locations,
            'leaveTypes' => $leaveTypes,

            'designations' => $designations,
        ];

        return view('admin.leave.report.balances', $data);
    }

    public function onLeaveToday(Request $request)
    {
        $login_employee = employeeInfo();
        $date = date('Y-m-d');
        $today = $date;
        $currentUser         = Auth::user();
        $currentEmployee = $currentUser->employeeDetails;
        $hasHRWiseEmployee   = Employee::select('employee_id')->where('supervisor_id', session('logged_session_data.employee_id'))->get()->toArray();
        $signed_in_user_role = User::select('role_id')->where('id', session('logged_session_data.id'))->pluck('role_id')->first();

        // Fiscal year calculation
        $departments       = Department::all();
        $locations          = Location::all();
        $fiscal_year       = getCurrentFinancialYear();
        $fiscal_start_date = $fiscal_year->start_date;
        $fiscal_end_date   = $fiscal_year->end_date;
        $leaveTypes = LeaveType::all();
        $logged_in_employee = employeeInfo();
        $from_date = date('d/m/Y');
        $to_date = date('d/m/Y');
        $employeeQuery = Employee::where('status', GeneralStatus::ACTIVE);

        if ($currentUser->hasRole(['HR Administrator', 'SuperAdmin'])) {
            $query = LeaveApplication::with(['employee', 'leaveType'])

                ->where('final_status', LeaveStatus::APPROVE)


                ->orderBy('leave_application_id', 'desc');
        } else {
            if ($logged_in_employee) {
                $employeesTocheck = Employee::where('supervisor_id', $currentEmployee->employee_id)
                    ->orWhere('employee_id', $currentEmployee->employee_id)
                    ->pluck('employee_id')
                    ->toArray();
                $query = LeaveApplication::with(['employee', 'leaveType'])
                    ->whereIn('employee_id', $employeesTocheck)
                    ->orderBy('status', 'asc')
                    ->orderBy('leave_application_id', 'desc');
            } else {
                $query             = LeaveApplication::with(['employee', 'leaveType'])
                    ->orderBy('status', 'asc')
                    ->orderBy('leave_application_id', 'desc');
            }
        }


        if ($request->filled('filtering')) {
            $from_date = dateConvertFormtoDB($request->input('from_date'));
            $to_date = dateConvertFormtoDB($request->input('to_date'));

            $location_id = $request->input('location_id');
            $department_id = $request->input('department_id');

            if ($from_date && $to_date) {
                // Find leaves where any date in the filtered range is covered by the leave
                $query->where(function ($q) use ($from_date, $to_date) {
                    $q->whereDate('application_from_date', '<=', $to_date)
                        ->whereDate('application_to_date', '>=', $from_date);
                });
            } else {
                // Default: Today's active leaves
                $query->whereDate('application_from_date', '<=', $today)
                    ->whereDate('application_to_date', '>=', $today);
            }

            // Location filter

            if ($location_id) {
                $query->whereHas('employee', function ($q) use ($location_id) {
                    $q->where('location_id', $location_id);
                });
                $employeeQuery->where('location_id', $request->location_id);
            }

            // Department filter
            if ($department_id) {
                $query->whereHas('employee', function ($q) use ($department_id) {
                    $q->where('department_id', $department_id);
                });
                $employeeQuery->where('department_id', $request->department_id);
            }
        } else {
            // Default: Today's active leaves
            $query->whereDate('application_from_date', '<=', $today)
                ->whereDate('application_to_date', '>=', $today);
        }

        $results = $query->get();

        $totalEmployees = $employeeQuery->count();
        $onLeave = $results->count();


        $present = max(0, $totalEmployees - $onLeave);
        return view('admin.leave.report.on-leave-today', [
            'results' => $results,
            'date' => $date,
            'signed_in_user_role' => $signed_in_user_role,
            'start_date' => dateConvertDBtoForm($from_date),
            'end_date' => dateConvertDBtoForm($to_date),
            'locations'            => $locations,
            'leaveTypes'            => $leaveTypes,
            'departments'         => $departments,
            'onLeave' => $onLeave,
            'present' => $present,
        ]);
    }

    /**
     * Monthly Leave Consumption Report
     * Shows leave days taken per month for each employee (Annual Leave only)
     */
    public function monthlyLeaveConsumption(Request $request)
    {

        $currentUser = Auth::user();
        $currentEmployee = $currentUser->employeeDetails;
        $logged_in_employee = employeeInfo();

        // Always load these for the filter dropdowns
        $financialYears = FinancialYear::orderBy('start_date', 'desc')->get();
        $leaveTypes = LeaveType::all();
        $locations = Location::all();
        $departments = Department::all();
        $designations = Designation::all();

        $signed_in_user_role = User::where('id', session('logged_session_data.id'))->value('role_id');

        $reportData = [];
        $monthlyTotals = array_fill(1, 12, 0);
        $selectedFinancialYear = null;

        // Load data ONLY if filtering is applied
        if ($request->filled('filtering')) {


            // Get selected financial year
            if ($request->filled('financial_year_id')) {
                $selectedFinancialYear = FinancialYear::find($request->financial_year_id);
            }

            if (!$selectedFinancialYear) {
                $selectedFinancialYear = getCurrentFinancialYear();
            }

            // Base employee query
            $employeeQuery = Employee::with(['location', 'department', 'designation'])
                ->where('status', 1);

            // Apply filters
            if ($request->filled('location')) {
                $employeeQuery->where('location_id', $request->location);
            }

            if ($request->filled('department')) {
                $employeeQuery->where('department_id', $request->department);
            }

            if ($request->filled('designation')) {
                $employeeQuery->where('designation_id', $request->designation);
            }

            if ($request->filled('employee_name')) {
                $searchName = $request->employee_name;
                $employeeQuery->where(function ($q) use ($searchName) {
                    $q->where('first_name', 'LIKE', "%{$searchName}%")
                        ->orWhere('middle_name', 'LIKE', "%{$searchName}%")
                        ->orWhere('last_name', 'LIKE', "%{$searchName}%");
                });
            }

            // Role-based filtering
            if (!$currentUser->hasRole(['HR Administrator', 'SuperAdmin'])) {
                if ($logged_in_employee) {
                    $employeeIds = $this->getEmployeeHierarchyIds($currentEmployee);
                    $employeeQuery->whereIn('employee_id', $employeeIds);
                }
            }

            // Get employees
            $employees = $employeeQuery->orderBy('first_name', 'asc')->get();

            // Get financial year dates
            $fyStart = Carbon::parse($selectedFinancialYear->start_date);
            $fyEnd = Carbon::parse($selectedFinancialYear->end_date);

            // Get the year for month display (use the year from financial year start)
            $displayYear = $fyStart->format('Y');

            foreach ($employees as $employee) {
                // Initialize employee data
                $employeeData = [
                    'employee_name' => $employee->fullName(),
                    'payroll_number' => $employee->payroll_number ?? 'N/A',
                    'location' => $employee->location->location_name ?? 'N/A',
                    'department' => $employee->department->department_name ?? 'N/A',
                    'designation' => $employee->designation->designation_name ?? 'N/A',
                    'monthly' => array_fill(1, 12, 0),
                    'total' => 0
                ];

                // Get Annual Leave applications for this employee in the financial year
                $leaveApplications = LeaveApplication::with('leaveType')
                    ->where('employee_id', $employee->employee_id)
                    ->where('final_status', LeaveStatus::APPROVE)
                    ->where('application_from_date', '>=', $fyStart)
                    ->where('application_from_date', '<=', $fyEnd)
                    ->whereHas('leaveType', function ($q) {
                        $q->where('leave_type_name', 'LIKE', '%Annual%');
                    })
                    ->get();


                foreach ($leaveApplications as $application) {
                    $fromDate = Carbon::parse($application->application_from_date);
                    $numDays = $application->number_of_day;

                    // Get the month (1-12)
                    $month = $fromDate->month;

                    // Add to employee's monthly count
                    $employeeData['monthly'][$month] += $numDays;
                    $monthlyTotals[$month] += $numDays;
                    $employeeData['total'] += $numDays;
                }

                // Only include employees with leave days or if show_zero checked
                if ($request->filled('show_zero') || $employeeData['total'] > 0) {
                    $reportData[] = $employeeData;
                }
            }

            // Sort by total days descending
            usort($reportData, function ($a, $b) {
                return $b['total'] <=> $a['total'];
            });
        }


        // Always return the view with all data
        return view('admin.leave.report.monthly-consumption', compact(
            'reportData',
            'monthlyTotals',
            'selectedFinancialYear',
            'financialYears',
            'locations',
            'departments',
            'designations',
            'leaveTypes',
            'signed_in_user_role'
        ));
    }
    public function downloadMonthlyLeaveConsumption(Request $request)
    {
        // Get selected financial year (same as view method)
        $selectedFinancialYear = null;
        if ($request->filled('financial_year_id')) {
            $selectedFinancialYear = FinancialYear::find($request->financial_year_id);
        }
        if (!$selectedFinancialYear) {
            $selectedFinancialYear = getCurrentFinancialYear();
        }

        $selectedYear = Carbon::parse($selectedFinancialYear->start_date)->format('Y');
        $fyStart = Carbon::parse($selectedFinancialYear->start_date);
        $fyEnd = Carbon::parse($selectedFinancialYear->end_date);

        // Get filtered employees (same logic as view method)
        $currentUser = Auth::user();
        $currentEmployee = $currentUser->employeeDetails;
        $logged_in_employee = employeeInfo();

        $employeeQuery = Employee::where('status', 1);

        // Apply same filters as in the view method
        if ($request->filled('location')) {
            $employeeQuery->where('location_id', $request->location);
        }
        if ($request->filled('department')) {
            $employeeQuery->where('department_id', $request->department);
        }
        if ($request->filled('designation')) {
            $employeeQuery->where('designation_id', $request->designation);
        }
        if ($request->filled('employee_name')) {
            $searchName = $request->employee_name;
            $employeeQuery->where(function ($q) use ($searchName) {
                $q->where('first_name', 'LIKE', "%{$searchName}%")
                    ->orWhere('middle_name', 'LIKE', "%{$searchName}%")
                    ->orWhere('last_name', 'LIKE', "%{$searchName}%");
            });
        }

        // Role-based filtering
        if (!$currentUser->hasRole(['HR Administrator', 'SuperAdmin'])) {
            if ($logged_in_employee) {
                $employeeIds = $this->getEmployeeHierarchyIds($currentEmployee);
                $employeeQuery->whereIn('employee_id', $employeeIds);
            }
        }

        $employees = $employeeQuery->orderBy('first_name', 'asc')->get();

        $reportData = [];
        $monthlyTotals = array_fill(1, 12, 0);
        $monthNames = [
            'January',
            'February',
            'March',
            'April',
            'May',
            'June',
            'July',
            'August',
            'September',
            'October',
            'November',
            'December'
        ];

        foreach ($employees as $employee) {
            $employeeData = [
                'employee_name' => $employee->fullName(),
                'payroll_number' => $employee->payroll_number ?? 'N/A',
                'location' => $employee->location->location_name ?? 'N/A',
                'department' => $employee->department->department_name ?? 'N/A',
                'designation' => $employee->designation->designation_name ?? 'N/A',
                'monthly' => array_fill(1, 12, 0),
                'total' => 0
            ];

            // Get Annual Leave applications for this employee in the financial year (same as view)
            $leaveApplications = LeaveApplication::with('leaveType')
                ->where('employee_id', $employee->employee_id)
                ->where('final_status', LeaveStatus::APPROVE)
                ->where('application_from_date', '>=', $fyStart)
                ->where('application_from_date', '<=', $fyEnd)
                ->whereHas('leaveType', function ($q) {
                    $q->where('leave_type_name', 'LIKE', '%Annual%');
                })
                ->get();

            foreach ($leaveApplications as $application) {
                $fromDate = Carbon::parse($application->application_from_date);
                $numDays = $application->number_of_day;

                // Get the month (1-12)
                $month = $fromDate->month;

                // Add to employee's monthly count
                $employeeData['monthly'][$month] += $numDays;
                $monthlyTotals[$month] += $numDays;
                $employeeData['total'] += $numDays;
            }

            // Only include employees with leave days or if show_zero checked
            if ($request->filled('show_zero') || $employeeData['total'] > 0) {
                $reportData[] = $employeeData;
            }
        }

        // Sort by total days descending
        usort($reportData, function ($a, $b) {
            return $b['total'] <=> $a['total'];
        });

        $printHead = PrintHeadSetting::first();

        $data = [
            'reportData' => $reportData,
            'monthlyTotals' => $monthlyTotals,
            'selectedYear' => $selectedYear,
            'monthNames' => $monthNames,
            'printHead' => $printHead,
            'generatedDate' => date('d/m/Y H:i:s'),
        ];

        $pdf = Pdf::loadView('admin.leave.report.monthly-consumption-pdf', $data);
        $pdf->setPaper('A4', 'landscape');

        return $pdf->download("monthly-leave-consumption-{$selectedYear}.pdf");
    }

    /**
     * Export Monthly Leave Consumption Report as Excel
     */
    public function exportMonthlyLeaveConsumption(Request $request)
    {
        // Get selected financial year (same as view method)
        $selectedFinancialYear = null;
        if ($request->filled('financial_year_id')) {
            $selectedFinancialYear = FinancialYear::find($request->financial_year_id);
        }
        if (!$selectedFinancialYear) {
            $selectedFinancialYear = getCurrentFinancialYear();
        }

        $selectedYear = Carbon::parse($selectedFinancialYear->start_date)->format('Y');

        // Get the data using the updated helper method
        $reportData = $this->getMonthlyConsumptionData($request, $selectedFinancialYear);

        return Excel::download(
            new \App\Exports\MonthlyLeaveConsumptionExport(
                $reportData['data'],
                $reportData['totals'],
                $selectedYear
            ),
            "monthly-leave-consumption-{$selectedYear}.xlsx"
        );
    }

    /**
     * Helper method to get monthly consumption data
     * Uses FinancialYear object for proper fiscal year date filtering
     */
    private function getMonthlyConsumptionData($request, $selectedFinancialYear)
    {
        $currentUser = Auth::user();
        $currentEmployee = $currentUser->employeeDetails;
        $logged_in_employee = employeeInfo();

        $fyStart = Carbon::parse($selectedFinancialYear->start_date);
        $fyEnd = Carbon::parse($selectedFinancialYear->end_date);

        $employeeQuery = Employee::where('status', 1);

        if ($request->filled('location')) {
            $employeeQuery->where('location_id', $request->location);
        }
        if ($request->filled('department')) {
            $employeeQuery->where('department_id', $request->department);
        }
        if ($request->filled('designation')) {
            $employeeQuery->where('designation_id', $request->designation);
        }
        if ($request->filled('employee_name')) {
            $searchName = $request->employee_name;
            $employeeQuery->where(function ($q) use ($searchName) {
                $q->where('first_name', 'LIKE', "%{$searchName}%")
                    ->orWhere('middle_name', 'LIKE', "%{$searchName}%")
                    ->orWhere('last_name', 'LIKE', "%{$searchName}%");
            });
        }

        // Role-based filtering (same as view)
        if (!$currentUser->hasRole(['HR Administrator', 'SuperAdmin'])) {
            if ($logged_in_employee) {
                $employeeIds = $this->getEmployeeHierarchyIds($currentEmployee);
                $employeeQuery->whereIn('employee_id', $employeeIds);
            }
        }

        $employees = $employeeQuery->orderBy('first_name', 'asc')->get();

        $reportData = [];
        $monthlyTotals = array_fill(1, 12, 0);

        foreach ($employees as $employee) {
            $employeeData = [
                'employee_name' => $employee->fullName(),
                'payroll_number' => $employee->payroll_number ?? 'N/A',
                'location' => $employee->location->location_name ?? 'N/A',
                'department' => $employee->department->department_name ?? 'N/A',
                'designation' => $employee->designation->designation_name ?? 'N/A',
                'monthly' => array_fill(1, 12, 0),
                'total' => 0
            ];

            // Get Annual Leave applications for this employee in the financial year (same as view)
            $leaveApplications = LeaveApplication::with('leaveType')
                ->where('employee_id', $employee->employee_id)
                ->where('final_status', LeaveStatus::APPROVE)
                ->where('application_from_date', '>=', $fyStart)
                ->where('application_from_date', '<=', $fyEnd)
                ->whereHas('leaveType', function ($q) {
                    $q->where('leave_type_name', 'LIKE', '%Annual%');
                })
                ->get();

            foreach ($leaveApplications as $application) {
                $fromDate = Carbon::parse($application->application_from_date);
                $numDays = $application->number_of_day;

                // Get the month (1-12)
                $month = $fromDate->month;

                // Add to employee's monthly count
                $employeeData['monthly'][$month] += $numDays;
                $monthlyTotals[$month] += $numDays;
                $employeeData['total'] += $numDays;
            }

            // Only include employees with leave days or if show_zero checked
            if ($request->filled('show_zero') || $employeeData['total'] > 0) {
                $reportData[] = $employeeData;
            }
        }

        // Sort by total days descending
        usort($reportData, function ($a, $b) {
            return $b['total'] <=> $a['total'];
        });

        return [
            'data' => $reportData,
            'totals' => $monthlyTotals
        ];
    }

    /**
     * Leave History Report
     * Shows list of all employees with their leave history
     * Clicking on an employee shows their detailed leave entries
     */
    public function leaveHistory(Request $request)
    {
        $currentUser = Auth::user();
        $currentEmployee = $currentUser->employeeDetails;

        // Get base employee query based on role
        if ($currentUser->hasRole(['HR Administrator', 'SuperAdmin'])) {
            $employeeQuery = Employee::with(['department', 'designation'])
                ->where('status', GeneralStatus::ACTIVE);
        } else {
            $employeeQuery = Employee::with(['department', 'designation', 'location'])
                ->where('status', GeneralStatus::ACTIVE)
                ->where(function ($query) use ($currentEmployee) {
                    $query->where('supervisor_id', $currentEmployee->employee_id)
                        ->orWhere('employee_id', $currentEmployee->employee_id);
                });
        }

        // Apply filters
        if ($request->filled('filtering') || $request->filled('work_number') || $request->filled('department') || $request->filled('location') || $request->filled('designation') || $request->filled('employee_name')) {
            if ($request->filled('work_number')) {
                $employeeQuery->where('staff_no', 'LIKE', "%{$request->work_number}%");
            }

            if ($request->filled('department')) {
                $employeeQuery->where('department_id', $request->department);
            }

            if ($request->filled('location')) {
                $employeeQuery->where('location_id', $request->location);
            }

            if ($request->filled('designation')) {
                $employeeQuery->where('designation_id', $request->designation);
            }

            if ($request->filled('employee_name')) {
                $searchName = $request->employee_name;
                $employeeQuery->where(function ($q) use ($searchName) {
                    $q->where('first_name', 'LIKE', "%{$searchName}%")
                        ->orWhere('middle_name', 'LIKE', "%{$searchName}%")
                        ->orWhere('last_name', 'LIKE', "%{$searchName}%");
                });
            }
        }

        // Get current financial year for summary data
        $currentFY = getCurrentFinancialYear();

        $employees = $employeeQuery->orderBy('first_name', 'asc')->get();

        // Get leave data for each employee
        $employeeLeaveData = [];
        foreach ($employees as $employee) {
            // Get all leave applications for this employee
            $leaveQuery = LeaveApplication::where('employee_id', $employee->employee_id);

            // Count total leave applications (all statuses)
            $totalApplications = $leaveQuery->count();

            // Get status breakdown
            $statusCounts = LeaveApplication::where('employee_id', $employee->employee_id)
                ->selectRaw('final_status, COUNT(*) as count')
                ->groupBy('final_status')
                ->pluck('count', 'final_status')
                ->toArray();

            // Count leave days taken this fiscal year (all statuses)
            $leaveDaysTaken = LeaveApplication::where('employee_id', $employee->employee_id)
                ->whereBetween('application_from_date', [$currentFY->start_date, $currentFY->end_date])
                ->sum('number_of_day');

            // Get last leave date (all statuses)
            $lastLeave = LeaveApplication::where('employee_id', $employee->employee_id)
                ->orderBy('application_to_date', 'desc')
                ->first();

            $employeeLeaveData[$employee->employee_id] = [
                'total_applications' => $totalApplications,
                'leave_days_taken' => $leaveDaysTaken ?? 0,
                'last_leave_date' => $lastLeave ? $lastLeave->application_to_date : null,
                'status_counts' => $statusCounts,
                'pending_count' => $statusCounts[LeaveStatus::PENDING] ?? 0,
                'approved_count' => $statusCounts[LeaveStatus::APPROVE] ?? 0,
                'rejected_count' => $statusCounts[LeaveStatus::REJECT] ?? 0,
            ];
        }

        $departments = Department::all();
        $locations = Location::all();
        $designations = Designation::all();

        return view('admin.leave.report.leaveHistory', [
            'employees' => $employees,
            'employeeLeaveData' => $employeeLeaveData,
            'departments' => $departments,
            'locations' => $locations,
            'designations' => $designations,
            'work_number' => $request->work_number,
            'employee_name' => $request->employee_name,
            'department' => $request->department,
            'location' => $request->location,
            'designation' => $request->designation,
        ]);
    }

    /**
     * Individual Employee Leave History Detail Page
     * Shows full leave history for a specific employee on its own page
     */
    public function leaveHistoryDetail($employee_id)
    {
        $currentUser = Auth::user();
        $currentEmployee = $currentUser->employeeDetails;

        // Get the employee
        $employee = Employee::with(['department', 'designation', 'location', 'user'])
            ->where('employee_id', $employee_id)
            ->where('status', GeneralStatus::ACTIVE)
            ->firstOrFail();

        // Check permission - only HR/SuperAdmin can see any employee, others only their subordinates
        if (!$currentUser->hasRole(['HR Administrator', 'SuperAdmin'])) {
            $allowedEmployeeIds = $this->getEmployeeHierarchyIds($currentEmployee);
            if (!in_array($employee_id, $allowedEmployeeIds)) {
                abort(403, 'Unauthorized access');
            }
        }

        // Get current financial year
        $currentFY = getCurrentFinancialYear();

        // Get leave summary data (all statuses)
        $totalApplications = LeaveApplication::where('employee_id', $employee_id)
            ->count();

        $leaveDaysThisFY = LeaveApplication::where('employee_id', $employee_id)
            ->whereBetween('application_from_date', [$currentFY->start_date, $currentFY->end_date])
            ->sum('number_of_day');

        $totalLeaveDaysAllTime = LeaveApplication::where('employee_id', $employee_id)
            ->sum('number_of_day');

        // Get leave history grouped by year (all statuses)
        $leaveHistory = LeaveApplication::with(['leaveType', 'approveBy'])
            ->where('employee_id', $employee_id)
            ->orderBy('application_from_date', 'desc')
            ->get();

        // Group leave history by financial year
        $leaveByYear = [];
        foreach ($leaveHistory as $leave) {
            $leaveYear = date('Y', strtotime($leave->application_from_date));
            if (!isset($leaveByYear[$leaveYear])) {
                $leaveByYear[$leaveYear] = [];
            }
            $leaveByYear[$leaveYear][] = $leave;
        }

        // Get applicable leave types and their balances
        $leaveTypes = $employee->applicableLeaveTypes();
        $leaveBalances = [];
        foreach ($leaveTypes as $leaveType) {
            $earnedDays = $employee->getEarnedLeaveDays($leaveType->leave_type_id, $currentFY->id);
            $usedDays = LeaveApplication::where('employee_id', $employee_id)
                ->where('leave_type_id', $leaveType->leave_type_id)
                ->where('final_status', LeaveStatus::APPROVE)
                ->whereBetween('application_from_date', [$currentFY->start_date, $currentFY->end_date])
                ->sum('number_of_day');

            $leaveBalances[] = [
                'leave_type' => $leaveType,
                'earned' => $earnedDays,
                'used' => $usedDays,
                'balance' => $earnedDays - $usedDays,
            ];
        }

        // Get joining date formatted
        $dateOfJoining = $employee->date_of_joining ? Carbon::parse($employee->date_of_joining)->format('d M Y') : 'N/A';

        return view('admin.leave.report.leaveHistoryDetail', [
            'employee' => $employee,
            'leaveHistory' => $leaveHistory,
            'leaveByYear' => $leaveByYear,
            'totalApplications' => $totalApplications,
            'leaveDaysThisFY' => $leaveDaysThisFY,
            'totalLeaveDaysAllTime' => $totalLeaveDaysAllTime,
            'leaveBalances' => $leaveBalances,
            'currentFY' => $currentFY,
            'dateOfJoining' => $dateOfJoining,
        ]);
    }

    /**
     * Leave Encashment Report
     * Shows all leave encashment entries from payroll and corresponding leave adjustments
     */
    public function leaveEncashmentReport(Request $request)
    {
        $currentUser = Auth::user();
        $currentEmployee = $currentUser->employeeDetails;

        // Get filter parameters
        $financialYearId = $request->financial_year_id;
        $employeeId = $request->employee_id;
        $fromDate = $request->from_date;
        $toDate = $request->to_date;

        // Get financial year
        $financialYear = null;
        if ($financialYearId) {
            $financialYear = FinancialYear::find($financialYearId);
        } else {
            $financialYear = getCurrentFinancialYear();
        }

        // Get all financial years for dropdown
        $financialYears = FinancialYear::orderBy('start_date', 'desc')->get();

        // Get departments and locations for filters
        $departments = Department::all();
        $locations = Location::all();

        // If no financial year, return empty results immediately
        if (!$financialYear) {
            return view('admin.leave.report.leaveEncashmentReport', [
                'encashments' => collect(),
                'employees' => collect(),
                'financialYears' => $financialYears,
                'financialYear' => null,
                'departments' => $departments,
                'locations' => $locations,
                'totalDaysEncashed' => 0,
                'totalEmployees' => 0,
                'employee_id' => $employeeId,
                'department_id' => $request->department_id,
                'location_id' => $request->location_id,
                'from_date' => $fromDate,
                'to_date' => $toDate,
            ]);
        }

        // Build the base query with simpler conditions first
        $encashmentQuery = LeaveAdjustment::query()
            ->select('leave_adjustments.*')
            ->join('employee', 'leave_adjustments.employee_id', '=', 'employee.employee_id')
            ->with(['employee.department', 'leaveType', 'financialYear'])
            ->where('leave_adjustments.status', 'approved')
            ->where('leave_adjustments.adjustment_type', 'deduct')
            ->where('leave_adjustments.financial_year_id', $financialYear->id);

        // Apply date filters
        if ($fromDate && $toDate) {
            $encashmentQuery->whereBetween('leave_adjustments.adjustment_date', [
                dateConvertFormtoDB($fromDate),
                dateConvertFormtoDB($toDate)
            ]);
        }

        // Apply employee filter if provided
        if ($employeeId) {
            $encashmentQuery->where('leave_adjustments.employee_id', $employeeId);
        }

        // Apply role-based filtering
        if (!$currentUser->hasRole(['HR Administrator', 'SuperAdmin'])) {
            $encashmentQuery->where(function ($q) use ($currentEmployee) {
                $q->where('employee.supervisor_id', $currentEmployee->employee_id)
                    ->orWhere('employee.employee_id', $currentEmployee->employee_id);
            });
        }

        // Apply department filter
        if ($request->filled('department_id')) {
            $encashmentQuery->where('employee.department_id', $request->department_id);
        }

        // Apply location filter
        if ($request->filled('location_id')) {
            $encashmentQuery->where('employee.location_id', $request->location_id);
        }

        // Apply encashment reason filter using raw SQL for better performance
        $encashmentQuery->whereRaw("(LOWER(leave_adjustments.reason) LIKE '%encashment%' OR LOWER(leave_adjustments.reason) LIKE '%leave encashment%')");

        $encashments = $encashmentQuery->orderBy('leave_adjustments.adjustment_date', 'desc')->get();

        // Get only employees who have encashments (for the dropdown) - limit columns for performance
        $employeeIds = $encashments->pluck('employee_id')->unique()->toArray();
        $employees = Employee::whereIn('employee_id', $employeeIds)
            ->orderBy('first_name', 'asc')
            ->get(['employee_id', 'first_name', 'last_name', 'payroll_number']);

        // Calculate totals
        $totalDaysEncashed = $encashments->sum('adjustment_days');
        $totalEmployees = $encashments->unique('employee_id')->count();

        return view('admin.leave.report.leaveEncashmentReport', [
            'encashments' => $encashments,
            'employees' => $employees,
            'financialYears' => $financialYears,
            'financialYear' => $financialYear,
            'departments' => $departments,
            'locations' => $locations,
            'totalDaysEncashed' => $totalDaysEncashed,
            'totalEmployees' => $totalEmployees,
            'employee_id' => $employeeId,
            'department_id' => $request->department_id,
            'location_id' => $request->location_id,
            'from_date' => $fromDate,
            'to_date' => $toDate,
        ]);
    }

    /**
     * Download Leave Encashment Report as PDF
     */
    public function downloadLeaveEncashmentReport(Request $request)
    {
        $printHead = PrintHeadSetting::first();

        // Get the same data as the report view
        $currentUser = Auth::user();
        $currentEmployee = $currentUser->employeeDetails;

        $financialYear = null;
        if ($request->filled('financial_year_id')) {
            $financialYear = FinancialYear::find($request->financial_year_id);
        }

        $encashmentQuery = LeaveAdjustment::with(['employee.department', 'leaveType', 'financialYear'])
            ->where('status', 'approved')
            ->where('adjustment_type', 'deduct')
            ->where(function ($q) {
                $q->where('reason', 'like', '%leave encashment%')
                    ->orWhere('reason', 'like', '%Leave Encashment%');
            });

        // Apply role-based filtering
        if (!$currentUser->hasRole(['HR Administrator', 'SuperAdmin'])) {
            $supervisedEmployeeIds = Employee::where('supervisor_id', $currentEmployee->employee_id)
                ->orWhere('employee_id', $currentEmployee->employee_id)
                ->pluck('employee_id')
                ->toArray();
            $encashmentQuery->whereIn('employee_id', $supervisedEmployeeIds);
        }

        if ($request->filled('employee_id')) {
            $encashmentQuery->where('employee_id', $request->employee_id);
        }

        if ($request->filled('from_date') && $request->filled('to_date')) {
            $encashmentQuery->whereBetween('adjustment_date', [
                dateConvertFormtoDB($request->from_date),
                dateConvertFormtoDB($request->to_date)
            ]);
        }

        if ($financialYear) {
            $encashmentQuery->where('financial_year_id', $financialYear->id);
        }

        $encashments = $encashmentQuery->orderBy('adjustment_date', 'desc')->get();

        $totalDaysEncashed = $encashments->sum('adjustment_days');

        $data = [
            'encashments' => $encashments,
            'printHead' => $printHead,
            'financialYear' => $financialYear,
            'totalDaysEncashed' => $totalDaysEncashed,
            'generatedDate' => date('d/m/Y H:i:s'),
        ];

        $pdf = Pdf::loadView('admin.leave.report.pdf.leaveEncashmentReportPdf', $data);
        $pdf->setPaper('A4', 'landscape');

        $filename = 'leave-encashment-report-' . date('Y-m-d') . '.pdf';
        return $pdf->download($filename);
    }
}
