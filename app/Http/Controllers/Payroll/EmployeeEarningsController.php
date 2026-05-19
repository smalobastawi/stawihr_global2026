<?php

namespace App\Http\Controllers\Payroll;

use App\Http\Controllers\Controller;
use App\Lib\Enumerations\ApprovalStatus;
use App\Models\EmployeeEarnings;
use App\Models\Employee;
use App\Models\PayrollEarningTypes;
use App\Models\FinancialYear;
use App\Models\ApprovalWorkflow;
use App\Lib\Enumerations\EarningCategories;
use App\Lib\Enumerations\CalculationTypes;
use App\Lib\Enumerations\EarningFrequencies;
use App\Lib\Enumerations\GeneralStatus;
use App\Models\Payroll\EmployeePayroll;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use App\Models\Payroll\PayrollPeriod;
use Illuminate\Support\Facades\Log;

class EmployeeEarningsController extends Controller
{
    /**
     * Display a listing of employee earnings
     */
    public function index(Request $request)
    {
        $query = EmployeeEarnings::with(['employee', 'payrollEarningType', 'createdBy', 'approvedBy']);

        // Filter by employee if provided
        if ($request->has('employee_id') && $request->employee_id) {
            $query->where('employee_id', $request->employee_id);
        }



        // Filter by status
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        // Filter by payroll periods
        if ($request->has('payroll_periods') && !empty($request->payroll_periods)) {
            $payrollPeriods = $request->payroll_periods;

            // Get the selected periods to extract years and months
            $selectedPeriods = PayrollPeriod::whereIn('id', $payrollPeriods)->get();

            $query->where(function ($q) use ($selectedPeriods) {
                foreach ($selectedPeriods as $period) {
                    $year = $period->start_date->year;
                    $month = $period->start_date->month;

                    $q->orWhere(function ($subQ) use ($year, $month) {
                        $subQ->where('payroll_year', $year)
                            ->where('payroll_month', $month);
                    });
                }
            });
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

        $earningCategories = EarningCategories::toArray();

        $statuses = [
            'active' => 'Active',
            'inactive' => 'Inactive',
            'suspended' => 'Suspended',
            'expired' => 'Expired'
        ];

        // Get payroll periods for filter dropdown (ordered by latest first)
        $payrollPeriods = PayrollPeriod::orderBy('start_date', 'desc')->take(24)->get();

        return view('admin.payroll.employee_earnings.index', compact(
            'results',
            'employees',
            'earningCategories',
            'statuses',
            'payrollPeriods'
        ));
    }

    /**
     * Show the form for creating a new employee earning
     */
    public function create(Request $request)
    {
        $employees = Employee::whereHas('employeePayroll')->select('employee_id', 'payroll_number', 'first_name', 'middle_name', 'last_name', 'staff_no')
            ->where('status', GeneralStatus::ACTIVE)
            ->orderBy('first_name')
            ->get();

        $payrollEarningTypes = PayrollEarningTypes::where('status', GeneralStatus::ACTIVE)
            ->orderBy('name')
            ->get();

        $earningCategories = EarningCategories::toArray();
        $frequencies = EarningFrequencies::toArray();
        $payrollPeriods = PayrollPeriod::where('status', '!=', 'closed')->get();


        // Get financial years for dropdown
        $financialYears = FinancialYear::orderBy('start_date', 'desc')->get();
        $activeFinancialYear = FinancialYear::active()->first();

        // Pre-select employee if provided
        $selectedEmployee = null;
        if ($request->has('employee_id')) {
            $selectedEmployee = Employee::find($request->employee_id);
        }

        return view('admin.payroll.employee_earnings.form', compact(
            'employees',
            'payrollEarningTypes',
            'earningCategories',
            'frequencies',
            'selectedEmployee',
            'financialYears',
            'activeFinancialYear',
            'payrollPeriods',
        ));
    }

    /**
     * Store a newly created employee earning
     */
    public function store(Request $request)
    {
        $earningType = PayrollEarningTypes::findOrFail($request->payroll_earning_type_id);

        $validator = Validator::make($request->all(), [
            'employee_id' => 'required|exists:employee,employee_id',
            'payroll_earning_type_id' => 'required|exists:payroll_earning_types,id',
            'earning_category' => 'required|' . EarningCategories::getValidationRule(),
            'amount' => 'nullable|numeric|min:0',
            'percentage' => 'nullable|numeric|min:0|max:100',
            'rate' => 'nullable|numeric|min:0',
            'units' => 'nullable|integer|min:0',
            'effective_from' => 'required|date',
            'effective_to' => 'nullable|date|after:effective_from',
            'financial_year_id' => 'required|exists:financial_years,id',
            'payroll_month' => 'required|integer|min:1|max:12',
            'frequency' => 'required|' . EarningFrequencies::getValidationRule(),
            'description' => 'nullable|string|max:1000',
            'status' => 'required|integer',
            'is_recurring' => 'boolean',

        ]);

        $validator->sometimes('amount', 'required', function ($input) use ($earningType) {
            return $earningType->calculation_type == 'fixed_amount';
        });

        $validator->sometimes('percentage', 'required', function ($input) use ($earningType) {
            return in_array($earningType->calculation_type, ['percentage_of_basic', 'percentage_of_gross']);
        });

        $validator->sometimes('rate', 'required', function ($input) use ($earningType) {
            return $earningType->calculation_type == 'daily_rate';
        });

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }


        //check if the employee has a salary on their payrol profile and return an error if they earnng being added has salary int he name. 
        $employeePayroll = EmployeePayroll::where('employee_id', $request->employee_id)->first();
        if (!$employeePayroll) {
            return redirect()->back()
                ->with('error', 'The selected employee does not have a payroll profile. Please create a payroll profile for the employee before adding earnings.')
                ->withInput();
        }
        $salaryTypeEarning = PayrollEarningTypes::where('name', 'like', '%salary%')->get();
        //check if the earning type_id being added is of salary type
        $isSalaryEarningType = $salaryTypeEarning->contains('id', $request->payroll_earning_type_id);

        if ($employeePayroll->basic_salary > 0 && $isSalaryEarningType) {
            return redirect()->back()
                ->with('error', 'Earning type cannot be of "salary" type as the employee already has a salary assigned in their payroll profile.')
                ->withInput();
        }

        if (strpos(strtolower($request->earning_category), 'salary') !== false && $employeePayroll->basic_salary > 0) {
            return redirect()->back()
                ->with('error', 'Earning category cannot be "salary" type as the employee already has a salary assigned in their payroll profile.')
                ->withInput();
        }

        // Check if effective_from date is within an open payroll period

        try {
            DB::beginTransaction();

            $input = $request->all();
            $input['created_by'] = Auth::id();

            // Check if approval workflow exists for EmployeeEarnings
            $workflowExists = ApprovalWorkflow::where('model_type', EmployeeEarnings::class)->exists();

            if ($workflowExists) {
                // Workflow exists - set to pending approval
                $input['status'] = GeneralStatus::INACTIVE;
                $input['approval_status'] = ApprovalStatus::DRAFT;
                $input['date_approved'] = null;
                $input['is_active'] = 'inactive';
            } else {
                // No workflow - auto-approve
                $input['status'] = GeneralStatus::ACTIVE;
                $input['approval_status'] = ApprovalStatus::APPROVED;
                $input['date_approved'] = now();
                $input['approved_by'] = Auth::id();
                $input['is_active'] = 'active';
            }

            // Get earning type details
            $input['calculation_type'] = $earningType->calculation_type;
            $input['is_taxable'] = $earningType->taxable;
            $input['is_pensionable'] = $earningType->is_pensionable;
            $input['is_recurring'] = $request->has('is_recurring');

            EmployeeEarnings::create($input);

            DB::commit();

            $successMessage = $workflowExists
                ? 'Employee earning successfully created and pending approval.'
                : 'Employee earning successfully created and approved.';

            return redirect()->route('employee_earnings.index')
                ->with('success', $successMessage);
        } catch (\Exception $e) {
            DB::rollback();
            Log::info('Error creating employee earning: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'An error occurred while creating the employee earning. Please try again.' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified employee earning
     */
    public function show($id)
    {
        $earning = EmployeeEarnings::with([
            'employee.department',
            'employee.designation',
            'employee.workLocation',
            'payrollEarningType',
            'createdBy',
            'updatedBy',
            'approvedBy'
        ])->findOrFail($id);

        return view('admin.payroll.employee_earnings.show', compact('earning'));
    }

    /**
     * Show the form for editing the specified employee earning
     */
    public function edit($id)
    {
        $editModeData = EmployeeEarnings::findOrFail($id);

        $employees = Employee::select('employee_id', 'payroll_number', 'first_name', 'last_name', 'staff_no')
            ->where('status', 1)
            ->orderBy('first_name')
            ->get();

        $payrollEarningTypes = PayrollEarningTypes::where('status', 1)
            ->orderBy('name')
            ->get();

        $earningCategories = EarningCategories::toArray();
        $frequencies = EarningFrequencies::toArray();

        $statuses = [
            'active' => 'Active',
            'inactive' => 'Inactive',
            'suspended' => 'Suspended',
            'expired' => 'Expired'
        ];

        // Get financial years for dropdown
        $financialYears = FinancialYear::orderBy('start_date', 'desc')->get();
        $activeFinancialYear = FinancialYear::active()->first();
        $payrollPeriods = PayrollPeriod::where('status', '!=', 'closed')->get();
        return view('admin.payroll.employee_earnings.form', compact(
            'editModeData',
            'employees',
            'payrollEarningTypes',
            'earningCategories',
            'frequencies',
            'statuses',
            'financialYears',
            'activeFinancialYear',
            'payrollPeriods',
        ));
    }

    /**
     * Update the specified employee earning
     */
    public function update(Request $request, $id)
    {
        $earning = EmployeeEarnings::findOrFail($id);
        $earningType = PayrollEarningTypes::findOrFail($request->payroll_earning_type_id);

        $validator = Validator::make($request->all(), [
            'employee_id' => 'required|exists:employee,employee_id',
            'payroll_earning_type_id' => 'required|exists:payroll_earning_types,id',
            'earning_category' => 'required|' . EarningCategories::getValidationRule(),
            'amount' => 'nullable|numeric|min:0',
            'percentage' => 'nullable|numeric|min:0|max:100',
            'rate' => 'nullable|numeric|min:0',
            'units' => 'nullable|integer|min:0',
            'effective_from' => 'required|date',
            'effective_to' => 'nullable|date|after:effective_from',
            'financial_year_id' => 'required|exists:financial_years,id',
            'payroll_month' => 'required|integer|min:1|max:12',
            'frequency' => 'required|' . EarningFrequencies::getValidationRule(),
            'status' => 'required|in:active,inactive,suspended,expired',
            'description' => 'nullable|string|max:1000',
            'status' => 'required|integer',
            'is_recurring' => 'boolean',

        ]);

        $validator->sometimes('amount', 'required', function ($input) use ($earningType) {
            return $earningType->calculation_type == 'fixed_amount';
        });

        $validator->sometimes('percentage', 'required', function ($input) use ($earningType) {
            return in_array($earningType->calculation_type, ['percentage_of_basic', 'percentage_of_gross']);
        });

        $validator->sometimes('rate', 'required', function ($input) use ($earningType) {
            return $earningType->calculation_type == 'daily_rate';
        });

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            DB::beginTransaction();

            $input = $request->all();
            $input['updated_by'] = Auth::id();

            // Check if approval workflow exists for EmployeeEarnings
            $workflowExists = ApprovalWorkflow::where('model_type', EmployeeEarnings::class)->exists();

            if ($workflowExists) {
                // Workflow exists - reset to pending approval
                $input['status'] = GeneralStatus::INACTIVE;
                $input['approval_status'] = ApprovalStatus::DRAFT;
                $input['date_approved'] = null;
                $input['is_active'] = 'inactive';
            } else {
                // No workflow - auto-approve
                $input['status'] = GeneralStatus::ACTIVE;
                $input['approval_status'] = ApprovalStatus::APPROVED;
                $input['date_approved'] = now();
                $input['approved_by'] = Auth::id();
                $input['is_active'] = 'active';
            }

            // Get earning type details
            $input['calculation_type'] = $earningType->calculation_type;
            $input['is_taxable'] = $earningType->taxable;
            $input['is_pensionable'] = $earningType->is_pensionable;
            $input['is_recurring'] = $request->has('is_recurring');

            $earning->update($input);

            DB::commit();

            $successMessage = $workflowExists
                ? 'Employee earning successfully updated and pending approval.'
                : 'Employee earning successfully updated and approved.';

            return redirect()->route('employee_earnings.index')
                ->with('success', $successMessage);
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->with('error', 'An error occurred while updating the employee earning. Please try again.')
                ->withInput();
        }
    }

    /**
     * Remove the specified employee earning
     */
    public function destroy($id)
    {
        try {
            $earning = EmployeeEarnings::findOrFail($id);
            $earning->delete();
            echo "success";
        } catch (\Exception $e) {
            if ($e->getCode() == 1451) {
                echo 'hasForeignKey';
            } else {
                echo 'error';
            }
        }
    }

    /**
     * Approve an employee earning
     */
    public function approve(Request $request, $id)
    {
        try {
            $earning = EmployeeEarnings::findOrFail($id);
            $earning->approve(Auth::user(), $request->approval_notes);

            return redirect()->back()
                ->with('success', 'Employee earning approved successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'An error occurred while approving the earning.');
        }
    }

    /**
     * Reject an employee earning
     */
    public function reject(Request $request, $id)
    {
        try {
            $earning = EmployeeEarnings::findOrFail($id);
            $earning->reject(Auth::id(), $request->approval_notes);

            return redirect()->back()
                ->with('success', 'Employee earning rejected successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'An error occurred while rejecting the earning.');
        }
    }

    /**
     * Suspend an employee earning
     */
    public function suspend(Request $request, $id)
    {
        try {
            $earning = EmployeeEarnings::findOrFail($id);
            $earning->suspend(Auth::id(), $request->approval_notes);

            return redirect()->back()
                ->with('success', 'Employee earning suspended successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'An error occurred while suspending the earning.');
        }
    }

    /**
     * Get employee earnings for a specific employee (AJAX)
     */
    public function getEmployeeEarnings(Request $request, $employeeId)
    {
        $year = $request->get('year', date('Y'));
        $month = $request->get('month', date('n'));

        $earnings = EmployeeEarnings::getActiveEarningsForEmployee($employeeId, $year, $month);

        return response()->json([
            'success' => true,
            'data' => $earnings->map(function ($earning) {
                return [
                    'id' => $earning->id,

                    'earning_category' => $earning->earning_category,
                    'calculation_type' => $earning->calculation_type,
                    'amount' => $earning->amount,
                    'calculated_amount' => $earning->calculated_amount,
                    'is_taxable' => $earning->is_taxable,
                    'is_pensionable' => $earning->is_pensionable,
                    'status' => $earning->status,
                ];
            })
        ]);
    }

    /**
     * Calculate total earnings for an employee (AJAX)
     */
    public function calculateTotalEarnings(Request $request, $employeeId)
    {
        $year = $request->get('year', date('Y'));
        $month = $request->get('month', date('n'));

        $totalEarnings = EmployeeEarnings::calculateTotalEarningsForEmployee($employeeId, $year, $month);

        return response()->json([
            'success' => true,
            'total_earnings' => $totalEarnings,
            'formatted_total' => number_format($totalEarnings, 2)
        ]);
    }
}