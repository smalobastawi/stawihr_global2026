<?php

namespace App\Http\Controllers\Payroll;

use App\Http\Controllers\Controller;
use App\Models\Payroll\PensionScheme;
use Illuminate\Http\Request;

class PensionSchemeController extends Controller
{
    /**
     * Display a listing of pension schemes
     */
    public function index()
    {
        $pensionSchemes = PensionScheme::withCount('employeePayrolls')
            ->orderBy('name')
            ->paginate(20);

        return view('admin.payroll.settings.pension-schemes.index', compact('pensionSchemes'));
    }

    /**
     * Show the form for creating a new pension scheme
     */
    public function create()
    {
        return view('admin.payroll.settings.pension-schemes.create');
    }

    /**
     * Store a newly created pension scheme
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:pension_schemes,code',
            'description' => 'nullable|string',
            'provider_name' => 'required|string|max:255',
            'provider_contact' => 'nullable|string|max:255',
            'max_employee_rate' => 'nullable|numeric|min:0|max:100',
            'max_employer_rate' => 'nullable|numeric|min:0|max:100',
            'minimum_contribution' => 'nullable|numeric|min:0',
            'maximum_contribution' => 'nullable|numeric|min:0',
            'is_active' => 'boolean'
        ]);

        PensionScheme::create([
            'name' => $request->name,
            'code' => $request->code,
            'description' => $request->description,
            'provider_name' => $request->provider_name,
            'provider_contact' => $request->provider_contact,
            'max_employee_rate' => $request->max_employee_rate,
            'max_employer_rate' => $request->max_employer_rate,
            'minimum_contribution' => $request->minimum_contribution,
            'maximum_contribution' => $request->maximum_contribution,
            'is_active' => $request->boolean('is_active', true),
            'created_by' => auth()->id()
        ]);

        return redirect()->route('payroll.settings.pension-schemes.index')
            ->with('success', 'Pension scheme created successfully.');
    }

    /**
     * Display the specified pension scheme
     */
    public function show(PensionScheme $pensionScheme)
    {
        $pensionScheme->load(['employeePayrolls.employee.department', 'creator']);

        // Calculate statistics
        $stats = [
            'total_employees' => $pensionScheme->employeePayrolls()->count(),
            'active_employees' => $pensionScheme->employeePayrolls()->where('is_active', true)->count(),
            'total_contributions' => 0, // This would be calculated from payroll records
            'average_contribution' => 0
        ];

        // Calculate total and average contributions based on current employee salaries
        $totalContributions = 0;
        foreach ($pensionScheme->employeePayrolls as $employeePayroll) {
            if ($employeePayroll->is_active && $employeePayroll->employee) {
                $basicSalary = $employeePayroll->employee->basic_salary ?? 0;
                $employeeContribution = $pensionScheme->calculateEmployeeContribution($basicSalary);
                $employerContribution = $pensionScheme->calculateEmployerContribution($basicSalary);
                $totalContributions += ($employeeContribution + $employerContribution);
            }
        }

        $stats['total_contributions'] = $totalContributions;
        $stats['average_contribution'] = $stats['active_employees'] > 0 ?
            $totalContributions / $stats['active_employees'] : 0;

        return view('admin.payroll.settings.pension-schemes.show', compact('pensionScheme', 'stats'));
    }

    /**
     * Show the form for editing the specified pension scheme
     */
    public function edit(PensionScheme $pensionScheme)
    {
        return view('admin.payroll.settings.pension-schemes.edit', compact('pensionScheme'));
    }

