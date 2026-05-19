<?php

namespace App\Http\Controllers\Payroll;

use App\Http\Controllers\Controller;
use App\Lib\Enumerations\ApprovalStatus;
use App\Lib\Enumerations\GeneralStatus;
use App\Models\EmployeeDeductions;
use App\Models\Employee;
use App\Models\PayrollDeductionTypes;
use App\Models\Payroll\DeductionType;
use App\Models\ApprovalWorkflow;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Carbon\Carbon;
use App\Models\User;
use Maatwebsite\Excel\Facades\Excel;

class EmployeeDeductionsController extends Controller
{
    /**
     * Display a listing of employee deductions
     */
    public function index(Request $request)
    {
        $query = EmployeeDeductions::with(['employee', 'payrollDeductionType', 'createdBy', 'approvedBy']);

        // Filter by employee if provided
        if ($request->has('employee_id') && $request->employee_id) {
            $query->where('employee_id', $request->employee_id);
        }

        // Filter by status
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        // Filter by payroll period
        if ($request->has('financial_year_id') && $request->financial_year_id) {
            $query->where('financial_year_id', $request->financial_year_id);
        }

        if ($request->has('payroll_month') && $request->payroll_month) {
            $query->where('payroll_month', $request->payroll_month);
        }

        // Search functionality
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->orWhereHas('employee', function ($empQuery) use ($search) {
                    $empQuery->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                        ->orWhere('payroll_number', 'like', "%{$search}%");
                });
            });
        }

        $results = $query->orderBy('created_at', 'desc')->get();

        // Get filter options
        $employees = Employee::select('employee_id', 'first_name', 'last_name', 'staff_no')
            ->orderBy('first_name')
            ->get();

        $deductionCategories = [
            'loan_repayment' => 'Loan Repayment',
            'advance_repayment' => 'Advance Repayment',
            'tax' => 'Tax',
            'nssf' => 'NSSF',
            'nhif' => 'NHIF',
            'other' => 'Other'
        ];

        $statuses = [
            'active' => 'Active',
            'inactive' => 'Inactive',
            'suspended' => 'Suspended',
            'expired' => 'Expired'
        ];

        $financialYears = \App\Models\FinancialYear::orderBy('start_date', 'desc')->get();
        $activeFinancialYear = \App\Models\FinancialYear::active()->first();

        return view('admin.payroll.employee_deductions.index', compact(
            'results',
            'employees',
            'deductionCategories',
            'statuses',
            'financialYears',
            'activeFinancialYear'
        ));
    }

    /**
     * Show the form for creating a new employee deduction
     */
    public function create(Request $request)
    {
        $employees = Employee::whereHas('employeePayroll')->select('employee_id', 'first_name', 'last_name', 'staff_no')
            ->where('status', GeneralStatus::ACTIVE)
            ->whereHas('employeePayroll')
            ->orderBy('first_name')
            ->get();

        $payrollDeductionTypes = DeductionType::where('is_active', 1)
            ->orderBy('name')
            ->get();

        $deductionCategories = [
            'loan_repayment' => 'Loan Repayment',
            'advance_repayment' => 'Advance Repayment',
            'other' => 'Other',
            'salary_deduction' => 'Salary Deduction',
        ];

        $calculationTypes = [
            'fixed_amount' => 'Fixed Amount',
            'percentage_of_basic' => 'Percentage of Basic Income',
            'daily_rate' => 'Daily Rate'
        ];

        $frequencies = [
            'monthly' => 'Monthly',
            'annually' => 'Annually',
            'one_time' => 'One Time'
        ];

        // Pre-select employee if provided
        $selectedEmployee = null;
        if ($request->has('employee_id')) {
            $selectedEmployee = Employee::find($request->employee_id);
        }

        $financialYears = \App\Models\FinancialYear::orderBy('start_date', 'desc')->get();
        $activeFinancialYear = \App\Models\FinancialYear::active()->first();

        $financialYears = \App\Models\FinancialYear::orderBy('start_date', 'desc')->get();
        $activeFinancialYear = \App\Models\FinancialYear::active()->first();

        return view('admin.payroll.employee_deductions.form', compact(
            'employees',
            'payrollDeductionTypes',
            'deductionCategories',
            'calculationTypes',
            'frequencies',
            'selectedEmployee',
            'financialYears',
            'activeFinancialYear'
        ));
    }

    /**
     * Store a newly created employee deduction
     */
    public function store(Request $request)
    {
        $deductionType = DeductionType::findOrFail($request->payroll_deduction_type_id);
        $calculationType = $deductionType->default_calculation_type;

        $validator = Validator::make($request->all(), [
            'employee_id' => 'required|exists:employee,employee_id',
            'payroll_deduction_type_id' => [
                'required',
                'exists:deduction_types,id',
                Rule::unique('employee_deductions')->where(function ($query) use ($request) {
                    return $query->where('employee_id', $request->employee_id)
                        ->where(function ($q) {
                            $q->whereNull('effective_to')
                                ->orWhere('effective_to', '>=', now());
                        });
                }),
            ],
            'deduction_category' => 'required|in:loan_repayment,advance_repayment,tax,nssf,nhif,other',
            'amount' => 'required_if:calculation_type,fixed_amount|nullable|numeric|min:0',
            'percentage' => 'required_if:calculation_type,percentage_of_basic|nullable|numeric|min:0|max:100',
            'rate' => 'required_if:calculation_type,daily_rate|nullable|numeric|min:0',
            'units' => 'nullable|integer|min:0',
            'limit_per_month' => 'nullable|numeric|min:0',
            'limit_per_year' => 'nullable|numeric|min:0',
            'effective_from' => 'required|date',
            'effective_to' => 'nullable|date|after:effective_from',
            'payroll_year' => 'required|integer|min:2020|max:2050',
            'payroll_month' => 'required|integer|min:1|max:12',
            'frequency' => 'required|in:monthly,weekly,bi_weekly,quarterly,annually,one_time',
            'is_tax_deductible' => 'boolean',
            'is_recurring' => 'boolean',
            'description' => 'nullable|string|max:1000',
            'status' => 'required|integer',
        ]);

        $request->merge(['calculation_type' => $calculationType]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            DB::beginTransaction();

            $input = $request->all();
            $input['created_by'] = Auth::id();

            // Check if approval workflow exists for EmployeeDeductions
            $workflowExists = ApprovalWorkflow::where('model_type', EmployeeDeductions::class)->exists();

            if ($workflowExists) {
                // Workflow exists - set to pending approval
                $input['status'] = GeneralStatus::INACTIVE;
                $input['approval_status'] = ApprovalStatus::DRAFT;
                $input['date_approved'] = null;
            } else {
                // No workflow - auto-approve
                $input['status'] = GeneralStatus::ACTIVE;
                $input['approval_status'] = ApprovalStatus::APPROVED;
                $input['date_approved'] = now();
                $input['approved_by'] = Auth::id();
            }

            // Generate reference number if not provided
            if (empty($input['reference_number'])) {
                $input['reference_number'] = $this->generateReferenceNumber();
            }

            // Set boolean values
            $input['is_recurring'] = $request->has('is_recurring');

            EmployeeDeductions::create($input);

            DB::commit();

            $successMessage = $workflowExists
                ? 'Employee deduction successfully created and pending approval.'
                : 'Employee deduction successfully created and approved.';

            return redirect()->route('employee_deductions.index')
                ->with('success', $successMessage);
        } catch (\Exception $e) {
            DB::rollback();

            // Log the exception for debugging
            \Log::error('Error creating employee deduction: ' . $e->getMessage());

            // Re-throw the exception to display full error details in browser
            throw $e;
        }
    }

    /**
     * Display the specified employee deduction
     */
    public function show($id)
    {
        $deduction = EmployeeDeductions::with([
            'employee.department',
            'employee.designation',
            'employee.workLocation',
            'payrollDeductionType',
            'createdBy',
            'updatedBy',
            'approvedBy'
        ])->findOrFail($id);

        return view('admin.payroll.employee_deductions.show', compact('deduction'));
    }

    /**
     * Show the form for editing the specified employee deduction
     */
    public function edit($id)
    {
        $editModeData = EmployeeDeductions::findOrFail($id);

        $employees = Employee::whereHas('employeePayroll')->select('employee_id', 'first_name', 'last_name', 'staff_no')
            ->where('status', 1)
            ->orderBy('first_name')
            ->get();

        $payrollDeductionTypes = DeductionType::orderBy('name')
            ->get();

        $deductionCategories = [
            'loan_repayment' => 'Loan Repayment',
            'advance_repayment' => 'Advance Repayment',
            'other' => 'Other',
            'salary_deduction' => 'Salary Deduction',
        ];


        $frequencies = [
            'monthly' => 'Monthly',
            'weekly' => 'Weekly',
            'bi_weekly' => 'Bi-Weekly',
            'quarterly' => 'Quarterly',
            'annually' => 'Annually',
            'one_time' => 'One Time'
        ];

        $statuses = [
            'active' => 'Active',
            'inactive' => 'Inactive',
            'suspended' => 'Suspended',
            'expired' => 'Expired'
        ];

        $financialYears = \App\Models\FinancialYear::orderBy('start_date', 'desc')->get();
        $activeFinancialYear = \App\Models\FinancialYear::active()->first();

        return view('admin.payroll.employee_deductions.form', compact(
            'editModeData',
            'employees',
            'payrollDeductionTypes',
            'deductionCategories',
            'frequencies',
            'statuses',
            'financialYears',
            'activeFinancialYear'
        ));
    }

    /**
     * Update the specified employee deduction
     */
    public function update(Request $request, $id)
    {
        $deduction = EmployeeDeductions::findOrFail($id);


        $deductionType = DeductionType::findOrFail($request->payroll_deduction_type_id);

        $calculationType = $deductionType->default_calculation_type;

        $validator = Validator::make($request->all(), [
            'employee_id' => 'required|exists:employee,employee_id',
            'payroll_deduction_type_id' => 'required|exists:deduction_types,id',
            'deduction_category' => 'required|in:loan_repayment,advance_repayment,tax,nssf,nhif,other',
            'amount' => 'required_if:calculation_type,fixed_amount|nullable|numeric|min:0',
            'percentage' => 'required_if:calculation_type,percentage_of_basic|nullable|numeric|min:0|max:100',
            'rate' => 'required_if:calculation_type,daily_rate|nullable|numeric|min:0',
            'units' => 'nullable|integer|min:0',
            'effective_from' => 'required|date',
            'effective_to' => 'nullable|date|after:effective_from',
            'payroll_year' => 'required|integer|min:2020|max:2050',
            'payroll_month' => 'required|integer|between:1,12',
            'frequency' => 'required|in:monthly,weekly,bi_weekly,quarterly,annually,one_time',
            'status' => 'nullable|integer',
        ]);

        $request->merge(['calculation_type' => $calculationType]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            DB::beginTransaction();

            $input = $request->all();
            $input['updated_by'] = Auth::id();

            // Check if approval workflow exists for EmployeeDeductions
            $workflowExists = ApprovalWorkflow::where('model_type', EmployeeDeductions::class)->exists();

            if ($workflowExists) {
                // Workflow exists - reset to pending approval
                $input['status'] = GeneralStatus::INACTIVE;
                $input['approval_status'] = ApprovalStatus::DRAFT;
                $input['date_approved'] = null;
            } else {
                // No workflow - auto-approve
                $input['status'] = GeneralStatus::ACTIVE;
                $input['approval_status'] = ApprovalStatus::APPROVED;
                $input['date_approved'] = now();
                $input['approved_by'] = Auth::id();
            }

            // Set boolean values
            $input['is_recurring'] = $request->has('is_recurring');

            $deduction->update($input);

            DB::commit();

            $successMessage = $workflowExists
                ? 'Employee deduction successfully updated and pending approval.'
                : 'Employee deduction successfully updated and approved.';

            return redirect()->route('employee_deductions.show', $deduction->id)
                ->with('success', $successMessage);
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->with('error', 'An error occurred while updating the employee deduction. Please try again.' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified employee deduction
     */
    public function destroy($id)
    {
        try {
            $deduction = EmployeeDeductions::findOrFail($id);
            $deduction->delete();
            return response()->json(['status' => 'success', 'message' => 'Employee deduction deleted successfully.']);
        } catch (\Exception $e) {
            if ($e->getCode() == 1451) {
                return response()->json(['success' => false, 'message' => 'Cannot delete deduction: It is associated with other records.'], 409); // 409 Conflict
            } else {
                \Log::error('Error deleting employee deduction: ' . $e->getMessage());
                return response()->json(['status' => 'error', 'message' => 'An error occurred while deleting the employee deduction. Please try again.'], 500); // 500 Internal Server Error
            }
        }
    }

    /**
     * Approve an employee deduction
     */
    public function approve(Request $request, $id)
    {
        try {
            $deduction = EmployeeDeductions::findOrFail($id);
            $deduction->approve(Auth::user(), $request->approval_notes);

            return redirect()->back()
                ->with('success', 'Employee deduction approved successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'An error occurred while approving the deduction.');
        }
    }

    /**
     * Reject an employee deduction
     */
    public function reject(Request $request, $id)
    {
        try {
            $deduction = EmployeeDeductions::findOrFail($id);
            $deduction->reject(Auth::id(), $request->approval_notes);

            return redirect()->back()
                ->with('success', 'Employee deduction rejected successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'An error occurred while rejecting the deduction.');
        }
    }

    /**
     * Suspend an employee deduction
     */
    public function suspend(Request $request, $id)
    {
        try {
            $deduction = EmployeeDeductions::findOrFail($id);
            $deduction->suspend(Auth::id(), $request->approval_notes);

            return redirect()->back()
                ->with('success', 'Employee deduction suspended successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'An error occurred while suspending the deduction.');
        }
    }

    /**
     * Get employee deductions for a specific employee (AJAX)
     */
    public function getEmployeeDeductions(Request $request, $employeeId)
    {
        $year = $request->get('year', date('Y'));
        $month = $request->get('month', date('n'));

        $deductions = EmployeeDeductions::getActiveDeductionsForEmployee($employeeId, $year, $month);

        return response()->json([
            'success' => true,
            'data' => $deductions->map(function ($deduction) {
                return [
                    'id' => $deduction->id,
                    'deduction_category' => $deduction->deduction_category,
                    'calculation_type' => $deduction->calculation_type,
                    'amount' => $deduction->amount,
                    'calculated_amount' => $deduction->calculated_amount,
                    'is_tax_deductible' => $deduction->is_tax_deductible,
                    'status' => 0,
                ];
            })
        ]);
    }

    /**
     * Calculate total deductions for an employee (AJAX)
     */
    public function calculateTotalDeductions(Request $request, $employeeId)
    {
        $year = $request->get('year', date('Y'));
        $month = $request->get('month', date('n'));

        $totalDeductions = EmployeeDeductions::calculateTotalDeductionsForEmployee($employeeId, $year, $month);

        return response()->json([
            'success' => true,
            'total_deductions' => $totalDeductions,
            'formatted_total' => number_format($totalDeductions, 2)
        ]);
    }

    /**
     * Generate a unique reference number
     */
    private function generateReferenceNumber()
    {
        return DB::transaction(function () {
            $prefix = 'ED';
            $yearMonth = date('Ym');
            $searchPattern = $prefix . $yearMonth . '%';

            // Include soft-deleted records in the search
            $lastNumber = EmployeeDeductions::withTrashed()
                ->where('reference_number', 'like', $searchPattern)
                ->lockForUpdate()
                ->orderBy('reference_number', 'desc')
                ->value('reference_number');

            if ($lastNumber) {
                $sequence = (int) substr($lastNumber, strlen($prefix . $yearMonth)) + 1;
            } else {
                $sequence = 1;
            }

            $referenceNumber = $prefix . $yearMonth . str_pad($sequence, 4, '0', STR_PAD_LEFT);

            // Final verification (include soft-deleted in check)
            if (EmployeeDeductions::withTrashed()
                ->where('reference_number', $referenceNumber)
                ->exists()
            ) {
                throw new \RuntimeException('Duplicate reference number detected: ' . $referenceNumber);
            }

            return $referenceNumber;
        });
    }

    public function importExcel()
    {
        return redirect()->route('payroll.bulk_upload.deductions.index');
    }

    public function import(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'select_file' => 'required|mimetypes:text/csv,application/csv,application/vnd.ms-excel,text/plain',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        DB::beginTransaction();
        try {
            $import = new \App\Imports\EmployeeDeductionsImport();
            Excel::import($import, $request->file('select_file'));

            if (!empty($import->getErrors())) {
                DB::rollback();
                return redirect()->back()->withErrors($import->getErrors())->withInput();
            }

            DB::commit();
            return redirect()->route('employee_deductions.index')->with('success', 'Employee Deductions imported successfully.');
        } catch (\Exception $e) {
            DB::rollback();
            \Log::error('Error importing employee deductions: ' . $e->getMessage());
            return redirect()->back()->with('error', 'An error occurred during import: ' . $e->getMessage());
        }
    }

    /**
     * Calculate daily rate for an employee based on working days
     */
    public function calculateDailyRate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'employee_id' => 'required|exists:employee,employee_id',
            'payroll_year' => 'required|integer|min:2020|max:2050',
            'payroll_month' => 'required|integer|min:1|max:12',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed'
            ], 422);
        }

        try {
            $employee = Employee::findOrFail($request->employee_id);
            $employeePayroll = $employee->employeePayroll;

            if (!$employeePayroll) {
                return response()->json([
                    'success' => false,
                    'message' => 'Employee payroll record not found'
                ]);
            }

            $basicSalary = $employeePayroll->basic_salary;

            // Create a payroll period for calculation
            $periodStart = Carbon::create($request->payroll_year, $request->payroll_month, 1);
            $periodEnd = $periodStart->copy()->endOfMonth();

            // Use the same method as in KenyanPayrollCalculationService to get working days
            $workingDays = $this->getWorkingDaysInMonth($periodStart, $periodEnd);

            if ($workingDays <= 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'No working days found for the selected period'
                ]);
            }

            $dailyRate = $basicSalary / $workingDays;

            return response()->json([
                'success' => true,
                'basic_salary' => $basicSalary,
                'working_days' => $workingDays,
                'daily_rate' => $dailyRate,
                'calculation_note' => "Basic salary: {$basicSalary} / {$workingDays} working days"
            ]);
        } catch (\Exception $e) {
            \Log::error('Error calculating daily rate: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error calculating daily rate: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get working days in a month (replicate the logic from KenyanPayrollCalculationService)
     */
    private function getWorkingDaysInMonth($startDate, $endDate)
    {
        try {
            // Use the same logic as in KenyanPayrollCalculationService
            $attendanceRepository = app(\App\Repositories\AttendanceRepository::class);
            $workingDays = $attendanceRepository->new_number_of_working_days_date($startDate, $endDate);

            return is_array($workingDays) ? count($workingDays) : 22; // Fallback to 22 if not array
        } catch (\Exception $e) {
            // Fallback calculation if the repository method fails
            $days = 0;
            $current = $startDate->copy();

            while ($current <= $endDate) {
                // Count weekdays (Monday to Friday)
                if (!$current->isWeekend()) {
                    $days++;
                }
                $current->addDay();
            }

            return max($days, 1); // Ensure at least 1 day
        }
    }
}