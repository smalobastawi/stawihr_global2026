<?php

namespace App\Http\Controllers\Payroll;

use App\Http\Controllers\Controller;
use App\Lib\Enumerations\ApprovalStatus;
use App\Lib\Enumerations\GeneralStatus;
use App\Models\ApprovalWorkflow;
use App\Models\Department;
use App\Models\Payroll\EmployeePayroll;
use App\Models\Payroll\AllowanceType;
use App\Models\Payroll\DeductionType;
use App\Models\Payroll\Bank;
use App\Models\Payroll\BankBranch;
use App\Models\Payroll\PensionScheme;
use App\Models\Employee;
use App\Services\Payroll\PayrollChangeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Validator;

class EmployeePayrollController extends Controller
{
    protected $payrollChangeService;

    public function __construct(PayrollChangeService $payrollChangeService)
    {
        $this->payrollChangeService = $payrollChangeService;
    }
    /**
     * Display a listing of employee payroll records
     */
    public function index(Request $request)
    {
        $query = EmployeePayroll::with(['employee.department', 'pensionScheme', 'allowances', 'employeeDeductions', 'earnings']);

        // Apply filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('employee', function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            })->orWhere('payroll_number', 'like', "%{$search}%");
        }

        if ($request->filled('department_id')) {
            $query->whereHas('employee', function ($q) use ($request) {
                $q->where('department_id', $request->department_id);
            });
        }

        if ($request->filled('is_active')) {
            $query->where('is_active', $request->is_active);
        }

        $employeePayrolls = $query->orderBy('created_at', 'desc')->get();


        // Get departments for filter
        $departments = Department::orderBy('department_name')->get();


