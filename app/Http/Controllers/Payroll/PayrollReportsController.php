<?php

namespace App\Http\Controllers\Payroll;

use App\Models\SalaryDetails;
use Illuminate\Http\Request;
use App\Exports\NhifReportExport;
use App\Exports\NssfReportExport;
use App\Exports\ShifReportExport;
use App\Exports\AhlReportExport;
use App\Exports\DeductionsReportExport;
use App\Models\Payroll\DeductionType;
use App\Models\Department;
use App\Models\Payroll\PayrollPeriod; // Added
use App\Models\Payroll\PayrollRecord; // Added
use App\Models\Payroll\PayrollRecordDetail;
use Carbon\Carbon; // Added
use App\Exports\PaysumRawExport;
use App\Exports\PayrollSummaryReport;
use App\Exports\EarningsReportExport;
use App\Models\PayrollEarningTypes;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PayrollVarianceExport;
use App\Exports\PayrollInputsReportExport;
use App\Models\ApprovalWorkflow;
use App\Models\ApprovalStep;
use App\Models\ApprovalAssignment;
use App\Models\ProjectEmployeePayrollAllocation;
use App\Models\User;
use App\Models\ApprovalLog;
use App\Lib\Enumerations\ApprovalStatus;
use App\Models\EmployeeEarnings;
use App\Models\EmployeeDeductions;
use App\Models\EmployeeOvertime;
use App\Models\LeaveApplication;
use App\Models\FinancialYear;
use App\Models\LeaveType;
use App\Models\Payroll\PensionScheme;
use App\Models\Company;
class PayrollReportsController
{
    public function index()
    {
        $currentPeriod = PayrollPeriod::where('is_current', 1)->orderBy('start_date', 'desc')->first();

        $currentMonth = $currentPeriod->input_period_end->format('Y-m') ?? date('Y-m');
        // Get summary data for charts
        $chartData = $this->getReportsChartData($currentMonth);

        // Get available payroll periods for dropdown
        $periods = PayrollPeriod::orderBy('start_date', 'desc')->take(12)->get();

        return view('admin.payroll.reports.dashboard', compact('chartData', 'periods', 'currentMonth', 'currentPeriod'));
    }

    /**
     * Get chart data for reports dashboard
     */
    private function getReportsChartData($currentMonth)
    {
        // Get statutory contributions data
        $period = PayrollPeriod::where('id', $currentMonth)->first();
        if (!$period) {
            $period = PayrollPeriod::where('is_current', 1)
                ->first();
        }

        $data = [
            'statutory_breakdown' => [],
            'monthly_trends' => [],
            'department_breakdown' => [],
            'report_summary' => []
        ];

        if ($period) {
            // Get payroll records for the period
            $payrollRecords = PayrollRecord::with(['employee', 'payrollPeriod'])
                ->where('payroll_period_id', $period->id)
                ->get();

            // Statutory breakdown (pie chart data)
            $data['statutory_breakdown'] = [
                'labels' => ['PAYE Tax', 'NSSF', 'SHIF', 'Housing Levy', 'Pension'],
                'data' => [
                    $payrollRecords->sum('paye_tax'),
                    $payrollRecords->sum('nssf_contribution'),
                    $payrollRecords->sum('shif_contribution'),
                    $payrollRecords->sum('housing_levy'),
                    $payrollRecords->sum('pension_contribution')
                ],
                'colors' => ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF']
            ];

            // Monthly trends (last 6 months)
            $monthlyTrends = $this->getMonthlyTrends();
            $data['monthly_trends'] = $monthlyTrends;

            // Department breakdown (bubble chart data)
            $departmentBreakdown = $this->getDepartmentBreakdown($payrollRecords);
            $data['department_breakdown'] = $departmentBreakdown;

            // Report summary statistics
            $data['report_summary'] = [
                'total_employees' => $payrollRecords->count(),
                'total_gross' => $payrollRecords->sum('gross_salary'),
                'total_net' => $payrollRecords->sum('net_salary'),
                'total_deductions' => $payrollRecords->sum('total_deductions'),
                'total_paye' => $payrollRecords->sum('paye_tax'),
                'total_nssf' => $payrollRecords->sum('nssf_contribution'),
                'total_shif' => $payrollRecords->sum('shif_contribution'),
                'total_housing_levy' => $payrollRecords->sum('housing_levy'),
                'period_name' => $period->name
            ];
        }

        return $data;
    }

    /**
     * Get monthly trends for the last 6 months
     */
    private function getMonthlyTrends()
    {
        $months = [];
        $payeData = [];
        $nssfData = [];
        $shifData = [];
        $housingLevyData = [];

        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $monthName = $date->format('M Y');
            $months[] = $monthName;

            $period = PayrollPeriod::whereYear('start_date', $date->year)
                ->whereMonth('start_date', $date->month)
                ->first();

            if ($period) {
                $monthlyData = PayrollRecord::where('payroll_period_id', $period->id)
                    ->selectRaw('
                                               SUM(paye_tax) as total_paye,
                                               SUM(nssf_contribution) as total_nssf,
                                               SUM(shif_contribution) as total_shif,
                                               SUM(housing_levy) as total_housing_levy
                                           ')->first();

                $payeData[] = $monthlyData->total_paye ?? 0;
                $nssfData[] = $monthlyData->total_nssf ?? 0;
                $shifData[] = $monthlyData->total_shif ?? 0;
                $housingLevyData[] = $monthlyData->total_housing_levy ?? 0;
            } else {
                $payeData[] = 0;
                $nssfData[] = 0;
                $shifData[] = 0;
                $housingLevyData[] = 0;
            }
        }

        return [
            'months' => $months,
            'datasets' => [
                [
                    'label' => 'PAYE Tax',
                    'data' => $payeData,
                    'backgroundColor' => 'rgba(255, 99, 132, 0.8)',
                    'borderColor' => 'rgba(255, 99, 132, 1)',
                ],
                [
                    'label' => 'NSSF',
                    'data' => $nssfData,
                    'backgroundColor' => 'rgba(54, 162, 235, 0.8)',
                    'borderColor' => 'rgba(54, 162, 235, 1)',
                ],
                [
                    'label' => 'SHIF',
                    'data' => $shifData,
                    'backgroundColor' => 'rgba(255, 206, 86, 0.8)',
                    'borderColor' => 'rgba(255, 206, 86, 1)',
                ],
                [
                    'label' => 'Housing Levy',
                    'data' => $housingLevyData,
                    'backgroundColor' => 'rgba(75, 192, 192, 0.8)',
                    'borderColor' => 'rgba(75, 192, 192, 1)',
                ]
            ]
        ];
    }

    /**
     * Get department breakdown for bubble chart
     */
    private function getDepartmentBreakdown($payrollRecords)
    {
        $departmentData = [];

        // Group by department
        $grouped = $payrollRecords->groupBy(function ($record) {
            return $record->employee->department->department_name ?? 'Unknown';
        });

        $colors = ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF', '#FF9F40', '#FF6384', '#36A2EB'];
        $colorIndex = 0;

        foreach ($grouped as $department => $records) {
            $totalGross = $records->sum('gross_salary');
            $totalNet = $records->sum('net_salary');
            $employeeCount = $records->count();

            $departmentData[] = [
                'label' => $department,
                'data' => [[
                    'x' => $totalGross / 1000, // Convert to thousands for better display
                    'y' => $totalNet / 1000,   // Convert to thousands for better display
                    'r' => max(5, min(25, $employeeCount * 2)) // Bubble size based on employee count
                ]],
                'backgroundColor' => $colors[$colorIndex % count($colors)],
                'borderColor' => $colors[$colorIndex % count($colors)],
                'employee_count' => $employeeCount,
                'total_gross' => $totalGross,
                'total_net' => $totalNet
            ];

            $colorIndex++;
        }

        return $departmentData;
    }

    /**
     * Get reports data via AJAX
     */
    public function getChartsData(Request $request)
    {
        $periodID = $request->get('month');

        $chartData = $this->getReportsChartData($periodID);

        return response()->json($chartData);
    }


