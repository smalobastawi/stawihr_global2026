<?php

namespace App\Http\Controllers\Payroll;

use App\Http\Controllers\Controller;
use App\Exports\PayeReportExport;
use App\Exports\P10ReportExport;
use App\Lib\Enumerations\ResidencyStatus;
use App\Models\Payroll\PayrollRecord;
use App\Models\Payroll\PayrollPeriod;
use App\Models\Payroll\EmployeePayroll;
use App\Models\Employee;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\StreamedResponse;



class ReportsController extends Controller
{
    /**
     * Display reports index
     */
    public function index()
    {
        $periods = PayrollPeriod::orderBy('start_date', 'desc')->limit(12)->get();
        $currentPeriod = PayrollPeriod::getCurrentPeriod();
        
        return view('admin.payroll.reports.index', compact('periods', 'currentPeriod'));
    }

    /**
     * PAYE Reports Index
     */
    public function payeIndex()
    {
        $periods = PayrollPeriod::orderBy('start_date', 'desc')->limit(12)->get();
        $employees = EmployeePayroll::with('employee')
            ->active()
            ->whereHas('employee')
            ->get();
        
        return view('admin.payroll.reports.paye.index', compact('periods', 'employees'));
    }

    /**
     * Generate PAYE Report
     */
    public function generatePayeReport(Request $request)
    {
        $request->validate([
            'period_id' => 'required|exists:payroll_periods,id',
            'format' => 'nullable|in:pdf,excel,csv',
        ]);

        $reportData = $this->buildPayeReportData($request->period_id);

        if ($request->filled('format')) {
            if ($request->format === 'pdf') {
                return $this->generatePayePdf($reportData);
            }
            if ($request->format === 'excel') {
                return $this->generatePayeExcel($reportData);
            }

            return $this->generatePayeCsv($reportData);
        }

        return view('admin.payroll.reports.paye.report', $reportData);
    }

    /**
     * Build PAYE report data for a payroll period.
     */
    private function buildPayeReportData(int $periodId): array
    {
        $period = PayrollPeriod::findOrFail($periodId);
        $records = PayrollRecord::with(['employeePayroll.employee'])
            ->where('payroll_period_id', $period->id)
            ->where('paye_tax', '>', 0)
            ->orderBy('created_at')
            ->get();

        $totalPaye = $records->sum('paye_tax');
        $totalGross = $records->sum('gross_salary');

        return [
            'period' => $period,
            'records' => $records,
            'totals' => [
                'employees' => $records->count(),
                'total_gross' => $totalGross,
                'total_paye' => $totalPaye,
            ],
        ];
    }

    /**
     * Generate P9 Form for individual employee
     */
    public function generateP9(Employee $employee, $year)
    {
        $employeePayroll = $employee->employeePayroll;
        if (!$employeePayroll) {
            return redirect()->back()->with('error', 'Employee payroll record not found.');
        }

        // Get all payroll records for the year
        $records = PayrollRecord::where('employee_payroll_id', $employeePayroll->id)
            ->whereHas('payrollPeriod', function($q) use ($year) {
                $q->whereYear('start_date', $year);
            })
            ->with('payrollPeriod')
            ->orderBy('created_at')
            ->get();

        if ($records->isEmpty()) {
            return redirect()->back()->with('error', 'No payroll records found for this employee in ' . $year);
        }

        // Calculate annual totals
        $annualTotals = [
            'gross_salary' => $records->sum('gross_salary'),
            'total_deductions' => $records->sum('total_deductions'),
            'paye_tax' => $records->sum('paye_tax'),
            'nssf_contribution' => $records->sum('nssf_contribution'),
            'shif_contribution' => $records->sum('shif_contribution'),
            'housing_levy' => $records->sum('housing_levy'),
            'pension_contribution' => $records->sum('pension_contribution'),
            'net_salary' => $records->sum('net_salary')
        ];

        return view('admin.payroll.reports.paye.p9', compact(
            'employee', 
            'employeePayroll', 
            'records', 
            'annualTotals', 
            'year'
        ));
    }