        return view('admin.payroll.employees.index', compact('employeePayrolls', 'departments'));
    }

    /**
     * Show the form for creating a new employee payroll record
     */
    public function create()
    {
        // Get employees who don't have active payroll records
        $employees = Employee::where('status', GeneralStatus::ACTIVE)->whereDoesntHave('employeePayroll')->orderBy('first_name')->get();

        $pensionSchemes = PensionScheme::active()->orderBy('name')->get();
        $allowanceTypes = AllowanceType::active()->orderBy('name')->get();
        $deductionTypes = DeductionType::active()->orderBy('name')->get();

        $banks = Bank::where('status', GeneralStatus::ACTIVE)->orderBy('name')->get();

        return view('admin.payroll.employees.create', compact(
            'employees',
            'pensionSchemes',
            'allowanceTypes',
            'deductionTypes',
            'banks'
        ));
    }

    /**
     * Store a newly created employee payroll record
     */
    public function store(Request $request)
    {
        // Custom validation for employer pension rates
        $validator = Validator::make($request->all(), [
            'employee_id' => 'required|exists:employee,employee_id|unique:employee_payrolls,employee_id,NULL,id,is_active,1',
            'phone_number' => 'nullable|string|max:20|regex:/^[\+0-9\-\(\)\s]*$/',
            'basic_salary' => 'required|numeric|min:0',
            'income_frequency' => 'required|in:daily,weekly,monthly',
            'payment_method' => 'required|in:bank_transfer,mobile_money,cash,cheque',
            'bank_name' => 'nullable|string|max:255',
            'bank_branch' => 'nullable|string|max:255',
            'account_number' => 'nullable|string|max:255',
            'account_name' => 'nullable|string|max:255',
            'kra_pin' => 'nullable|string|max:20',
            'nssf_number' => 'nullable|string|max:20',
            'shif_number' => 'nullable|string|max:20',
            'tax_status' => 'required|in:resident,non_resident,exempt',
            'disability_exemption' => 'boolean',
            'pension_scheme_ids' => 'nullable|array',
            'pension_scheme_ids.*' => 'exists:pension_schemes,id',
            'pension_rates' => 'nullable|array',
            'pension_rates.*.employee_rate' => 'required_with:pension_scheme_ids|numeric|min:0',
            'pension_rates.*.employer_rate' => 'nullable|numeric|min:0',
            'effective_date' => 'required|date',
            'overtime_rate_normal' => 'nullable|numeric|min:0|max:5',
            'overtime_rate_weekend' => 'nullable|numeric|min:0|max:5',
            'overtime_rate_holiday' => 'nullable|numeric|min:0|max:5',
            'allowances' => 'nullable|array',
            'allowances.*.type_id' => 'required_with:allowances|exists:allowance_types,id',
            'allowances.*.calculation_type' => 'required_with:allowances|in:fixed,percentage',
            'allowances.*.amount' => 'required_if:allowances.*.calculation_type,fixed|nullable|numeric|min:0',
            'allowances.*.percentage' => 'required_if:allowances.*.calculation_type,percentage|nullable|numeric|min:0|max:100',
            'deductions' => 'nullable|array',
            'deductions.*.type_id' => 'required_with:deductions|exists:deduction_types,id',
            'deductions.*.calculation_type' => 'required_with:deductions|in:fixed,percentage',
            'deductions.*.amount' => 'required_if:deductions.*.calculation_type,fixed|nullable|numeric|min:0',
            'deductions.*.percentage' => 'required_if:deductions.*.calculation_type,percentage|nullable|numeric|min:0|max:100',
            'nssf_rate_type' => 'required',
        ]);

        // Add custom validation for total employer rate
        $validator->after(function ($validator) use ($request) {
            if ($request->filled('pension_rates')) {
                $totalEmployerRate = 0;

                foreach ($request->pension_rates as $schemeId => $rates) {
                    $employerRate = $rates['employer_rate'] ?? 0;
                    $totalEmployerRate += (float) $employerRate;
                }

                if ($totalEmployerRate > 6) {
                    $validator->errors()->add(
                        'pension_rates',
                        "Total employer pension rate cannot exceed 6%. Current total: {$totalEmployerRate}%"
                    );
                }
            }
        });

        // Check if validation fails
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Update Employee Details in the main table here
        $employeeDetails = Employee::where('employee_id', $request->employee_id)->first();
        $employeeDetails->nssf_rate_type = $request->nssf_rate_type;
        $employeeDetails->KRA_Pin = $request->kra_pin;
        $employeeDetails->shif_number = $request->shif_number;
        $employeeDetails->NSSF_no = $request->nssf_number;
        $employeeDetails->bank = $request->bank_name;
        $employeeDetails->bank_branch = $request->bank_branch;
        $employeeDetails->bank_account_number = $request->account_number;
        $employeeDetails->bank_account_name = $request->account_name;
        $employeeDetails->save();

        // Check if the employee has a payroll number before generating a new one
        $payrollNumber = Employee::where('employee_id', $request->employee_id)->value('payroll_number');
        if (!$payrollNumber) {
            $payrollNumber = EmployeePayroll::generatePayrollNumber();
            // Update the employee table with the new payroll number
            $employeeDetails->payroll_number = $payrollNumber;
            $employeeDetails->save();
        }

        try {
            DB::beginTransaction();

            // Check if approval workflow exists for EmployeePayroll model
            $workflowExists = ApprovalWorkflow::where('model_type', EmployeePayroll::class)->exists();

            // Determine initial status based on workflow existence
            if ($workflowExists) {
                $initialStatus = GeneralStatus::INACTIVE;
                $initialApprovalStatus = ApprovalStatus::DRAFT;
                $initialIsActive = false;
                $dateApproved = null;
            } else {
                // No workflow - save as fully approved and active
                $initialStatus = GeneralStatus::ACTIVE;
                $initialApprovalStatus = ApprovalStatus::APPROVED;
                $initialIsActive = true;
                $dateApproved = now();
            }

            // Create employee payroll record
            $employeePayroll = EmployeePayroll::create([
                'employee_id' => $request->employee_id,
                'payroll_number' => $payrollNumber,
                'phone_number' => $request->phone_number,
                'basic_salary' => $request->basic_salary,
                'income_frequency' => $request->income_frequency ?? 'monthly',
                'payment_method' => $request->payment_method,
                'bank_name' => $request->bank_name,
                'bank_branch' => $request->bank_branch,
                'account_number' => $request->account_number,
                'account_name' => $request->account_name,
                'kra_pin' => $request->kra_pin,
                'nssf_number' => $request->nssf_number,
                'shif_number' => $request->shif_number,
                'tax_status' => $request->tax_status,
                'disability_exemption' => $request->boolean('disability_exemption'),
                'overtime_rate_normal' => $request->overtime_rate_normal ?? 1.5,
                'overtime_rate_weekend' => $request->overtime_rate_weekend ?? 2.0,
                'overtime_rate_holiday' => $request->overtime_rate_holiday ?? 2.0,
                'effective_date' => $request->effective_date,
                'is_active' => $initialIsActive,
                'created_by' => auth()->id(),
                'status' => $initialStatus,
                'approval_status' => $initialApprovalStatus,
                'date_approved' => $dateApproved,
            ]);

            // Attach pension schemes with custom rates
            if ($request->filled('pension_scheme_ids')) {
                $pensionSchemes = [];
                $totalEmployerRate = 0;

                foreach ($request->pension_scheme_ids as $schemeId) {
                    $scheme = \App\Models\Payroll\PensionScheme::find($schemeId);
                    if ($scheme) {
                        // Use custom rates from form, fallback to max rates if not provided
                        $employeeRate = $request->input("pension_rates.{$schemeId}.employee_rate", $scheme->max_employee_rate ?? 0);
                        $employerRate = $request->input("pension_rates.{$schemeId}.employer_rate", $scheme->max_employer_rate ?? 0);

                        // Apply capping logic: employer rate matches employee rate up to 6%, then caps at 6% or scheme max
                        $cappedEmployerRate = min($employeeRate, 6);
                        $cappedEmployerRate = min($cappedEmployerRate, $scheme->max_employer_rate ?? 6);

                        // Ensure total employer rate doesn't exceed 6%
                        if (($totalEmployerRate + $cappedEmployerRate) > 6) {
                            $cappedEmployerRate = max(0, 6 - $totalEmployerRate);
                        }

                        $totalEmployerRate += $cappedEmployerRate;

                        $pensionSchemes[$schemeId] = [
                            'employee_rate' => $employeeRate,
                            'employer_rate' => $cappedEmployerRate,
                        ];
                    }
                }
                $employeePayroll->pensionSchemes()->attach($pensionSchemes);
            }

            // Create allowances
            if ($request->filled('allowances')) {
                foreach ($request->allowances as $allowanceData) {
                    if (!empty($allowanceData['type_id'])) {
                        $allowanceType = AllowanceType::find($allowanceData['type_id']);

                        $employeePayroll->allowances()->create([
                            'allowance_type_id' => $allowanceData['type_id'],
                            'name' => $allowanceType->name,
                            'calculation_type' => $allowanceData['calculation_type'],
                            'amount' => $allowanceData['calculation_type'] === 'fixed' ? $allowanceData['amount'] : 0,
                            'percentage' => $allowanceData['calculation_type'] === 'percentage' ? $allowanceData['percentage'] : 0,
                            'is_taxable' => $allowanceType->is_taxable,
                            'is_pensionable' => $allowanceType->is_pensionable,
                            'is_active' => true,
                            'effective_date' => $request->effective_date,
                            'created_by' => auth()->id()
                        ]);
                    }
                }
            }

            // Create deductions
            if ($request->filled('deductions')) {
                foreach ($request->deductions as $deductionData) {
                    if (!empty($deductionData['type_id'])) {
                        $deductionType = DeductionType::find($deductionData['type_id']);

                        $employeePayroll->deductions()->create([
                            'deduction_type_id' => $deductionData['type_id'],
                            'name' => $deductionType->name,
                            'calculation_type' => $deductionData['calculation_type'],
                            'amount' => $deductionData['calculation_type'] === 'fixed' ? $deductionData['amount'] : 0,
                            'percentage' => $deductionData['calculation_type'] === 'percentage' ? $deductionData['percentage'] : 0,
                            'is_statutory' => $deductionType->is_statutory,
                            'is_active' => true,
                            'effective_date' => $request->effective_date,
                            'created_by' => auth()->id()
                        ]);
                    }
                }
            }

            DB::commit();

            return redirect()->route('payroll.employees.index')
                ->with('success', 'Employee payroll record created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error creating employee payroll record: ' . $e->getMessage());
        }
    }
    /**
     * Display the specified employee payroll record
     */
    public function show(EmployeePayroll $employeePayroll)
    {
        $employeePayroll->load([
            'employee.department',
            'employee.designation',
            'pensionSchemes',
            'earnings.payrollEarningType', // Changed from allowanceType to match  schema
            'deductions',
            'payrollRecords' => function ($query) {
                $query->orderBy('created_at', 'desc')->limit(10);
            }
        ]);

        return view('admin.payroll.employees.show', compact('employeePayroll'));
    }

    /**
     * Show the form for editing the specified employee payroll record
     */
    public function edit(EmployeePayroll $employeePayroll)
    {
        $employeePayroll->load(['employee', 'allowances', 'deductions', 'pensionSchemes']);

        $pensionSchemes = PensionScheme::active()->orderBy('name')->get();
        $allowanceTypes = AllowanceType::active()->orderBy('name')->get();
        $deductionTypes = DeductionType::active()->orderBy('name')->get();
        $banks = Bank::where('status', GeneralStatus::ACTIVE)->orderBy('name')->get();

        return view('admin.payroll.employees.edit', compact(
            'employeePayroll',
            'pensionSchemes',
            'allowanceTypes',
            'deductionTypes',
            'banks'
        ));
    }

    /**
     * Update the specified employee payroll record
     */
    public function update(Request $request, EmployeePayroll $employeePayroll)
    {

        $validator = Validator::make($request->all(), [
            'phone_number' => 'nullable|string|max:20|regex:/^[\+0-9\-\(\)\s]*$/',
            'basic_salary' => 'required|numeric|min:0',
            'income_frequency' => 'required|in:daily,weekly,monthly',
            'payment_method' => 'required|in:bank_transfer,mobile_money,cash,cheque',
            'bank_name' => 'nullable|string|max:255',
            'bank_branch' => 'nullable|string|max:255',
            'account_number' => 'nullable|string|max:255',
            'account_name' => 'nullable|string|max:255',
            'kra_pin' => 'nullable|string|max:20',
            'nssf_number' => 'nullable|string|max:20',
            'shif_number' => 'nullable|string|max:20',
            'tax_status' => 'required|in:resident,non_resident,exempt',
            'disability_exemption' => 'boolean',
            'pension_scheme_ids' => 'nullable|array',
            'pension_scheme_ids.*' => 'exists:pension_schemes,id',
            'pension_rates' => 'nullable|array',
            'pension_rates.*.employee_rate' => 'required_with:pension_scheme_ids|numeric|min:0',
            'pension_rates.*.employer_rate' => 'nullable|numeric|min:0',
            'nssf_rate_type' => 'required|in:2,3,4',
            'overtime_rate_normal' => 'nullable|numeric|min:0|max:5',
            'overtime_rate_weekend' => 'nullable|numeric|min:0|max:5',
            'overtime_rate_holiday' => 'nullable|numeric|min:0|max:5',
            'is_active' => 'boolean',
            'salary_change_reason' => 'nullable:basic_salary_changed,true|string|max:1000', // NEW FIELD
            'salary_effective_date' => 'nullable:basic_salary_changed,true|date', // NEW FIELD
            'salary_change_type' => 'nullable:basic_salary_changed,true|in:promotion,annual_increment,adjustment,market_correction,other', // NEW FIELD
        ]);

        // Add custom validation for total employer rate
        $validator->after(function ($validator) use ($request) {
            if ($request->filled('pension_rates')) {
                $totalEmployerRate = 0;

                foreach ($request->pension_rates as $schemeId => $rates) {
                    $employerRate = $rates['employer_rate'] ?? 0;
                    $totalEmployerRate += (float) $employerRate;
                }

                if ($totalEmployerRate > 6) {
                    $validator->errors()->add(
                        'pension_rates',
                        "Total employer pension rate cannot exceed 6%. Current total: {$totalEmployerRate}%"
                    );
                }
            }
        });

        // NEW: Validate salary change if basic salary is being modified

        $oldSalary = $employeePayroll->basic_salary;
        $newSalary = $request->basic_salary;

        if ($oldSalary != $newSalary) {
            if (!$request->filled('salary_change_reason')) {
                $validator->errors()->add(
                    'salary_change_reason',
                    "Salary change reason is required when modifying basic salary"
                );
            }

            if (!$request->filled('salary_effective_date')) {
                $validator->errors()->add(
                    'salary_effective_date',
                    "Effective date is required when modifying basic salary"
                );
            }
        }


        // Check if validation fails
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $employeeDetails = Employee::where('employee_id', $request->employee_id)->first();
        $employeeDetails->nssf_rate_type = $request->nssf_rate_type;
        $employeeDetails->KRA_Pin = $request->kra_pin;
        $employeeDetails->shif_number = $request->shif_number;
        $employeeDetails->NSSF_no = $request->nssf_number;
        $employeeDetails->bank = $request->bank_name;
        $employeeDetails->bank_branch = $request->bank_branch;
        $employeeDetails->bank_account_number = $request->account_number;
        $employeeDetails->bank_account_name = $request->account_name;
        $employeeDetails->save();

        try {
            DB::beginTransaction();

            // Check if basic salary is being changed
            $oldSalary = $employeePayroll->basic_salary;
            $newSalary = $request->basic_salary;
            $salaryChanged = ($oldSalary != $newSalary);

            if ($salaryChanged) {
                // Use PayrollChangeService to handle salary change with history
                $this->payrollChangeService->updateBasicSalary(
                    $employeePayroll->employee_id,
                    $newSalary,
                    $request->salary_effective_date,
                    $request->salary_change_type,
                    $request->salary_change_reason,
                    auth()->id(),
                    [
                        'ip_address' => $request->ip(),
                        'user_agent' => $request->userAgent(),
                        'updated_fields' => $this->getUpdatedFields($request, $employeePayroll)
                    ]
                );
            } else {
                // Regular update without salary change
                $employeePayroll->update([
                    'phone_number' => $request->phone_number,
                    'basic_salary' => $newSalary,
                    'income_frequency' => $request->income_frequency ?? $employeePayroll->income_frequency,
                    'payment_method' => $request->payment_method,
                    'bank_name' => $request->bank_name,
                    'bank_branch' => $request->bank_branch,
                    'account_number' => $request->account_number,
                    'account_name' => $request->account_name,
                    'kra_pin' => $request->kra_pin,
                    'nssf_number' => $request->nssf_number,
                    'shif_number' => $request->shif_number,
                    'tax_status' => $request->tax_status,
                    'disability_exemption' => $request->boolean('disability_exemption'),
                    'overtime_rate_normal' => $request->overtime_rate_normal ?? $employeePayroll->overtime_rate_normal,
                    'overtime_rate_weekend' => $request->overtime_rate_weekend ?? $employeePayroll->overtime_rate_weekend,
                    'overtime_rate_holiday' => $request->overtime_rate_holiday ?? $employeePayroll->overtime_rate_holiday,

                ]);
            }

            // Sync pension schemes with custom rates (existing code)
            if ($request->filled('pension_scheme_ids')) {
                $pensionSchemes = [];
                $totalEmployerRate = 0;

                foreach ($request->pension_scheme_ids as $schemeId) {
                    $scheme = PensionScheme::find($schemeId);

                    if ($scheme) {
                        $employeeRate = $request->input("pension_rates.{$schemeId}.employee_rate", $scheme->max_employee_rate ?? 0);
                        $employerRate = $request->input("pension_rates.{$schemeId}.employer_rate", $scheme->max_employer_rate ?? 0);
                        $totalEmployerRate += $employerRate;

                        $pensionSchemes[$schemeId] = [
                            'employee_rate' => $employeeRate,
                            'employer_rate' => $employerRate,
                        ];
                    }
                }

                if ($totalEmployerRate > 6) {
                    throw new \Exception("Total employer pension rate cannot exceed 6%. Current total: {$totalEmployerRate}%");
                }

                $employeePayroll->pensionSchemes()->sync($pensionSchemes);
            } else {
                $employeePayroll->pensionSchemes()->detach();
            }
            $employeePayroll->update([
                'is_active' => false,
                'updated_by' => auth()->id(),
                'status' => GeneralStatus::INACTIVE,
                'approval_status' => ApprovalStatus::DRAFT,
                'date_approved' => null,
            ]);
            DB::commit();

            $message = 'Employee payroll record updated successfully.';
            if ($salaryChanged) {
                $message .= ' Salary change has been recorded in history.';
            }

            return redirect()->route('payroll.employees.show', $employeePayroll)
                ->with('success', $message);
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error updating employee payroll record: ' . $e->getMessage());
        }
    }

    /**
     * Get updated fields for audit purposes
     */
    private function getUpdatedFields(Request $request, EmployeePayroll $employeePayroll)
    {
        $updatedFields = [];

        $fieldsToCheck = [
            'phone_number',
            'income_frequency',
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
            'overtime_rate_normal',
            'overtime_rate_weekend',
            'overtime_rate_holiday'
        ];

        foreach ($fieldsToCheck as $field) {
            if ($request->has($field) && $request->$field != $employeePayroll->$field) {
                $updatedFields[$field] = [
                    'old' => $employeePayroll->$field,
                    'new' => $request->$field
                ];
            }
        }

        return $updatedFields;
    }

    /**
     * Show salary history for an employee
     */
    public function salaryHistory($employeeId)
    {
        $employee = Employee::with('employeePayroll')->findOrFail($employeeId);
        $salaryHistory = $this->payrollChangeService->getSalaryHistory($employeeId);

        return view('admin.payroll.employees.salary-history', compact('employee', 'salaryHistory'));
    }


    /**
     * Remove the specified employee payroll record
     */
    public function destroy(EmployeePayroll $employeePayroll)
    {
        try {
            // Check if employee has any payroll records
            if ($employeePayroll->payrollRecords()->count() > 0) {
                return redirect()->back()
                    ->with('error', 'Cannot delete employee payroll record with existing payroll history.');
            }

            $employeePayroll->delete();

            return redirect()->route('payroll.employees.index')
                ->with('success', 'Employee payroll record deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error deleting employee payroll record: ' . $e->getMessage());
        }
    }

    /**
     * Toggle employee payroll status
     */
    public function toggleStatus(EmployeePayroll $employeePayroll)
    {
        try {
            $employeePayroll->update([
                'is_active' => !$employeePayroll->is_active,
                'updated_by' => auth()->id()
            ]);

            $status = $employeePayroll->is_active ? 'activated' : 'deactivated';

            return redirect()->back()
                ->with('success', "Employee payroll record {$status} successfully.");
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error updating employee status: ' . $e->getMessage());
        }
    }

    /**
     * Show import form
     */
    public function showImportForm()
    {
        return view('admin.payroll.employees.import');
    }

    /**
     * Download template for import
     */
    public function downloadTemplate()
    {
        try {
            return \Maatwebsite\Excel\Facades\Excel::download(
                new \App\Exports\EmployeePayrollTemplateExport(),
                'employee_payroll_template.xlsx'
            );
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error downloading template: ' . $e->getMessage());
        }
    }

    /**
     * Bulk import employees from Excel/CSV
     */
    public function import(Request $request)
    {
        $request->validate([
            'upload_file' => 'required|file|mimes:xlsx,xls,csv|max:10240'
        ]);

        try {
            $import = new \App\Imports\EmployeePayrollImport();
            \Maatwebsite\Excel\Facades\Excel::import($import, $request->file('upload_file'));

            return redirect()->route('payroll.employees.index')
                ->with('success', 'Employee payroll records imported successfully.');
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
            \Illuminate\Support\Facades\Log::error('Payroll import error: ' . $e->getMessage());

            return redirect()->back()
                ->withInput()
                ->with('error', 'Error importing employee records: ' . $e->getMessage());
        }
    }

    /**
     * Export employee payroll records
     */
    public function export(Request $request)
    {
        try {
            return \Maatwebsite\Excel\Facades\Excel::download(
                new \App\Exports\EmployeePayrollTemplateExport(),
                'employee_payroll_data.xlsx'
            );
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error exporting employee records: ' . $e->getMessage());
        }
    }

    /**
     * Get locations for a selected bank
     */
    public function getBranches($bankId)
    {
        $locations = BankBranch::where('bank_id', $bankId)
            ->where('status', GeneralStatus::ACTIVE)
            ->orderBy('branch_name')
            ->get(['id', 'branch_name']);

        return response()->json($locations);
    }
}