    public function nhifReportIndex(Request $request)
    {

        $currentMonth = $request->month ?? Date('Y-m', strtotime("-1 months"));
        $results = SalaryDetails::with('employee')->where('month_of_salary', $currentMonth)->orderBy('month_of_salary', 'DESC')->get();
        if ($request->action == 'Filter') {
            return view('admin.payroll.report.NHIF_Reports.nhif_monthly_reports', ['results' => $results, 'currentMonth' => $currentMonth]);
        } elseif ($request->action == 'Download') {
            $results = SalaryDetails::with('employee')->where('month_of_salary', $currentMonth)->orderBy('month_of_salary', 'DESC')->get();
            $totalNHIF = SalaryDetails::where('month_of_salary', $currentMonth)->sum('nhifRate');

            $data = ['results' => $results, 'currentMonth' => $currentMonth, 'totalNHIF' => $totalNHIF];
            return Excel::download(new NhifReportExport($data), $currentMonth . '_nhif_report.xlsx');
        }
        return view('admin.payroll.report.NHIF_Reports.nhif_monthly_reports', ['results' => $results, 'currentMonth' => $currentMonth]);
    }
    public function shifReportIndex(Request $request)
    {
        $payrollPeriods = PayrollPeriod::orderBy('start_date', 'desc')->take(24)->get();
        $companyId = resolveReportCompanyId($request);

        if ($request->filled('payroll_period_id')) {
            $period = PayrollPeriod::find($request->payroll_period_id);
        } else {
            $period = PayrollPeriod::where('is_current', true)->first();
        }

        $results = $period
            ? getStatutoryPayrollRecords($period, $companyId, 'shif_contribution')
            : collect();

        $viewData = array_merge(
            $this->payrollReportViewData($payrollPeriods, $period, $companyId, $results),
            ['totalSHIF' => $results->sum('shif_contribution')]
        );

        if ($request->action == 'Download') {
            $data = ['results' => $results, 'currentPeriod' => $period, 'totalSHIF' => $viewData['totalSHIF']];
            return Excel::download(new ShifReportExport($data), ($period->name ?? 'shif') . '_shif_report.xlsx');
        }

        return view('admin.payroll.report.SHIF_Reports.shif_monthly_reports', $viewData);
    }



    public function nssfReportIndex(Request $request)
    {
        $payrollPeriods = PayrollPeriod::orderBy('start_date', 'desc')->take(24)->get();
        $companyId = $this->resolveReportCompanyId($request);

        if ($request->filled('payroll_period_id')) {
            $period = PayrollPeriod::find($request->payroll_period_id);
        } else {
            $period = PayrollPeriod::where('is_current', true)->first();
        }

        $results = $period
            ? getStatutoryPayrollRecords($period, $companyId, 'nssf_contribution')
            : collect();

        $viewData = $this->payrollReportViewData($payrollPeriods, $period, $companyId, $results);

        if ($request->action == 'Download') {
            $data = ['results' => $results, 'currentPeriod' => $period];
            return Excel::download(new NssfReportExport($data), ($period->name ?? 'nssf') . '_nssf_report.xlsx');
        }

        return view('admin.payroll.report.NSSF_Reports.nssf_monthly_report', $viewData);
    }

    public function ahlReportIndex(Request $request)
    {
        $payrollPeriods = PayrollPeriod::orderBy('start_date', 'desc')->take(24)->get();
        $companyId = resolveReportCompanyId($request);

        if ($request->filled('payroll_period_id')) {
            $period = PayrollPeriod::find($request->payroll_period_id);
        } else {
            $period = PayrollPeriod::where('is_current', true)->first();
        }

        $results = $period
            ? getStatutoryPayrollRecords($period, $companyId, 'housing_levy')
            : collect();

        $viewData = array_merge(
            $this->payrollReportViewData($payrollPeriods, $period, $companyId, $results),
            ['totalAHL' => $results->sum('housing_levy')]
        );

        if ($request->action == 'Download') {
            $data = ['results' => $results, 'currentPeriod' => $period, 'totalAHL' => $viewData['totalAHL']];
            return Excel::download(new AhlReportExport($data), ($period->name ?? 'ahl') . '_ahl_report.xlsx');
        }

        return view('admin.payroll.report.AHL_Reports.ahl_monthly_reports', $viewData);
    }

    public function deductionsReport(Request $request)
    {
        $deductionTypes = DeductionType::where('is_statutory', false)
            ->where('name', '!=', 'Salary Advance - General')
            ->orderBy('name')->get();
        $departments = Department::orderBy('department_name')->get();
        $payrollPeriods = PayrollPeriod::orderBy('start_date', 'desc')->take(24)->get();
        $companyId = resolveReportCompanyId($request);

        // Get current period for default selection
        $currentPeriod = PayrollPeriod::where('is_current', true)->first();
        $selectedPeriodId = $request->payroll_period_id ?? $currentPeriod?->id;

        $results = $this->getDeductionsData($request, $selectedPeriodId, $companyId);

        return view('admin.payroll.report.deductions_report', array_merge([
            'results' => $results,
            'deductionTypes' => $deductionTypes,
            'departments' => $departments,
            'payrollPeriods' => $payrollPeriods,
            'selectedPeriodId' => $selectedPeriodId,
        ], reportCompanyViewData($companyId)));
    }

    private function getDeductionsData(Request $request, $periodId = null, ?int $companyId = null)
    {
        $companyId = $companyId ?? resolveReportCompanyId($request);

        $query = PayrollRecordDetail::with([
            'payrollRecord.employeePayroll.employee.department',
            'payrollRecord.payrollPeriod'
        ])->where('type', 'deduction')
            ->where('name', '!=', 'Salary Advance - General');

        // Filter by payroll period if provided
        $filterPeriodId = $request->payroll_period_id ?? $periodId;
        if ($filterPeriodId) {
            $query->whereHas('payrollRecord', function ($q) use ($filterPeriodId) {
                $q->where('payroll_period_id', $filterPeriodId);
            });
        }

        if ($request->filled('department_id')) {
            $query->whereHas('payrollRecord.employeePayroll.employee', function ($q) use ($request) {
                $q->where('department_id', $request->department_id);
            });
        }

        if ($request->filled('deduction_id')) {
            $deduction = DeductionType::find($request->deduction_id);
            if ($deduction) {
                $query->where('name', $deduction->name);
            }
        }

        return applyCompanyFilterToPayrollRecordDetails($query, $companyId)->get();
    }

    public function exportDeductionsReport(Request $request)
    {
        $currentPeriod = PayrollPeriod::where('is_current', true)->first();
        $selectedPeriodId = $request->payroll_period_id ?? $currentPeriod?->id;
        $data = $this->getDeductionsData($request, $selectedPeriodId);
        return Excel::download(new DeductionsReportExport($data), 'deductions_report.xlsx');
    }

    public function earningsReport(Request $request)
    {
        $earningTypes = PayrollEarningTypes::orderBy('name')->get();
        $departments = Department::orderBy('department_name')->get();
        $payrollPeriods = PayrollPeriod::orderBy('start_date', 'desc')->take(24)->get();
        $companyId = resolveReportCompanyId($request);

        // Get current period for default selection
        $currentPeriod = PayrollPeriod::where('is_current', true)->first();
        $selectedPeriodId = $request->payroll_period_id ?? $currentPeriod?->id;

        $results = $this->getEarningsData($request, $selectedPeriodId, $companyId);

        return view('admin.payroll.report.earnings_report', array_merge([
            'results' => $results,
            'earningTypes' => $earningTypes,
            'departments' => $departments,
            'payrollPeriods' => $payrollPeriods,
            'selectedPeriodId' => $selectedPeriodId,
        ], reportCompanyViewData($companyId)));
    }

    private function getEarningsData(Request $request, $periodId = null, ?int $companyId = null)
    {
        $companyId = $companyId ?? resolveReportCompanyId($request);

        $query = PayrollRecordDetail::with([
            'payrollRecord.employeePayroll.employee.department',
            'payrollRecord.payrollPeriod'
        ])->where('type', 'allowance');

        // Filter by payroll period if provided
        $filterPeriodId = $request->payroll_period_id ?? $periodId;
        if ($filterPeriodId) {
            $query->whereHas('payrollRecord', function ($q) use ($filterPeriodId) {
                $q->where('payroll_period_id', $filterPeriodId);
            });
        }

        if ($request->filled('department_id')) {
            $query->whereHas('payrollRecord.employeePayroll.employee', function ($q) use ($request) {
                $q->where('department_id', $request->department_id);
            });
        }

        if ($request->filled('earning_id')) {
            $earningType = PayrollEarningTypes::find($request->earning_id);
            if ($earningType) {
                $query->where('name', $earningType->name);
            }
        }

        return applyCompanyFilterToPayrollRecordDetails($query, $companyId)->get();
    }