    /**
     * Generate P10 Monthly Return
     */
    public function generateP10(Request $request, PayrollPeriod $period)
    {
        $records = PayrollRecord::with(['employeePayroll.employee', 'employee.employeeType', 'details'])
            ->where('payroll_period_id', $period->id)
            ->where('paye_tax', '>', 0)
            ->orderBy('created_at')
            ->get();

        $p10Rows = $records->map(fn ($record) => $this->buildP10Row($record))->values();

        $summary = [
            'total_employees' => $records->count(),
            'total_gross_salary' => $records->sum('gross_salary'),
            'total_paye' => $records->sum('paye_tax'),
            'total_nssf' => $records->sum('nssf_contribution'),
            'total_shif' => $records->sum('shif_contribution'),
            'total_housing_levy' => $records->sum('housing_levy'),
        ];

        if ($request->query('format') === 'excel') {
            $filename = str_replace(' ', '_', $period->name) . '_p10_return.xlsx';

            return Excel::download(
                new P10ReportExport(['p10Rows' => $p10Rows, 'period' => $period, 'summary' => $summary]),
                $filename
            );
        }

        return view('admin.payroll.reports.paye.p10', compact('period', 'records', 'summary', 'p10Rows'));
    }

    /**
     * Build a KRA P10 row from a payroll record.
     */
    private function buildP10Row(PayrollRecord $record): array
    {
        $employee = $record->employee;
        $employeePayroll = $record->employeePayroll;

        return [
            'PIN of Employee' => $employeePayroll->kra_pin ?? $employee->KRA_Pin ?? '',
            'Name of Employee' => trim(($employee->first_name ?? '') . ' ' . ($employee->last_name ?? '')),
            'Resident Status' => ResidencyStatus::getName($employee->residential_status ?? ''),
            'Type of Employee' => $employee->employeeType->name ?? '',
            'Basic Salary' => $record->basic_salary ?? 0,
            'Housing Allowance' => $record->house_allowance ?? $this->sumDetailAmount($record, ['Housing Allowance', 'House Allowance']),
            'Transport Allowance' => $record->transport_allowance ?? $this->sumDetailAmount($record, ['Transport Allowance', 'Travelling Allowance']),
            'Over Time Allowance' => $record->total_overtime_amount ?? $this->sumDetailAmount($record, ['Overtime', 'Over Time Allowance', 'Overtime Totals']),
            'Other Allowance' => $record->total_allowances ?? $this->sumOtherAllowances($record),
            'Social Health Insurance Fund (J)' => $record->shif_contribution ?? 0,
            'Affordable Housing Levy (N)' => $record->housing_levy ?? 0,
            'Actual Pension Contribution (K)' => $record->pension_contribution ?? 0,
            'Amount of Insurance Relief (Ksh) (S)' => $record->insurance_relief ?? 0,
            'PAYE Tax' => $record->paye_tax ?? 0,
        ];
    }

    private function sumDetailAmount(PayrollRecord $record, array $names): float
    {
        if (!$record->relationLoaded('details') || $record->details->isEmpty()) {
            return 0;
        }

        return (float) $record->details
            ->whereIn('type', ['allowance', 'earning'])
            ->filter(fn ($detail) => in_array($detail->name, $names, true))
            ->sum('amount');
    }

    private function sumOtherAllowances(PayrollRecord $record): float
    {
        if (!$record->relationLoaded('details') || $record->details->isEmpty()) {
            return (float) ($record->total_allowances ?? 0);
        }

        $excluded = [
            'Basic Income', 'Housing Allowance', 'House Allowance',
            'Transport Allowance', 'Travelling Allowance',
            'Overtime', 'Over Time Allowance', 'Overtime Totals',
        ];

        $fromDetails = (float) $record->details
            ->whereIn('type', ['allowance', 'earning'])
            ->reject(fn ($detail) => in_array($detail->name, $excluded, true))
            ->sum('amount');

        return $fromDetails > 0 ? $fromDetails : (float) ($record->total_allowances ?? 0);
    }

