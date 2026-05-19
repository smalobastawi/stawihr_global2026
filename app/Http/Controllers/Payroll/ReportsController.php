<?php

namespace App\Http\Controllers\Payroll;

use App\Http\Controllers\Controller;
use App\Models\Payroll\PayrollRecord;
use App\Models\Payroll\PayrollPeriod;
use App\Models\Payroll\EmployeePayroll;
use App\Models\Employee;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;



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
            'format' => 'required|in:pdf,excel,csv'
        ]);

        $period = PayrollPeriod::findOrFail($request->period_id);
        $records = PayrollRecord::with(['employeePayroll.employee'])
            ->where('payroll_period_id', $period->id)
            ->where('paye_tax', '>', 0)
            ->get();

        $totalPaye = $records->sum('paye_tax');
        $totalGross = $records->sum('gross_salary');

        $reportData = [
            'period' => $period,
            'records' => $records,
            'totals' => [
                'employees' => $records->count(),
                'total_gross' => $totalGross,
                'total_paye' => $totalPaye
            ]
        ];

        if ($request->format === 'pdf') {
            return $this->generatePayePdf($reportData);
        } elseif ($request->format === 'excel') {
            return $this->generatePayeExcel($reportData);
        } else {
            return $this->generatePayeCsv($reportData);
        }
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
    public function generateP10(PayrollPeriod $period)
    {
        $records = PayrollRecord::with(['employeePayroll.employee'])
            ->where('payroll_period_id', $period->id)
            ->where('paye_tax', '>', 0)
            ->get();

        $summary = [
            'total_employees' => $records->count(),
            'total_gross_salary' => $records->sum('gross_salary'),
            'total_paye' => $records->sum('paye_tax'),
            'total_nssf' => $records->sum('nssf_contribution'),
            'total_shif' => $records->sum('shif_contribution'),
            'total_housing_levy' => $records->sum('housing_levy')
        ];

        return view('admin.payroll.reports.paye.p10', compact('period', 'records', 'summary'));
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
    private function generatePayePdf($data)
    {
        // PDF generation logic would go here
        return response()->json([
            'message' => 'PAYE PDF report generated',
            'data' => $data
        ]);
    }

    /**
     * Generate PAYE Excel Report
     */
    private function generatePayeExcel($data)
    {
        // Excel generation logic would go here
        return response()->json([
            'message' => 'PAYE Excel report generated',
            'data' => $data
        ]);
    }

    /**
     * Generate PAYE CSV Report
     */
    private function generatePayeCsv($data)
    {
        // CSV generation logic would go here
        return response()->json([
            'message' => 'PAYE CSV report generated',
            'data' => $data
        ]);
    }


}