    public function exportEarningsReport(Request $request)
    {
        $currentPeriod = PayrollPeriod::where('is_current', true)->first();
        $selectedPeriodId = $request->payroll_period_id ?? $currentPeriod?->id;
        $data = $this->getEarningsData($request, $selectedPeriodId);
        return Excel::download(new EarningsReportExport($data), 'earnings_report.xlsx');
    }

    public function exportPayrollSummaryReport(Request $request)
    {
        $currentMonth = $request->month ?? date('Y-m');
        $currentPeriod = PayrollPeriod::where('id', $request->period_id)->first();
        if (!$currentPeriod) {
            $currentPeriod = PayrollPeriod::where('is_current', true)->first();
        }

        $query = PayrollRecord::with([
            'pensionScheme',
            'employee.department',
            'employee.designation',
            'employee.branch',
            'employeePayroll',
            'employeePayroll.pensionSchemes', // Load multiple pension schemes
            'payrollPeriod',
            'details'
        ])->whereHas('payrollPeriod', function ($q) use ($request, $currentPeriod) {
            $q->where('payroll_period_id', $currentPeriod->id);
        });

        if ($request->filled('department_id')) {
            $query->whereHas('employee.department', function ($q) use ($request) {
                $q->where('department_id', $request->department_id);
            });
        }

        $payrollRecords = $query->get();

        // Get all earning and deduction types for dynamic columns
        $earningTypes = PayrollEarningTypes::where('status', \GeneralStatus::ACTIVE)->orderBy('id')->get();
        $deductionTypes = DeductionType::where('is_statutory', false)
            ->where('name', '!=', 'Salary Advance - General')
            ->orderBy('id')
            ->get();

        // Get all pension schemes for dynamic columns
        $pensionSchemes = PensionScheme::where('is_active', true)->orderBy('id')->get();

        $payrollSummaryData = [];

        foreach ($payrollRecords as $record) {
            // Fetch the employee's pension schemes (multiple)
            $employeePensionSchemes = $record->employeePayroll->pensionSchemes ?? collect();

            // Fetch employee project allocations using the correct relationship
            $projectAllocations = $record->employee->projectAllocations()
                ->with(['project', 'project.parent'])
                ->where('status', 'active')
                ->orderBy('percentage_allocated', 'desc')
                ->get();

            // Project allocations are now working correctly

            // Use basic salary as pensionable pay
            $pensionablePay = $record->basic_salary ?? 0;

            // Calculate pension contributions for each scheme
            $totalEmployeePensionContribution = 0;
            $totalEmployerPensionContribution = 0;
            $pensionSchemeDetails = [];

            foreach ($employeePensionSchemes as $scheme) {
                $employeeRate = $scheme->pivot->employee_rate ?? 0;
                $employerRate = $scheme->pivot->employer_rate ?? 0;

                $employeeContribution = $pensionablePay * ($employeeRate / 100);
                $employerContribution = $pensionablePay * ($employerRate / 100);

                $totalEmployeePensionContribution += $employeeContribution;
                $totalEmployerPensionContribution += $employerContribution;

                $pensionSchemeDetails[$scheme->name] = [
                    'employee_rate' => $employeeRate,
                    'employer_rate' => $employerRate,
                    'employee_contribution' => $employeeContribution,
                    'employer_contribution' => $employerContribution
                ];
            }
            $salaryChangeInfo = $this->processSalaryChangeMetadata($record);

            $row = [
                'employee_code' => $record->employee->payroll_number ?? $record->employee->employee_id ?? '',
                'employee_surname' => $record->employee->last_name ?? '',
                'employee_first_name' => $record->employee->first_name ?? '',
                'employee_second_name' => $record->employee->middle_name ?? '',
                'job_title' => $record->employee->designation->designation_name ?? '',
                'location' => $record->employee->branch->branch_name ?? '',
                'sub_program' => $record->employee->department->department_name ?? '',

                // Initialize earnings
                'Basic Income' => $record->basic_salary ?? 0,
                'Income Frequency' => $record->employeePayroll->income_frequency ?? '',

                // Salary Change Metadata
                'Salary_Calculation_Type' => $salaryChangeInfo['calculation_type'],
                'Salary_Changes_Count' => $salaryChangeInfo['changes_count'],
                'Salary_Segments_Count' => $salaryChangeInfo['segments_count'],
                'Salary_Segments_Details' => $salaryChangeInfo['segments_details'],
                'Prorated_Calculation' => $salaryChangeInfo['is_prorated'] ? 'Yes' : 'No',
                // Initialize company contributions - FIXED: Added missing NITA field
                'NITA_Levy' => $record->industrial_training_levy ?? 0,
                'NSSF Tier I (CompanyContribution)' => $record->nssf_tier1_company_contribution ?? 0,
                'NSSF Tier II (CompanyContribution)' => $record->nssf_tier2_company_contribution ?? 0,
                'TOTAL NSSF (CompanyContribution)' => ($record->nssf_tier1_company_contribution ?? 0) + ($record->nssf_tier2_company_contribution ?? 0),
                'Affordable Housing Levy (Company Contribution)' => $record->housing_levy_company_contribution ?? 0,
                'SHIF (Company Contribution)' => $record->shif_company_contribution ?? 0,

                // Pension scheme - total contributions
                'Pension Employee Total' => $totalEmployeePensionContribution ?? '-',
                'Pension Employer Total' => $totalEmployerPensionContribution ?? '-',
                'CompanyContribution Total' => 0,
                'Total Cost' => 0,

                // Initialize deductions - FIXED: Added NSSF Tier II deduction
                'PAYE' => $record->paye_tax ?? 0.0,
                'NSSF Tier I (Deduction)' => $record->nssf_tier1_contribution ?? 0.0,
                'NSSF Tier II (Deduction)' => $record->nssf_tier2_contribution ?? 0.0, // Added this line
                'TOTAL NSSF (Deduction)' => ($record->nssf_contribution ?? 0.0), // Updated calculation
                'SHIF' => $record->shif_contribution ?? 0.0,
                'Affordable Housing Levy(Deduction)' => $record->housing_levy ?? 0.0,
                'NetPay' => $record->net_salary ?? 0.0,

                // Analysis columns
                '30_percent_rule' => 0, // Added this key
                'Change vs Previous Payroll (%)' => 0,
                'Variance Comments' => '',

                // Pension scheme data
                'pensionable_pay' => $pensionablePay,
                'payment_reference_type' => $record->employeePayroll->payment_method ?? '',
                'Earning Total' => 0,
                'Total Deductions' => $record->total_deductions ?? 0,
                'Unpaid' => 0, // Added this missing field
                'Effective Earning' => $record->net_salary ?? 0,
            ];

            // Initialize ALL earning type columns to 0
            foreach ($earningTypes as $earningType) {
                if ($earningType->name !== 'Basic Income') {
                    $row[$earningType->name] = 0.0;
                }
            }

            // Initialize ALL deduction type columns to 0
            foreach ($deductionTypes as $deductionType) {
                $row[$deductionType->name] = 0.0;
            }

            // Initialize pension scheme columns for each active scheme
            foreach ($pensionSchemes as $scheme) {
                $row[$scheme->name . ' (Employee)'] = 0;
                $row[$scheme->name . ' (Employer)'] = 0;
                $row[$scheme->name . ' Employee Rate'] = 0;
                $row[$scheme->name . ' Employer Rate'] = 0;
            }

            // Populate pension scheme data
            foreach ($pensionSchemeDetails as $schemeName => $details) {
                $row[$schemeName . ' (Employee)'] = $details['employee_contribution'];
                $row[$schemeName . ' (Employer)'] = $details['employer_contribution'];
                $row[$schemeName . ' Employee Rate'] = $details['employee_rate'];
                $row[$schemeName . ' Employer Rate'] = $details['employer_rate'];
            }

            $earningTotal = 0;
            $deductionTotal = 0;

            foreach ($record->details as $detail) {
                if ($detail->type === 'allowance' || $detail->type === 'earning') {
                    $earningTotal += $detail->amount;
                    $row['Earning Total'] += $detail->amount;

                    // Add to specific earning column
                    if (isset($row[$detail->name])) {
                        if ($detail->name == 'Basic Income') {
                            continue;
                        }
                        $row[$detail->name] += $detail->amount;
                    }
                } elseif ($detail->type === 'deduction') {
                    $deductionTotal += $detail->amount;

                    // Add to specific deduction column
                    if (isset($row[$detail->name])) {
                        $row[$detail->name] += $detail->amount;
                    }
                }
            }

            // Calculate CompanyContribution Total and Total Cost
            $companyContributionTotal = $row['NITA_Levy'] +
                $row['NSSF Tier I (CompanyContribution)'] +
                $row['NSSF Tier II (CompanyContribution)'] +
                $row['Affordable Housing Levy (Company Contribution)'] +
                $row['SHIF (Company Contribution)'] +
                $totalEmployerPensionContribution;

            $row['CompanyContribution Total'] = $companyContributionTotal;
            $row['Total Cost'] = $row['Earning Total'] + $companyContributionTotal;

            // Calculate deductions vs earnings percentage
            $totalEarnings = $row['Earning Total'];
            $totalDeductions = $row['Total Deductions'];

            if ($totalEarnings > 0) {
                $row['30_percent_rule'] = round(($totalDeductions / $totalEarnings) * 100, 2);
            } else {
                $row['30_percent_rule'] = 0;
            }

            // Ensure all numeric fields are properly set to 0 if null/empty
            $numericFields = [
                'Basic Income',
                'Earning Total',
                'Unpaid',
                'Effective Earning',
                'PAYE',
                'NSSF Tier I (Deduction)',
                'NSSF Tier II (Deduction)',
                'TOTAL NSSF (Deduction)',
                'SHIF',
                'Affordable Housing Levy(Deduction)',
                'Pension Employee Total',
                'Total Deductions',
                'NITA_Levy',
                'NSSF Tier I (CompanyContribution)',
                'NSSF Tier II (CompanyContribution)',
                'TOTAL NSSF (CompanyContribution)',
                'Affordable Housing Levy (Company Contribution)',
                'SHIF (Company Contribution)',
                'Pension Employer Total',
                'CompanyContribution Total',
                'NetPay',
                'Total Cost',
                '30_percent_rule'
            ];

            foreach ($numericFields as $field) {
                $row[$field] = $row[$field] ?? '0.0';
            }

            // Ensure all earning types have values
            foreach ($earningTypes as $earningType) {
                if ($earningType->name !== 'Basic Income') {
                    $row[$earningType->name] = $row[$earningType->name] ?? 0.0;
                }
            }

            // Ensure all deduction types have values
            foreach ($deductionTypes as $deductionType) {
                $row[$deductionType->name] = $row[$deductionType->name] ?? 0.0;
            }

            // Ensure all pension scheme fields have values
            foreach ($pensionSchemes as $scheme) {
                $row[$scheme->name . ' (Employee)'] = $row[$scheme->name . ' (Employee)'] ?? 0;
                $row[$scheme->name . ' (Employer)'] = $row[$scheme->name . ' (Employer)'] ?? 0;
                $row[$scheme->name . ' Employee Rate'] = $row[$scheme->name . ' Employee Rate'] ?? 0;
                $row[$scheme->name . ' Employer Rate'] = $row[$scheme->name . ' Employer Rate'] ?? 0;
            }

            // Process project allocations for this employee
            $processedAllocations = $this->processProjectAllocations($projectAllocations);

            // TEMPORARY: If no allocations found, try without status filter
            if (empty($processedAllocations) && $record->employee->projectAllocations()->count() > 0) {
                $allAllocations = $record->employee->projectAllocations()
                    ->with(['project', 'project.parent'])
                    ->orderBy('percentage_allocated', 'desc')
                    ->get();
                $processedAllocations = $this->processProjectAllocations($allAllocations);
            }

            // Project allocations processed successfully

            // Add real project allocation fields to row
            $row['primary_grant'] = $processedAllocations['primary_grant'] ?? '';
            $row['primary_loe'] = $processedAllocations['primary_loe'] ?? '';
            $row['sec_grant'] = $processedAllocations['sec_grant'] ?? '';
            $row['sec_loe'] = $processedAllocations['sec_loe'] ?? '';
            $row['tertiary_project'] = $processedAllocations['tertiary_project'] ?? '';
            $row['tertiary_loe'] = $processedAllocations['tertiary_loe'] ?? '';

            $payrollSummaryData[] = $row;
        }


        return Excel::download(
            new PayrollSummaryReport(collect($payrollSummaryData), $earningTypes, $deductionTypes, $pensionSchemes),
            'payroll_summary_report_' . $currentPeriod->name . '.xlsx'
        );
    }