    /**
     * NSSF Reports Index
     */
    public function nssfIndex()
    {
        $periods = PayrollPeriod::orderBy('start_date', 'desc')->limit(12)->get();
        
        return view('admin.payroll.reports.nssf.index', compact('periods'));
    }

    /**
     * Generate NSSF Report
     */
    public function generateNssfReport(Request $request)
    {
        $request->validate([
            'period_id' => 'required|exists:payroll_periods,id',
            'format' => 'required|in:pdf,excel,csv'
        ]);

        $period = PayrollPeriod::findOrFail($request->period_id);
        $records = PayrollRecord::with(['employeePayroll.employee'])
            ->where('payroll_period_id', $period->id)
            ->where('nssf_contribution', '>', 0)
            ->get();

        $reportData = [
            'period' => $period,
            'records' => $records,
            'totals' => [
                'employees' => $records->count(),
                'employee_contribution' => $records->sum('nssf_contribution'),
                'employer_contribution' => $records->sum('nssf_contribution'), // Same amount
                'total_contribution' => $records->sum('nssf_contribution') * 2
            ]
        ];

        return view('admin.payroll.reports.nssf.report', $reportData);
    }

    /**
     * SHIF Reports Index
     */
    public function shifIndex()
    {
        $periods = PayrollPeriod::orderBy('start_date', 'desc')->limit(12)->get();
        
        return view('admin.payroll.reports.shif.index', compact('periods'));
    }

    /**
     * Generate SHIF Report
     */
    public function generateShifReport(Request $request)
    {
        $request->validate([
            'period_id' => 'required|exists:payroll_periods,id',
            'format' => 'required|in:pdf,excel,csv'
        ]);

        $period = PayrollPeriod::findOrFail($request->period_id);
        $records = PayrollRecord::with(['employeePayroll.employee'])
            ->where('payroll_period_id', $period->id)
            ->where('shif_contribution', '>', 0)
            ->get();

        $reportData = [
            'period' => $period,
            'records' => $records,
            'totals' => [
                'employees' => $records->count(),
                'total_contribution' => $records->sum('shif_contribution')
            ]
        ];

        return view('admin.payroll.reports.shif.report', $reportData);
    }

    /**
     * Housing Levy Reports Index
     */
    public function housingLevyIndex()
    {
        $periods = PayrollPeriod::orderBy('start_date', 'desc')->limit(12)->get();
        
        return view('admin.payroll.reports.housing-levy.index', compact('periods'));
    }

    /**
     * Generate Housing Levy Report
     */
    public function generateHousingLevyReport(Request $request)
    {
        $request->validate([
            'period_id' => 'required|exists:payroll_periods,id',
            'format' => 'required|in:pdf,excel,csv'
        ]);

        $period = PayrollPeriod::findOrFail($request->period_id);
        $records = PayrollRecord::with(['employeePayroll.employee'])
            ->where('payroll_period_id', $period->id)
            ->where('housing_levy', '>', 0)
            ->get();

        $reportData = [
            'period' => $period,
            'records' => $records,
            'totals' => [
                'employees' => $records->count(),
                'total_levy' => $records->sum('housing_levy'),
                'total_gross' => $records->sum('gross_salary')
            ]
        ];

        return view('admin.payroll.reports.housing-levy.report', $reportData);
    }

    /**
     * Payroll Summary Reports Index
     */
    public function summaryIndex()
    {
        $periods = PayrollPeriod::orderBy('start_date', 'desc')->limit(12)->get();
        
        return view('admin.payroll.reports.summary.index', compact('periods'));
    }

    /**
     * Generate Summary Report
     */
    public function generateSummaryReport(Request $request)
    {
        $request->validate([
            'period_id' => 'required|exists:payroll_periods,id'
        ]);

        $period = PayrollPeriod::findOrFail($request->period_id);
        $summary = $period->getSummary();
        
        // Get department breakdown
        $departmentBreakdown = PayrollRecord::select(
                'departments.department_name',
                DB::raw('COUNT(*) as employee_count'),
                DB::raw('SUM(gross_salary) as total_gross'),
                DB::raw('SUM(total_deductions) as total_deductions'),
                DB::raw('SUM(net_salary) as total_net')
            )
            ->join('employee_payrolls', 'payroll_records.employee_payroll_id', '=', 'employee_payrolls.id')
            ->join('employees', 'employee_payrolls.employee_id', '=', 'employees.id')
            ->join('departments', 'employees.department_id', '=', 'departments.id')
            ->where('payroll_period_id', $period->id)
            ->groupBy('departments.id', 'departments.department_name')
            ->get();

        return view('admin.payroll.reports.summary.report', compact(
            'period', 
            'summary', 
            'departmentBreakdown'
        ));
    }