    /**
     * Update the specified pension scheme
     */
    public function update(Request $request, PensionScheme $pensionScheme)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:pension_schemes,code,' . $pensionScheme->id,
            'description' => 'nullable|string',
            'provider_name' => 'required|string|max:255',
            'provider_contact' => 'nullable|string|max:255',
            'max_employee_rate' => 'nullable|numeric|min:0|max:100',
            'max_employer_rate' => 'nullable|numeric|min:0|max:100',
            'minimum_contribution' => 'nullable|numeric|min:0',
            'maximum_contribution' => 'nullable|numeric|min:0',
            'is_active' => 'boolean'
        ]);

        $pensionScheme->update([
            'name' => $request->name,
            'code' => $request->code,
            'description' => $request->description,
            'provider_name' => $request->provider_name,
            'provider_contact' => $request->provider_contact,
            'max_employee_rate' => $request->max_employee_rate,
            'max_employer_rate' => $request->max_employer_rate,
            'minimum_contribution' => $request->minimum_contribution,
            'maximum_contribution' => $request->maximum_contribution,
            'is_active' => $request->boolean('is_active'),
            'updated_by' => auth()->id()
        ]);

        return redirect()->route('payroll.settings.pension-schemes.index')
            ->with('success', 'Pension scheme updated successfully.');
    }

    /**
     * Remove the specified pension scheme
     */
    public function destroy(PensionScheme $pensionScheme)
    {
        // Check if pension scheme is being used
        if ($pensionScheme->employeePayrolls()->count() > 0) {
            return redirect()->back()
                ->with('error', 'Cannot delete pension scheme that is being used by employees.');
        }

        $pensionScheme->delete();

        return redirect()->route('payroll.settings.pension-schemes.index')
            ->with('success', 'Pension scheme deleted successfully.');
    }

    /**
     * Toggle pension scheme status
     */
    public function toggleStatus(PensionScheme $pensionScheme)
    {
        $pensionScheme->update([
            'is_active' => !$pensionScheme->is_active,
            'updated_by' => auth()->id()
        ]);

        $status = $pensionScheme->is_active ? 'activated' : 'deactivated';

        return redirect()->back()
            ->with('success', "Pension scheme {$status} successfully.");
    }

    /**
     * Calculate contribution for a given salary
     */
    public function calculateContribution(Request $request, PensionScheme $pensionScheme)
    {
        $request->validate([
            'pensionable_pay' => 'required|numeric|min:0'
        ]);

        $pensionablePay = $request->pensionable_pay;
        $employeeContribution = $pensionScheme->calculateEmployeeContribution($pensionablePay);
        $employerContribution = $pensionScheme->calculateEmployerContribution($pensionablePay);

        return response()->json([
            'pensionable_pay' => $pensionablePay,
            'employee_contribution' => $employeeContribution,
            'employer_contribution' => $employerContribution,
            'total_contribution' => $employeeContribution + $employerContribution,
            'employee_rate' => $pensionScheme->employee_contribution_rate,
            'employer_rate' => $pensionScheme->employer_contribution_rate
        ]);
    }

    /**
     * Generate pension scheme report
     */
    public function generateReport(PensionScheme $pensionScheme, Request $request)
    {
        $request->validate([
            'period_id' => 'nullable|exists:payroll_periods,id',
            'format' => 'required|in:pdf,excel,csv'
        ]);

        // Get employees in this pension scheme
        $employees = $pensionScheme->employeePayrolls()
            ->with(['employee', 'payrollRecords' => function ($query) use ($request) {
                if ($request->period_id) {
                    $query->where('payroll_period_id', $request->period_id);
                }
            }])
            ->where('is_active', true)
            ->get();

        $reportData = [
            'pension_scheme' => $pensionScheme,
            'employees' => $employees,
            'period_id' => $request->period_id,
            'totals' => [
                'employees' => $employees->count(),
                'total_employee_contributions' => 0,
                'total_employer_contributions' => 0,
                'total_contributions' => 0
            ]
        ];

        // Calculate totals from payroll records
        foreach ($employees as $employee) {
            foreach ($employee->payrollRecords as $record) {
                $reportData['totals']['total_employee_contributions'] += $record->pension_contribution;
                $reportData['totals']['total_employer_contributions'] += $record->pension_contribution; // Assuming same amount
            }
        }

        $reportData['totals']['total_contributions'] =
            $reportData['totals']['total_employee_contributions'] +
            $reportData['totals']['total_employer_contributions'];

        return view('admin.payroll.settings.pension-schemes.report', $reportData);
    }

    /**
     * Bulk create default pension schemes
     */
    public function createDefaults()
    {
        $defaultSchemes = [
            [
                'name' => 'Company Pension Scheme',
                'code' => 'company_pension',
                'description' => 'Default company pension scheme',
                'provider_name' => 'Company Pension Fund',
                'max_employee_rate' => 12.0,
                'max_employer_rate' => 6.0,
                'minimum_contribution' => 200,
                'maximum_contribution' => 20000
            ],
            [
                'name' => 'NSSF Voluntary',
                'code' => 'nssf_voluntary',
                'description' => 'NSSF voluntary additional contributions',
                'provider_name' => 'National Social Security Fund',
                'max_employee_rate' => 12.0,
                'max_employer_rate' => 6.0,
                'minimum_contribution' => 200,
                'maximum_contribution' => 18000
            ]
        ];

        $created = 0;
        foreach ($defaultSchemes as $schemeData) {
            if (!PensionScheme::where('code', $schemeData['code'])->exists()) {
                PensionScheme::create(array_merge($schemeData, [
                    'is_active' => true,
                    'created_by' => auth()->id()
                ]));
                $created++;
            }
        }

        return redirect()->back()
            ->with('success', "{$created} default pension schemes created successfully.");
    }

    /**
     * Download template for pension scheme assignments
     */
    public function downloadTemplate()
    {
        $pensionSchemes = PensionScheme::active()->orderBy('name')->get();
        $employees = \App\Models\Employee::with('department')->orderBy('first_name')->get();

        $filename = 'pension_scheme_assignments_template_' . date('Y-m-d') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($pensionSchemes, $employees) {
            $file = fopen('php://output', 'w');

            // Header row
            $header = [
                'Employee ID',
                'Payroll Number',
                'Full Name',
                'Department',
                'Basic Salary'
            ];

            foreach ($pensionSchemes as $scheme) {
                $header[] = $scheme->name . ' - Employee Rate (%)';
                $header[] = $scheme->name . ' - Employer Rate (%)';
            }

            fputcsv($file, $header);

            // Data rows
            foreach ($employees as $employee) {
                $row = [
                    $employee->employee_id,
                    $employee->payroll_number ?? '',
                    $employee->full_name ?? $employee->name ?? '',
                    $employee->department->department_name ?? '',
                    $employee->employeePayroll->basic_salary ?? 0
                ];

                foreach ($pensionSchemes as $scheme) {
                    // Get current rates if any
                    $pivot = $employee->employeePayroll?->pensionSchemes()
                        ->where('pension_scheme_id', $scheme->id)
                        ->first()?->pivot;

                    $row[] = $pivot->employee_rate ?? '';
                    $row[] = $pivot->employer_rate ?? '';
                }

                fputcsv($file, $row);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Upload pension scheme assignments
     */
    public function uploadAssignments(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt|max:10240'
        ]);

        $file = $request->file('file');
        $path = $file->getRealPath();

        $data = array_map('str_getcsv', file($path));
        $header = array_shift($data);

        // Validate header structure
        $expectedColumns = ['Employee ID', 'Payroll Number', 'Full Name', 'Department', 'Basic Salary'];
        $pensionSchemes = PensionScheme::active()->orderBy('name')->get();

        foreach ($pensionSchemes as $scheme) {
            $expectedColumns[] = $scheme->name . ' - Employee Rate (%)';
            $expectedColumns[] = $scheme->name . ' - Employer Rate (%)';
        }

        if (count($header) !== count($expectedColumns)) {
            return redirect()->back()->with('error', 'Invalid CSV format. Please use the template.');
        }

        $successCount = 0;
        $errorCount = 0;
        $errors = [];

        foreach ($data as $rowIndex => $row) {
            if (count($row) !== count($expectedColumns)) {
                $errors[] = "Row " . ($rowIndex + 2) . ": Invalid number of columns";
                $errorCount++;
                continue;
            }

            $employeeId = trim($row[0]);
            if (!$employeeId || !is_numeric($employeeId)) {
                $errors[] = "Row " . ($rowIndex + 2) . ": Invalid Employee ID";
                $errorCount++;
                continue;
            }

            $employee = \App\Models\Employee::find($employeeId);
            if (!$employee) {
                $errors[] = "Row " . ($rowIndex + 2) . ": Employee not found (ID: {$employeeId})";
                $errorCount++;
                continue;
            }

            $payrollProfile = $employee->employeePayroll;
            if (!$payrollProfile) {
                $errors[] = "Row " . ($rowIndex + 2) . ": Employee has no payroll profile";
                $errorCount++;
                continue;
            }

            // Process each pension scheme
            $columnIndex = 5; // Start after basic columns
            foreach ($pensionSchemes as $scheme) {
                $employeeRate = trim($row[$columnIndex] ?? '');
                $employerRate = trim($row[$columnIndex + 1] ?? '');

                // Skip if both rates are empty
                if (empty($employeeRate) && empty($employerRate)) {
                    $columnIndex += 2;
                    continue;
                }

                // Validate rates
                if (!is_numeric($employeeRate) || $employeeRate < 0 || $employeeRate > 100) {
                    $errors[] = "Row " . ($rowIndex + 2) . ": Invalid employee rate for {$scheme->name}";
                    $errorCount++;
                    $columnIndex += 2;
                    continue;
                }


                if (!is_numeric($employerRate) || $employerRate < 0 || $employerRate > 100) {
                    $errors[] = "Row " . ($rowIndex + 2) . ": Invalid employer rate for {$scheme->name}";
                    $errorCount++;
                    $columnIndex += 2;
                    continue;
                }

                // Update or create the relationship
                $payrollProfile->pensionSchemes()->syncWithoutDetaching([
                    $scheme->id => [
                        'employee_rate' => $employeeRate,
                        'employer_rate' => $employerRate,
                        'updated_at' => now()
                    ]
                ]);

                $columnIndex += 2;
            }

            $successCount++;
        }

        $message = "Upload completed. {$successCount} employees processed successfully.";
        if ($errorCount > 0) {
            $message .= " {$errorCount} errors occurred.";
            session()->flash('errors', $errors);
        }

        return redirect()->back()->with('success', $message);
    }
}