    /**
     * Process project allocations for an employee
     * Consolidates sub-projects under main projects and assigns to PRIMARY/SEC/Tertiary
     */
    private function processProjectAllocations($projectAllocations)
    {
        if ($projectAllocations->isEmpty()) {
            return [];
        }

        // Debug: Log processing
        \Log::info("Processing allocations:", [
            'count' => $projectAllocations->count(),
            'allocations' => $projectAllocations->pluck('percentage_allocated')->toArray()
        ]);

        // Group allocations by main project (consolidate sub-projects)
        $consolidatedProjects = [];

        foreach ($projectAllocations as $allocation) {
            $project = $allocation->project;

            // Get main project name (if it's a sub-project, use parent name)
            $mainProjectName = $project->parent ? $project->parent->name : $project->name;

            // Consolidate percentages under main project
            if (!isset($consolidatedProjects[$mainProjectName])) {
                $consolidatedProjects[$mainProjectName] = 0;
            }
            $consolidatedProjects[$mainProjectName] += $allocation->percentage_allocated;
        }

        // Sort by percentage (highest first) and assign to PRIMARY/SEC/Tertiary
        arsort($consolidatedProjects);
        $sortedProjects = array_keys($consolidatedProjects);
        $sortedPercentages = array_values($consolidatedProjects);

        $result = [];

        // Assign to PRIMARY GRANT
        if (count($sortedProjects) >= 1) {
            $result['primary_grant'] = $sortedProjects[0];
            $result['primary_loe'] = number_format($sortedPercentages[0], 2) . '%';
        }

        // Assign to SEC. GRANT
        if (count($sortedProjects) >= 2) {
            $result['sec_grant'] = $sortedProjects[1];
            $result['sec_loe'] = number_format($sortedPercentages[1], 2) . '%';
        }

        // Assign to Tertiary project
        if (count($sortedProjects) >= 3) {
            $result['tertiary_project'] = $sortedProjects[2];
            $result['tertiary_loe'] = number_format($sortedPercentages[2], 2) . '%';
        }

        return $result;
    }

    /**
     * Generate and export payroll variance report
     */
    public function varianceReport(Request $request)
    {
        $departments = Department::orderBy('department_name')->get();
        $payrollPeriods = PayrollPeriod::orderBy('start_date', 'desc')->take(24)->get();
        $companyId = resolveReportCompanyId($request);

        $currentPeriod = null;
        $previousPeriod = null;
        $currentPeriodData = collect();
        $previousPeriodData = collect();

        // Only fetch data if a payroll_period_id is explicitly selected
        if ($request->filled('payroll_period_id')) {
            $currentPeriod = PayrollPeriod::find($request->payroll_period_id);

            if ($currentPeriod) {
                if ($request->filled('compare_period_id')) {
                    $previousPeriod = PayrollPeriod::find($request->compare_period_id);
                } else {
                    $previousPeriod = PayrollPeriod::where('start_date', '<', $currentPeriod->start_date)
                        ->orderBy('start_date', 'desc')
                        ->first();
                }

                $currentPeriodData = $this->getVariancePeriodData($request, $currentPeriod, $companyId);
                if ($previousPeriod) {
                    $previousPeriodData = $this->getVariancePeriodData($request, $previousPeriod, $companyId);
                }
            }
        }

        if ($request->action === 'Download') {
            if (!$previousPeriod) {
                return redirect()->back()->with('error', 'Cannot generate variance report without a previous period for comparison.');
            }

            $company = $companyId ? Company::find($companyId) : getActiveCompany();

            return Excel::download(
                new PayrollVarianceExport($currentPeriodData, $previousPeriodData, $currentPeriod, $previousPeriod, $company),
                'payroll_variance_report_' . $currentPeriod->name . '.xlsx'
            );
        }

        return view('admin.payroll.report.variance_report', array_merge([
            'currentPeriodData' => $currentPeriodData,
            'previousPeriodData' => $previousPeriodData,
            'currentPeriod' => $currentPeriod,
            'previousPeriod' => $previousPeriod,
            'departments' => $departments,
            'payrollPeriods' => $payrollPeriods,
        ], reportCompanyViewData($companyId)));
    }