    /**
     * Bank Transfer Reports Index
     */
    public function bankTransferIndex()
    {
        $periods = PayrollPeriod::orderBy('start_date', 'desc')->limit(12)->get();
        
        return view('admin.payroll.reports.bank-transfer.index', compact('periods'));
    }

    /**
     * Generate Bank Transfer Report
     */
    public function generateBankTransferReport(Request $request)
    {
        $request->validate([
            'period_id' => 'required|exists:payroll_periods,id',
            'format' => 'required|in:pdf,excel,csv'
        ]);

        $period = PayrollPeriod::findOrFail($request->period_id);
        $records = PayrollRecord::with(['employeePayroll.employee'])
            ->where('payroll_period_id', $period->id)
            ->whereHas('employeePayroll', function($q) {
                $q->where('payment_method', 'bank_transfer')
                  ->whereNotNull('account_number');
            })
            ->get();

        $reportData = [
            'period' => $period,
            'records' => $records,
            'totals' => [
                'employees' => $records->count(),
                'total_amount' => $records->sum('net_salary')
            ]
        ];

        return view('admin.payroll.reports.bank-transfer.report', $reportData);
    }

    /**
     * Generate PAYE PDF Report
     */
    private function generatePayePdf(array $data)
    {
        return view('admin.payroll.reports.paye.print', $data);
    }

    /**
     * Generate PAYE Excel Report
     */
    private function generatePayeExcel(array $data)
    {
        $filename = str_replace(' ', '_', $data['period']->name) . '_paye_report.xlsx';

        return Excel::download(new PayeReportExport($data), $filename);
    }

    /**
     * Generate PAYE CSV Report
     */
    private function generatePayeCsv(array $data): StreamedResponse
    {
        $filename = str_replace(' ', '_', $data['period']->name) . '_paye_report.csv';

        return response()->streamDownload(function () use ($data) {
            $handle = fopen('php://output', 'w');

            fputcsv($handle, [
                '#',
                'Employee Code',
                'Employee Name',
                'KRA PIN',
                'Basic Salary',
                'Gross Salary',
                'NSSF',
                'SHIF',
                'Housing Levy',
                'Pension',
                'PAYE Tax',
                'Net Salary',
            ]);

            foreach ($data['records'] as $index => $record) {
                $employee = $record->employee;
                $employeePayroll = $record->employeePayroll;
                $kraPin = $employeePayroll->kra_pin ?? $employee->KRA_Pin ?? '';
                $employeeName = trim(($employee->first_name ?? '') . ' ' . ($employee->last_name ?? ''));

                fputcsv($handle, [
                    $index + 1,
                    $employee->staff_no ?? $employeePayroll->payroll_number ?? '',
                    $employeeName,
                    $kraPin,
                    $record->basic_salary ?? 0,
                    $record->gross_salary ?? 0,
                    $record->nssf_contribution ?? 0,
                    $record->shif_contribution ?? 0,
                    $record->housing_levy ?? 0,
                    $record->pension_contribution ?? 0,
                    $record->paye_tax ?? 0,
                    $record->net_salary ?? 0,
                ]);
            }

            fputcsv($handle, [
                '',
                '',
                '',
                'Totals',
                $data['records']->sum('basic_salary'),
                $data['totals']['total_gross'],
                $data['records']->sum('nssf_contribution'),
                $data['records']->sum('shif_contribution'),
                $data['records']->sum('housing_levy'),
                $data['records']->sum('pension_contribution'),
                $data['totals']['total_paye'],
                $data['records']->sum('net_salary'),
            ]);

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv',
        ]);
    }


}