    /**
     * Export payroll variance report
     */
    public function exportVarianceReport(Request $request)
    {
        $companyId = resolveReportCompanyId($request);

        // Get current payroll period
        $currentPeriod = null;
        if ($request->filled('payroll_period_id')) {
            $currentPeriod = PayrollPeriod::find($request->payroll_period_id);
        }

        if (!$currentPeriod) {
            return redirect()->back()->with('error', 'No payroll period selected or found.');
        }

        // Get previous payroll period
        $previousPeriod = PayrollPeriod::where('start_date', '<', $currentPeriod->start_date)
            ->orderBy('start_date', 'desc')
            ->first();

        if (!$previousPeriod) {
            return redirect()->back()->with('error', 'No previous payroll period found for comparison.');
        }

        $currentPeriodData = $this->getVariancePeriodData($request, $currentPeriod, $companyId);
        $previousPeriodData = $this->getVariancePeriodData($request, $previousPeriod, $companyId);
        $company = $companyId ? Company::find($companyId) : getActiveCompany();

        return Excel::download(
            new PayrollVarianceExport($currentPeriodData, $previousPeriodData, $currentPeriod, $previousPeriod, $company),
            'payroll_variance_report_' . str_replace(' ', '_', $currentPeriod->name) . '.xlsx'
        );
    }

    private function getVariancePeriodData(Request $request, PayrollPeriod $period, ?int $companyId)
    {
        $query = PayrollRecord::with([
            'employee.department',
            'employee.designation',
            'employee.branch',
            'payrollPeriod',
            'details'
        ])->where('payroll_period_id', $period->id);

        if ($request->filled('department_id')) {
            $query->whereHas('employee.department', function ($q) use ($request) {
                $q->where('department_id', $request->department_id);
            });
        }

        return applyCompanyFilterToPayrollRecords($query, $companyId)->get();
    }

    public function payrollInputsReport()
    {
        $payrollPeriods = \App\Models\Payroll\PayrollPeriod::orderBy('start_date', 'desc')->take(24)->get();

        return view('admin.payroll.report.inputs_report', [
            'payrollPeriods' => $payrollPeriods,
        ]);
    }

    protected const MODEL_NAMESPACE_MAP = [
        'employee_earning' => \App\Models\EmployeeEarnings::class,
        'employee_deduction' => \App\Models\EmployeeDeductions::class,
        'employee_overtime' => \App\Models\EmployeeOvertime::class,
        // Add other models as needed
    ];

    public function exportPayrollInputsReport(Request $request)
    {
        $request->validate([
            'payroll_period_id' => 'required|exists:payroll_periods,id',
        ]);

        $period = \App\Models\Payroll\PayrollPeriod::find($request->payroll_period_id);
        $startDate = $period->start_date;
        $endDate = $period->end_date;

        $financialYear = FinancialYear::where('start_date', '<=', $startDate)
            ->where('end_date', '>=', $startDate)
            ->first();
        $financialYearId = $financialYear ? $financialYear->id : null;
        $periodMonth = $period->start_date->month;
        $periodMonthYear = $period->start_date->format('Y-m');

        // 1. Fetch all inputs for the period with approval details
        $allEarnings = EmployeeEarnings::with(['payrollEarningType', 'approvalLogs.user', 'employee'])
            ->where(function ($query) use ($startDate, $endDate, $periodMonth, $financialYearId) {
                $query->where(function ($q) use ($startDate, $endDate) {
                    $q->where('is_recurring', true)
                        ->where('effective_from', '<=', $endDate)
                        ->where(function ($subQ) use ($startDate) {
                            $subQ->where('effective_to', '>=', $startDate)
                                ->orWhereNull('effective_to');
                        });
                });
                if ($financialYearId) {
                    $query->orWhere(function ($q) use ($periodMonth, $financialYearId) {
                        $q->where('is_recurring', false)
                            ->where('payroll_month', $periodMonth)
                            ->where('financial_year_id', $financialYearId);
                    });
                }
            })->get();

        $allDeductions = EmployeeDeductions::with(['payrollDeductionType', 'approvalLogs.user', 'employee'])
            ->where(function ($query) use ($startDate, $endDate, $periodMonth, $financialYearId) {
                $query->where(function ($q) use ($startDate, $endDate) {
                    $q->where('is_recurring', true)
                        ->where('effective_from', '<=', $endDate)
                        ->where(function ($subQ) use ($startDate) {
                            $subQ->where('effective_to', '>=', $startDate)
                                ->orWhereNull('effective_to');
                        });
                });
                if ($financialYearId) {
                    $query->orWhere(function ($q) use ($periodMonth, $financialYearId) {
                        $q->where('is_recurring', false)
                            ->where('payroll_month', $periodMonth)
                            ->where('financial_year_id', $financialYearId);
                    });
                }
            })->get();

        $allOvertimes = EmployeeOvertime::where('month_year', $periodMonthYear)->get();

        $unpaidLeaveTypeIds = LeaveType::where('leave_type_name', 'Unpaid Leave')->pluck('leave_type_id');
        $allUnpaidLeaves = LeaveApplication::whereIn('leave_type_id', $unpaidLeaveTypeIds)
            ->where(function ($query) use ($startDate, $endDate) {
                $query->whereBetween('application_from_date', [$startDate, $endDate])
                    ->orWhereBetween('application_to_date', [$startDate, $endDate]);
            })->get();

        // 2. Get all unique employee IDs
        $employeeIds = collect([
            $allEarnings->pluck('employee_id'),
            $allDeductions->pluck('employee_id'),
            $allOvertimes->pluck('employee_id'),
            $allUnpaidLeaves->pluck('employee_id'),
        ])->flatten()->unique();

        $employees = \App\Models\Employee::with(['designation', 'department', 'branch'])->whereIn('employee_id', $employeeIds)->get();

        // 3. Group inputs by employee
        $earningsByEmployee = $allEarnings->groupBy('employee_id');
        $deductionsByEmployee = $allDeductions->groupBy('employee_id');
        $overtimesByEmployee = $allOvertimes->groupBy('employee_id');
        $unpaidLeavesByEmployee = $allUnpaidLeaves->groupBy('employee_id');

        // 4. Prepare dynamic headers and collect all unique approvers for each input type
        $staticHeaders = [
            'Employee Code',
            'Employee Surname',
            'Employee First Name',
            'Employee Second Name',
            'Job Title',
            'Location',
            'Department'
        ];
        $dynamicInputHeaders = []; // Headers like 'Earning Name (Earning)'
        $approverHeaders = []; // Unified approver email/status headers across input types
        $granularStatusHeaders = []; // Headers like 'Earning Name Status'

        $allInputTypes = [
            'earning' => ['models' => $allEarnings, 'name_field' => 'payrollEarningType.name', 'model_class' => EmployeeEarnings::class],
            'deduction' => ['models' => $allDeductions, 'name_field' => 'payrollDeductionType.name', 'model_class' => EmployeeDeductions::class],
            'overtime' => ['models' => $allOvertimes, 'name_field' => null, 'model_class' => EmployeeOvertime::class], // Overtime has no specific type name
        ];

        $maxApproversPerInputType = [];
        $approverEmailsByType = [];

        foreach ($allInputTypes as $typeKey => $typeConfig) {
            // Build headers from the full catalog of input types in the system (not only present rows)
            if ($typeKey === 'earning') {
                $uniqueInputNames = \App\Models\PayrollEarningTypes::orderBy('name')->pluck('name')->toArray();
            } elseif ($typeKey === 'deduction') {
                $uniqueInputNames = DeductionType::where('is_statutory', false)
                    ->where('name', '!=', 'Salary Advance - General')
                    ->orderBy('name')
                    ->pluck('name')
                    ->toArray();
            } else { // overtime
                $uniqueInputNames = ['Overtime Hours'];
            }

            foreach ($uniqueInputNames as $inputName) {
                $headerPrefix = $inputName . ' (' . ucfirst($typeKey) . ')';
                $dynamicInputHeaders[] = $headerPrefix;
                $granularStatusHeaders[] = $headerPrefix . ' Status';
            }

            // Collect approver emails for this input type's workflow
            $typeHeaderPrefix = ucfirst($typeKey);
            $maxApprovers = 0;
            $workflow = ApprovalWorkflow::where('model_type', $typeConfig['model_class'])->first();
            $emailsForType = [];
            if ($workflow) {
                $steps = $workflow->steps()->orderBy('level')->get();
                $maxApprovers = $steps->count();
                foreach ($steps as $step) {
                    $assignment = $step->assignments->first();
                    $emailsForType[] = $assignment && $assignment->user ? ($assignment->user->email ?? '') : '';
                }
            }
            $maxApproversPerInputType[$typeKey] = $maxApprovers;
            $approverEmailsByType[$typeKey] = $emailsForType;
        }

        // Decide unified approver columns across all input types
        $typeKeysForComparison = array_keys($allInputTypes);
        $firstTypeKey = $typeKeysForComparison[0] ?? null;
        $allSameApprovers = true;
        if ($firstTypeKey) {
            $baseline = $approverEmailsByType[$firstTypeKey] ?? [];
            foreach ($typeKeysForComparison as $tk) {
                if (($approverEmailsByType[$tk] ?? []) !== $baseline) {
                    $allSameApprovers = false;
                    break;
                }
            }
        }

        $unifiedApproverEmails = [];
        if ($allSameApprovers) {
            $unifiedApproverEmails = $approverEmailsByType[$firstTypeKey] ?? [];
        } else {
            // Fallback: choose the type with the largest number of approvers, prefer earnings, then deductions, advances, overtime
            $priority = ['earning', 'deduction', 'advance', 'overtime'];
            $chosen = null;
            $maxCount = -1;
            foreach ($priority as $tk) {
                $count = count($approverEmailsByType[$tk] ?? []);
                if ($count > $maxCount) {
                    $maxCount = $count;
                    $chosen = $tk;
                }
            }
            $unifiedApproverEmails = $chosen ? ($approverEmailsByType[$chosen] ?? []) : [];
        }

        // Build unified approver headers: Approver N Email, Approver N Status, Approver N Comment
        $maxUnified = count($unifiedApproverEmails);
        for ($i = 1; $i <= $maxUnified; $i++) {
            $approverHeaders[] = 'Approver ' . $i . ' Email';
            $approverHeaders[] = 'Approver ' . $i . ' Status';
            $approverHeaders[] = 'Approver ' . $i . ' Comment';
        }

        $otherInputHeaders = [
            'Overtime Hours (Other)',
            'Unpaid Leave Days (Other)'
        ];

        // Do NOT expose granular status columns; append approver columns at the very end
        $headers = array_merge(
            $staticHeaders,
            $dynamicInputHeaders,
            $otherInputHeaders,
            $approverHeaders
        );

        // 5. Build the report data
        $reportData = collect();
        foreach ($employees as $employee) {
            $row = array_fill_keys($headers, '');

            $row['Employee Code'] = $employee->staff_no;
            $row['Employee Surname'] = $employee->last_name;
            $row['Employee First Name'] = $employee->first_name;
            $row['Employee Second Name'] = $employee->middle_name;
            $row['Job Title'] = $employee->designation->designation_name ?? '';
            $row['Location'] = $employee->branch->branch_name ?? '';
            $row['Department'] = $employee->department->department_name ?? '';

            // Helper to get input status and approver details
            $getInputDetails = function ($inputModel, $inputHeaderPrefix) use ($maxApproversPerInputType) {
                $details = [
                    'status' => $inputModel->approval_status ?? ApprovalStatus::DRAFT,
                    'approvers' => []
                ];

                $workflow = method_exists($inputModel, 'approvalWorkflow') ? $inputModel->approvalWorkflow() : null;
                if ($workflow) {
                    $steps = $workflow->steps()->orderBy('level')->get();
                    foreach ($steps as $index => $step) {
                        $approverEmail = '';
                        $approverStatus = ApprovalStatus::DRAFT; // Default for this approver

                        // Get assigned user for this step
                        $assignment = $step->assignments->first(); // Assuming one assignment per step for simplicity
                        if ($assignment && $assignment->user) {
                            $approverEmail = $assignment->user->email;

                            // Find the log for this specific input, step, and user
                            $log = $inputModel->approvalLogs
                                ->where('approval_step_id', $step->id)
                                ->where('user_id', $assignment->user->id)
                                ->sortByDesc('created_at') // Get the latest action
                                ->first();

                            if ($log) {
                                $approverStatus = $log->action;
                            } else {
                                // If no log, but it's part of a workflow, it's pending or queued
                                // For display, if no action taken, consider it pending if the overall is pending
                                if ($details['status'] === ApprovalStatus::PENDING) {
                                    $approverStatus = ApprovalStatus::PENDING;
                                }
                            }
                        }
                        $details['approvers'][] = ['email' => $approverEmail, 'status' => $approverStatus];
                    }
                }
                return $details;
            };

            // Pre-fill unified approver email/status columns for this row
            for ($i = 1; $i <= $maxUnified; $i++) {
                $row['Approver ' . $i . ' Email'] = $unifiedApproverEmails[$i - 1] ?? '';
                $row['Approver ' . $i . ' Status'] = ApprovalStatus::PENDING; // default
                $row['Approver ' . $i . ' Comment'] = '';
            }

            // Initialize all dynamic headers with defaults to ensure color/status for every value cell
            foreach ($dynamicInputHeaders as $dynHeader) {
                $row[$dynHeader] = $row[$dynHeader] ?? 0;
                $row[$dynHeader . ' Status'] = $row[$dynHeader . ' Status'] ?? ApprovalStatus::DRAFT;
            }

            // Process earnings
            if (isset($earningsByEmployee[$employee->employee_id])) {
                foreach ($earningsByEmployee[$employee->employee_id] as $earning) {
                    if ($earning->payrollEarningType) {
                        $headerPrefix = $earning->payrollEarningType->name . ' (Earning)';
                        // Use calculated amount for percentage/daily-rate etc.
                        $row[$headerPrefix] = $earning->calculated_amount ?? $earning->amount;

                        $earningDetails = $getInputDetails($earning, $headerPrefix);
                        $row[$headerPrefix . ' Status'] = $earningDetails['status'];
                    }
                }
            }

            // Process deductions
            if (isset($deductionsByEmployee[$employee->employee_id])) {
                foreach ($deductionsByEmployee[$employee->employee_id] as $deduction) {
                    if ($deduction->payrollDeductionType) {
                        $headerPrefix = $deduction->payrollDeductionType->name . ' (Deduction)';
                        // Use calculated amount for percentage/daily-rate etc.
                        $row[$headerPrefix] = (float) ($deduction->calculated_deduction_amount ?? $deduction->amount);

                        $deductionDetails = $getInputDetails($deduction, $headerPrefix);
                        $row[$headerPrefix . ' Status'] = $deductionDetails['status'];
                    }
                }
            }

            // Process overtimes
            if (isset($overtimesByEmployee[$employee->employee_id])) {
                foreach ($overtimesByEmployee[$employee->employee_id] as $overtime) {
                    $headerPrefix = 'Overtime Hours (Other)'; // Use the generic header for overtime
                    $row[$headerPrefix] = $overtime->hours_worked; // Assuming hours_worked is the relevant field

                    $overtimeDetails = $getInputDetails($overtime, $headerPrefix);
                    $row[$headerPrefix . ' Status'] = $overtimeDetails['status'];
                }
            }

            // Unpaid leaves (no approval workflow for these, so no dynamic approver columns)
            if (isset($unpaidLeavesByEmployee[$employee->employee_id])) {
                $row['Unpaid Leave Days (Other)'] = $unpaidLeavesByEmployee[$employee->employee_id]->sum('number_of_day');
                // For unpaid leaves, we can set a default status or leave it blank if no approval is needed
                $row['Unpaid Leave Days (Other) Status'] = ApprovalStatus::NOT_APPLICABLE; // Or 'N/A'
            }


            $reportData->push($row);
        }

        return Excel::download(new PayrollInputsReportExport($reportData, $headers, $period, $reportData), 'payroll_inputs_report_' . $period->name . '.xlsx');
    }

    /**
     * Upload and process approved payroll inputs template
     */
    public function uploadApprovedInputsTemplate(Request $request)
    {
        $request->validate([
            'approved_template' => 'required|file|mimes:xlsx|max:10240', // Max 10MB
        ]);

        try {
            $uploader = auth()->user();
            if (!$uploader) {
                return redirect()->back()->with('upload_error', 'You must be logged in to upload approvals.');
            }

            // Authorization: Check if the uploader is an approver in any workflow
            $isApprover = \App\Models\ApprovalAssignment::where('user_id', $uploader->id)->exists();
            if (!$isApprover) {
                return redirect()->back()->with('upload_error', 'You are not authorized to upload payroll inputs.');
            }

            $file = $request->file('approved_template');

            // Store the file
            $originalFilename = $file->getClientOriginalName();
            $timestamp = now()->format('Y-m-d_His');
            $newFilename = "payroll_inputs_{$timestamp}_{$originalFilename}";
            $storedPath = $file->storeAs('payroll_input_uploads', $newFilename);

            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load(storage_path('app/' . $storedPath));
            $properties = $spreadsheet->getProperties();
            $payrollPeriodId = $properties->isCustomPropertySet('payroll_period_id') ? $properties->getCustomPropertyValue('payroll_period_id') : null;

            if (!$payrollPeriodId) {
                return redirect()->back()->with('upload_error', 'Invalid template: Payroll period ID is missing.');
            }

            $sheet = $spreadsheet->getActiveSheet();
            $sheetData = $sheet->toArray(null, true, true, true);
            $headers = array_shift($sheetData);

            $employeeCodeIndex = array_search('Employee Code', $headers);
            if ($employeeCodeIndex === false) {
                return redirect()->back()->with('upload_error', 'Invalid template format: Missing \'Employee Code\' column.');
            }

            $approverColumns = [];
            foreach ($headers as $index => $headerName) {
                if (preg_match('/^Approver (\d+) Status$/', $headerName, $matches)) {
                    $approverColumns[(int)$matches[1]]['status'] = $index;
                }
                if (preg_match('/^Approver (\d+) Comment$/', $headerName, $matches)) {
                    $approverColumns[(int)$matches[1]]['comment'] = $index;
                }
            }

            if (empty($approverColumns)) {
                return redirect()->back()->with('upload_error', 'Invalid template: No approver columns found.');
            }

            $processedCount = 0;
            $errorMessages = [];

            foreach ($sheetData as $rowIndex => $row) {
                $excelRowNumber = $rowIndex + 2;
                $employeeCode = trim($row[$employeeCodeIndex]);
                if (empty($employeeCode) || $employeeCode === '-') continue;

                $employee = \App\Models\Employee::where('staff_no', $employeeCode)->first();
                if (!$employee) {
                    $errorMessages[] = "Row {$excelRowNumber}: Employee '{$employeeCode}' not found.";
                    continue;
                }

                $validationResult = $this->validateTemplateIntegrity($row, $headers, $employee, $payrollPeriodId);
                if (!$validationResult['valid']) {
                    $errorMessages[] = "Row {$excelRowNumber} (Emp: {$employeeCode}): " . $validationResult['message'];
                    continue;
                }

                $allApproved = true;
                $rejectionReasons = [];
                foreach ($approverColumns as $level => $cols) {
                    if (!isset($cols['status'])) continue;
                    $status = strtolower(trim($row[$cols['status']]));
                    if ($status !== 'approved') {
                        $allApproved = false;
                        break;
                    }
                    if (isset($cols['comment'])) {
                        $comment = trim($row[$cols['comment']]);
                        if (!empty($comment)) $rejectionReasons[] = "Approver {$level}: {$comment}";
                    }
                }

                if (!$allApproved) continue;

                $finalActions = [];
                $finalReason = empty($rejectionReasons) ? 'Approved via Excel Upload' : implode("; ", $rejectionReasons);

                foreach ($headers as $colIdx => $headerName) {
                    if (!preg_match('/\(Earning\)|\(Deduction\)|\(Advance\)|Overtime Hours/', $headerName)) continue;

                    $cellCoordinate = $colIdx . $excelRowNumber;
                    $commentText = '';
                    try {
                        $comment = $sheet->getComment($cellCoordinate);
                        if ($comment && $comment->getText()) $commentText = strtolower(trim($comment->getText()));
                    } catch (\Exception $e) {
                    }

                    if ($commentText === 'rejected') {
                        $finalActions[$headerName] = ['action' => ApprovalStatus::REJECTED, 'comment' => $finalReason];
                    } else {
                        $finalActions[$headerName] = ['action' => ApprovalStatus::APPROVED, 'comment' => 'Approved via Excel Upload'];
                    }
                }

                if (empty($finalActions)) continue;

                $this->processEmployeeApprovals($employee, $finalActions, $payrollPeriodId);
                $processedCount++;
            }

            \App\Models\PayrollInputUploadLog::create([
                'payroll_period_id' => $payrollPeriodId,
                'uploaded_by' => $uploader->id,
                'uploaded_at' => now(),
                'file_name' => $originalFilename,
                'stored_file_path' => $storedPath,
                'details' => ['processed_rows' => $processedCount, 'errors' => $errorMessages]
            ]);

            if (!empty($errorMessages)) {
                return redirect()->back()->with('upload_error', "Upload completed with errors:<br>" . implode("<br>", array_slice($errorMessages, 0, 15)));
            }
            if ($processedCount === 0) {
                return redirect()->back()->with('upload_error', 'No records were updated. Please ensure all approver statuses are set to \'Approved\' and that data has not been modified.');
            }

            return redirect()->back()->with('success', "Successfully processed {$processedCount} employee approval records.");
        } catch (\Exception $e) {
            return redirect()->back()->with('upload_error', 'Error processing file: ' . $e->getMessage());
        }
    }

    /**
     * Validate that template data hasn't been modified.
     */
    private function validateTemplateIntegrity($row, $headers, $employee, $payrollPeriodId)
    {
        $period = \App\Models\Payroll\PayrollPeriod::find($payrollPeriodId);
        if (!$period) return ['valid' => false, 'message' => 'Invalid payroll period.'];

        $startDate = $period->start_date;
        $endDate = $period->end_date;
        $financialYear = \App\Models\FinancialYear::where('start_date', '<=', $startDate)->where('end_date', '>=', $startDate)->first();
        $financialYearId = $financialYear ? $financialYear->id : null;
        $periodMonth = $period->start_date->month;

        // Correctly fetch earnings for the period
        $earnings = \App\Models\EmployeeEarnings::where('employee_id', $employee->employee_id)
            ->where(function ($query) use ($startDate, $endDate, $periodMonth, $financialYearId) {
                $query->where(function ($q) use ($startDate, $endDate) {
                    $q->where('is_recurring', true)
                        ->where('effective_from', '<=', $endDate)
                        ->where(function ($subQ) use ($startDate) {
                            $subQ->where('effective_to', '>=', $startDate)->orWhereNull('effective_to');
                        });
                });
                if ($financialYearId) {
                    $query->orWhere(function ($q) use ($periodMonth, $financialYearId) {
                        $q->where('is_recurring', false)
                            ->where('payroll_month', $periodMonth)
                            ->where('financial_year_id', $financialYearId);
                    });
                }
            })->get()->keyBy('payroll_earning_type_id');

        // Correctly fetch deductions for the period
        $deductions = \App\Models\EmployeeDeductions::where('employee_id', $employee->employee_id)
            ->where(function ($query) use ($startDate, $endDate, $periodMonth, $financialYearId) {
                $query->where(function ($q) use ($startDate, $endDate) {
                    $q->where('is_recurring', true)
                        ->where('effective_from', '<=', $endDate)
                        ->where(function ($subQ) use ($startDate) {
                            $subQ->where('effective_to', '>=', $startDate)->orWhereNull('effective_to');
                        });
                });
                if ($financialYearId) {
                    $query->orWhere(function ($q) use ($periodMonth, $financialYearId) {
                        $q->where('is_recurring', false)
                            ->where('payroll_month', $periodMonth)
                            ->where('financial_year_id', $financialYearId);
                    });
                }
            })->get()->keyBy('payroll_deduction_type_id');


        foreach ($headers as $colIdx => $headerName) {
            $originalValue = null;
            $inputTypeName = null;

            if (str_contains($headerName, ' (Earning)')) {
                $inputTypeName = str_replace(' (Earning)', '', $headerName);
                $type = \App\Models\PayrollEarningTypes::where('name', $inputTypeName)->first();
                if ($type && isset($earnings[$type->id])) {
                    $originalValue = (float)($earnings[$type->id]->calculated_amount ?? $earnings[$type->id]->amount);
                }
            } elseif (str_contains($headerName, ' (Deduction)')) {
                $inputTypeName = str_replace(' (Deduction)', '', $headerName);
                $type = DeductionType::where('name', $inputTypeName)->first();
                if ($type && isset($deductions[$type->deduction_id])) {
                    $originalValue = (float)($deductions[$type->deduction_id]->calculated_deduction_amount ?? $deductions[$type->deduction_id]->amount);
                }
            }

            if (!is_null($originalValue)) {
                $uploadedValue = trim($row[$colIdx]);
                if ($uploadedValue === '' || is_null($uploadedValue)) $uploadedValue = 0;
                $uploadedValue = (float) $uploadedValue;

                if (abs($originalValue - $uploadedValue) > 0.001) {
                    return ['valid' => false, 'message' => "Data for '{$headerName}' modified. Expected {$originalValue}, found {$uploadedValue}."];
                }
            }
        }

        return ['valid' => true, 'message' => ''];
    }

    /**
     * Process approvals for a specific employee
     */
    private function processEmployeeApprovals($employee, $finalActions, $payrollPeriodId)
    {
        $period = \App\Models\Payroll\PayrollPeriod::find($payrollPeriodId);
        $startDate = $period->start_date;
        $endDate = $period->end_date;
        $financialYear = \App\Models\FinancialYear::where('start_date', '<=', $startDate)->where('end_date', '>=', $startDate)->first();
        $financialYearId = $financialYear ? $financialYear->id : null;
        $periodMonth = $period->start_date->month;
        $periodMonthYear = $period->start_date->format('Y-m');

        $findInputModel = function ($modelClass, $employeeId, $typeId) use ($startDate, $endDate, $periodMonth, $financialYearId, $periodMonthYear) {
            $query = $modelClass::where('employee_id', $employeeId);

            if ($modelClass === \App\Models\EmployeeOvertime::class) {
                return $query->where('month_year', $periodMonthYear)->first();
            }

            if ($typeId) {
                if ($modelClass === \App\Models\EmployeeEarnings::class) $query->where('payroll_earning_type_id', $typeId);
                if ($modelClass === \App\Models\EmployeeDeductions::class) $query->where('payroll_deduction_type_id', $typeId);
            }

            $query->where(function ($q) use ($startDate, $endDate, $periodMonth, $financialYearId) {
                $q->where(function ($subQ) use ($startDate, $endDate) {
                    $subQ->where('is_recurring', true)
                        ->where('effective_from', '<=', $endDate)
                        ->where(function ($ssQ) use ($startDate) {
                            $ssQ->where('effective_to', '>=', $startDate)->orWhereNull('effective_to');
                        });
                });
                if ($financialYearId) {
                    $q->orWhere(function ($subQ) use ($periodMonth, $financialYearId) {
                        $subQ->where('is_recurring', false)
                            ->where('payroll_month', $periodMonth)
                            ->where('financial_year_id', $financialYearId);
                    });
                }
            });

            return $query->first();
        };

        foreach ($finalActions as $inputHeaderName => $decision) {
            $modelClass = null;
            $typeId = null;

            if (str_contains($inputHeaderName, ' (Earning)')) {
                $name = str_replace(' (Earning)', '', $inputHeaderName);
                $type = \App\Models\PayrollEarningTypes::where('name', $name)->first();
                if ($type) {
                    $modelClass = \App\Models\EmployeeEarnings::class;
                    $typeId = $type->id;
                }
            } elseif (str_contains($inputHeaderName, ' (Deduction)')) {
                $name = str_replace(' (Deduction)', '', $inputHeaderName);
                $type = DeductionType::where('name', $name)->first();
                if ($type) {
                    $modelClass = \App\Models\EmployeeDeductions::class;
                    $typeId = $type->deduction_id;
                }
            } elseif ($inputHeaderName === 'Overtime Hours (Other)') {
                $modelClass = \App\Models\EmployeeOvertime::class;
            }

            if (!$modelClass) continue;

            $modelInstance = $findInputModel($modelClass, $employee->employee_id, $typeId);

            if ($modelInstance && $modelInstance->approval_status === ApprovalStatus::PENDING) {
                $updateData = [];
                if ($decision['action'] === ApprovalStatus::APPROVED) {
                    $updateData = [
                        'status' => \App\Lib\Enumerations\GeneralStatus::ACTIVE,
                        'approval_status' => ApprovalStatus::APPROVED,
                        'date_approved' => now()
                    ];
                } elseif ($decision['action'] === ApprovalStatus::REJECTED) {
                    $updateData = [
                        'status' => \App\Lib\Enumerations\GeneralStatus::INACTIVE,
                        'approval_status' => ApprovalStatus::REJECTED,
                    ];
                }

                if (!empty($updateData)) {
                    $modelInstance->update($updateData);

                    $lastStepId = null;
                    if (method_exists($modelInstance, 'approvalWorkflow')) {
                        $workflow = $modelInstance->approvalWorkflow();
                        if ($workflow) {
                            $lastStep = $workflow->steps()->orderBy('level', 'desc')->first();
                            if ($lastStep) $lastStepId = $lastStep->id;
                        }
                    }

                    \App\Models\ApprovalLog::create([
                        'approvable_type' => get_class($modelInstance),
                        'approvable_id' => $modelInstance->id,
                        'approval_step_id' => $lastStepId,
                        'user_id' => auth()->id(),
                        'action' => $decision['action'] === ApprovalStatus::APPROVED ? 'approved' : 'rejected',
                        'comments' => $decision['comment'],
                        'created_by' => auth()->id(),
                        'action_date' => now(),
                    ]);
                }
            }
        }
    }
    private function processSalaryChangeMetadata($payrollRecord)
    {
        $defaultInfo = [
            'calculation_type' => 'normal',
            'changes_count' => 0,
            'segments_count' => 1,
            'segments_details' => 'No salary changes',
            'is_prorated' => false
        ];

        if (!$payrollRecord->metadata) {
            return $defaultInfo;
        }

        try {
            $metadata = json_decode($payrollRecord->metadata, true);

            if (!is_array($metadata)) {
                return $defaultInfo;
            }

            $calculationType = $metadata['calculation_type'] ?? 'normal';
            $changesCount = $metadata['salary_changes_during_period'] ?? 0;
            $segments = $metadata['salary_segments'] ?? [];
            $segmentsCount = count($segments);

            // Format segments details for export
            $segmentsDetails = 'Single rate calculation';
            if ($segmentsCount > 1) {
                $segmentStrings = [];
                foreach ($segments as $index => $segment) {
                    $segmentStrings[] = sprintf(
                        "Seg%d: %s-to-%s (%s days at KES %s)",
                        $index + 1,
                        $segment['start_date'] ?? 'N/A',
                        $segment['end_date'] ?? 'N/A',
                        $segment['working_days'] ?? 0,
                        number_format($segment['salary'] ?? 0, 2)
                    );
                }
                $segmentsDetails = implode('; ', $segmentStrings);
            }

            return [
                'calculation_type' => $calculationType,
                'changes_count' => $changesCount,
                'segments_count' => $segmentsCount,
                'segments_details' => $segmentsDetails,
                'is_prorated' => $calculationType === 'prorated'
            ];
        } catch (\Exception $e) {
            \Log::error('Error processing salary change metadata: ' . $e->getMessage());
            return $defaultInfo;
        }
    }

    protected function payrollReportViewData($payrollPeriods, $period, ?int $companyId, $results): array
    {
        return array_merge([
            'results' => $results,
            'payrollPeriods' => $payrollPeriods,
            'currentPeriod' => $period,
        ], reportCompanyViewData($companyId));
    }
}