<?php
/*
 * Copyright (c) 2023/9/6 sw@stawitech
 */

namespace App\Http\Controllers\Payroll;

use App\Exports\P9Export;
use App\Mail\Employee\PasswordResetMail;
use App\Mail\P9\SendEmployeeP9;
use App\Models\Employee;
use App\Models\Paryroll9;
use App\Http\Controllers\Controller;
use App\Models\CompanySettings;
use App\Models\SalaryDetails;
use App\Repositories\CommonRepository;
use App\Repositories\PayrollRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\PayeReportExport;
use Mail;
use App\Lib\Enumerations\ResidencyStatus;

use App\Models\Payroll\PayrollPeriod;
use App\Models\Payroll\PayrollRecord;
use App\Models\FinancialYear;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;


//use Maatwebsite\Excel\Concerns\FromView;



class Payroll9Controller extends Controller implements ShouldQueue
{
    use  InteractsWithQueue, SerializesModels;

    protected $commonRepository;
    protected $payrollRepository;

    public function __construct(CommonRepository $commonRepository, PayrollRepository $payrollRepository)
    {
        $this->commonRepository = $commonRepository;
        $this->payrollRepository = $payrollRepository;

    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $employeeList = $this->commonRepository->employeeListAllP9();
        $financialYears = FinancialYear::orderBy('start_date', 'desc')->get();
        $activeFinancialYear = FinancialYear::where('status', 1)->first();

        $results = SalaryDetails::with(['employee' => function ($query) {
            $query->with(['department', 'jobCategory']);
        }])->orderBy('salary_details_id', 'DESC')->paginate(200);

        if (request()->ajax()) {

            $results = SalaryDetails::with(['employee' => function ($query) {
                $query->with(['department', 'jobCategory']);
            }])->orderBy('salary_details_id', 'DESC');

            if ($request->monthField != '') {
                $results->where('month_of_salary', $request->monthField);
            }

            if ($request->status != '') {
                $results->where('status', $request->status);
            }

            $results = $results->paginate(200);

            return View('admin.payroll.p9.pagination', compact('results'))->render();
        }
        $departmentList = $this->commonRepository->departmentList();
        return view('admin.payroll.p9.index', [
            'results' => $results,
            'departmentList' => $departmentList,
            'employeeList' => $employeeList,
            'financialYears' => $financialYears,
            'activeFinancialYear' => $activeFinancialYear,
        ]);

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {

        return view('admin.payroll.p9.export', [
            'user' => Employee::find($request->employee_id)
        ]);
    }

    public function preview()
    {
        return view('admin.payroll.p9.preview');
    }

    /**
     * Get months array between two dates formatted as Y-m
     */
    private function getMonthsInRange(Carbon $startDate, Carbon $endDate): array
    {
        $months = [];
        $current = $startDate->copy();

        while ($current <= $endDate) {
            $months[] = $current->format('Y-m');
            $current->addMonth();
        }

        return $months;
    }

    /**
     * Build P9 data from PayrollRecord model within a date range (2025 KRA Format)
     *
     * New P9 Columns (A-O):
     * A - Basic Salary
     * B - Benefits Non-Cash
     * C - Value of Quarters
     * D - Total Gross Pay
     * E - Defined Contribution Retirement Scheme (E1=30% of A, E2=Actual, E3=Fixed 30,000)
     * F - Affordable Housing Levy (AHL)
     * G - Social Health Insurance Fund (SHIF)
     * H - Post Retirement Medical Fund (PRMF)
     * I - Owner-Occupied Interest
     * J - Total Deductions (Lower of E + F + G + H + I)
     * K - Chargeable Pay (D - J)
     * L - Tax Charged
     * M - Personal Relief
     * N - Insurance Relief
     * O - PAYE Tax (L - M - N)
     */
    private function buildP9Data(Request $request, Carbon $startDate, Carbon $endDate): array
    {
        $months = $this->getMonthsInRange($startDate, $endDate);

        // Get payroll periods within the date range
        $payrollPeriods = PayrollPeriod::whereBetween('start_date', [$startDate, $endDate])
            ->orderBy('start_date', 'asc')
            ->get();

        $periodIds = $payrollPeriods->pluck('id')->toArray();

        // Get payroll records for the employee within these periods
        $payrollRecords = PayrollRecord::where('employee_id', $request->employee_id)
            ->whereIn('payroll_period_id', $periodIds)
            ->with(['payrollPeriod', 'details'])
            ->orderBy('created_at', 'asc')
            ->get();

        $employeeDetails = Employee::where('employee_id', $request->employee_id)->first();

        $data = [];
        $totals = [
            'A' => 0, 'B' => 0, 'C' => 0, 'D' => 0,
            'E1' => 0, 'E2' => 0, 'E3' => 0, 'E_total' => 0,
            'F' => 0, 'G' => 0, 'H' => 0, 'I' => 0,
            'J' => 0, 'K' => 0, 'L' => 0, 'M' => 0, 'N' => 0, 'O' => 0,
        ];

        foreach ($months as $month) {
            // Find payroll record for this month
            $record = $payrollRecords->first(function ($rec) use ($month) {
                return $rec->payrollPeriod && $rec->payrollPeriod->start_date->format('Y-m') === $month;
            });

            if ($record) {
                // Column A: Basic Salary
                $A = $record->basic_salary ?? 0;

                // Column B: Benefits Non-Cash (from allowance details that are non-cash)
                $B = $this->getNonCashBenefits($record);

                // Column C: Value of Quarters (not tracked in current system, default 0)
                $C = 0;

                // Column D: Total Gross Pay
                $D = $record->gross_salary ?? 0;

                // Column E: Defined Contribution Retirement Scheme
                $E1 = 0.3 * $A; // 30% of A
                $E2 = $record->pension_contribution ?? 0; // Actual pension contribution
                $E3 = 30000; // Fixed 30,000 per month (2025 KRA update)
                $E_total = min($E1, $E2, $E3); // Lower of the three

                // Column F: Affordable Housing Levy (AHL)
                $F = $record->housing_levy ?? 0;

                // Column G: Social Health Insurance Fund (SHIF)
                $G = $record->shif_contribution ?? 0;

                // Column H: Post Retirement Medical Fund (PRMF)
                // Check if there's a PRMF deduction in details
                $H = $this->getPRMFContribution($record);

                // Column I: Owner-Occupied Interest (not tracked, default 0)
                $I = 0;

                // Column J: Total Deductions (Lower of E + F + G + H + I)
                $J = $E_total + $F + $G + $H + $I;

                // Column K: Chargeable Pay (D - J)
                $K = $D - $J;

                // Column L: Tax Charged (PAYE before reliefs)
                $L = $record->paye_tax ?? 0;
                // Add back reliefs to get gross tax
                $personalRelief = 2400; // Monthly personal relief
                $insuranceRelief = $this->getInsuranceRelief($record);
                $L = $L + $personalRelief + $insuranceRelief;

                // Column M: Personal Relief
                $M = $personalRelief;

                // Column N: Insurance Relief (15% of premium, max 5,000/month or 60,000/year)
                $N = $insuranceRelief;

                // Column O: PAYE Tax (L - M - N)
                $O = $L - $M - $N;

                $rowData = [
                    'month' => $month,
                    'A' => $A,
                    'B' => $B,
                    'C' => $C,
                    'D' => $D,
                    'E1' => $E1,
                    'E2' => $E2,
                    'E3' => $E3,
                    'E_total' => $E_total,
                    'F' => $F,
                    'G' => $G,
                    'H' => $H,
                    'I' => $I,
                    'J' => $J,
                    'K' => $K,
                    'L' => $L,
                    'M' => $M,
                    'N' => $N,
                    'O' => $O,
                    'hasData' => true,
                ];

                // Accumulate totals
                $totals['A'] += $A;
                $totals['B'] += $B;
                $totals['C'] += $C;
                $totals['D'] += $D;
                $totals['E1'] += $E1;
                $totals['E2'] += $E2;
                $totals['E3'] += $E3;
                $totals['E_total'] += $E_total;
                $totals['F'] += $F;
                $totals['G'] += $G;
                $totals['H'] += $H;
                $totals['I'] += $I;
                $totals['J'] += $J;
                $totals['K'] += $K;
                $totals['L'] += $L;
                $totals['M'] += $M;
                $totals['N'] += $N;
                $totals['O'] += $O;
            } else {
                $rowData = [
                    'month' => $month,
                    'A' => 0, 'B' => 0, 'C' => 0, 'D' => 0,
                    'E1' => 0, 'E2' => 0, 'E3' => 0, 'E_total' => 0,
                    'F' => 0, 'G' => 0, 'H' => 0, 'I' => 0,
                    'J' => 0, 'K' => 0, 'L' => 0, 'M' => 0, 'N' => 0, 'O' => 0,
                    'hasData' => false,
                ];
            }

            $data[] = $rowData;
        }

        $taxationData = [
            'total_chargeable_pay' => $totals['K'],
            'total_tax' => $totals['O'],
            'tax_year' => $endDate->format('Y'),
        ];

        return [
            'salaryDetails' => $data,
            'employeeDetails' => $employeeDetails,
            'taxationData' => $taxationData,
            'totals' => $totals,
            'months' => $months,
            'financialYearEnd' => $endDate->format('Y'),
        ];
    }

    /**
     * Get non-cash benefits from payroll record details
     */
    private function getNonCashBenefits(PayrollRecord $record): float
    {
        // Sum of allowance details that are marked as non-cash benefits
        // For now, return total_allowances minus cash allowances
        // This can be refined based on how benefits are categorized in details
        $nonCash = $record->details()
            ->where('type', 'allowance')
            ->where(function ($query) {
                $query->where('name', 'like', '%benefit%')
                    ->orWhere('name', 'like', '%non-cash%')
                    ->orWhere('name', 'like', '%quarters%')
                    ->orWhere('name', 'like', '%housing%');
            })
            ->sum('amount');

        return $nonCash ?: 0;
    }

    /**
     * Get PRMF (Post Retirement Medical Fund) contribution
     */
    private function getPRMFContribution(PayrollRecord $record): float
    {
        $prmf = $record->details()
            ->where(function ($query) {
                $query->where('name', 'like', '%prmf%')
                    ->orWhere('name', 'like', '%post retirement%')
                    ->orWhere('name', 'like', '%medical fund%')
                    ->orWhere('code', 'like', '%prmf%');
            })
            ->sum('amount');

        return $prmf ?: 0;
    }

    /**
     * Get Insurance Relief (15% of premium, max 5,000/month)
     */
    private function getInsuranceRelief(PayrollRecord $record): float
    {
        // Insurance relief is 15% of insurance premium paid
        // Max 5,000 per month or 60,000 per year
        $insurancePremium = $record->details()
            ->where(function ($query) {
                $query->where('name', 'like', '%insurance%')
                    ->orWhere('name', 'like', '%life%')
                    ->orWhere('name', 'like', '%education%');
            })
            ->sum('amount');

        if ($insurancePremium > 0) {
            $relief = 0.15 * $insurancePremium;
            return min($relief, 5000); // Cap at 5,000 per month
        }

        return 0;
    }

    public function generate(Request $request)
    {

        if ($request->request_type == 'Download') {
            return $this->exportP9PDF($request);
        } elseif ($request->request_type == 'Send via eMail') {
            return $this->sendViaMail($request);
        }

        $year = $request->calendar_year;

        $salaryDetails = SalaryDetails::where('employee_id', $request->employee_id)->with('employee')
            ->where('month_of_salary', 'like', $year . '%')
            ->with('SalaryBonuses', 'allowances')->orderBy('month_of_salary', 'asc')->get();
        $employeeDetails = Employee::where('employee_id', $request->employee_id)->first();
        $data = [];

        for ($startNumber = 1; $startNumber <= 12; $startNumber++) {

            $salaryDetails1 = SalaryDetails::where('employee_id', $request->employee_id)->with('employee')
                ->where('month_of_salary', "2021-1")
                ->with('SalaryBonuses', 'allowances')->orderBy('month_of_salary', 'asc')->get();

            $deta = $salaryDetails->where('month_of_salary', '=', $year . '-' . $startNumber)->first();

            array_push($data, array(
                'month' => $year . '-' . $startNumber,
                'paymentDetails' => $deta
            ));
        }

        $totalTax_H = collect($salaryDetails)->sum('PAYE_tax');
        $sumTaxableSalary = collect($salaryDetails)->sum('taxable_salary');

        $totalA = collect($salaryDetails)->sum('basic_salary');
        $totalB = 0;
        $totalC = 0;
        $totalD = collect($salaryDetails)->sum('gross_pay');
        $totalE1 = 0.3 * collect($salaryDetails)->sum('basic_salary');
        $totalE2 = collect($salaryDetails)->sum('nssf_amount');
        $totalE3 = count($salaryDetails) * 20000;
        $totalF = 0;
        $totalG = count($salaryDetails) * 20000;
        $totalH = collect($salaryDetails)->sum('taxable_salary');

        $totalJ = collect($salaryDetails)->sum('PAYE_tax') + count($salaryDetails) * 2400;
        $totalK = count($salaryDetails) * 2400;
        $totalL = collect($salaryDetails)->sum('PAYE_tax');
        $taxationData = [];
        $totals [] = [
            'totalA' => $totalA,
            'totalB' => $totalB,
            'totalC' => $totalC,
            'totalD' => $totalD,
            'totalE1' => $totalE1,
            'totalE2' => $totalE2,
            'totalE3' => $totalE3,
            'totalF' => $totalF,
            'totalG' => $totalG,
            'totalH' => $totalH,
            'totalJ' => $totalJ,
            'totalK' => $totalK,
            'totalL' => $totalL,


        ];

        $taxationData['total_chargeable_pay'] = $sumTaxableSalary;
        $taxationData['total_tax'] = $totalTax_H;
        $taxationData['tax_year'] = $year;


        return view('admin.payroll.p9.preview', ['financial_year_end' => $year, 'salaryDetails' => $data, 'employeeDetails' => $employeeDetails, 'taxationData' => $taxationData, 'totals' => $totals]);

    }

    public function exportP9PDF(Request $request)
    {
        $financialYear = FinancialYear::findOrFail($request->financial_year_id);
        $startDate = Carbon::parse($financialYear->start_date);
        $endDate = Carbon::parse($financialYear->end_date);

        $p9Data = $this->buildP9Data($request, $startDate, $endDate);
        $companySettings = CompanySettings::orderBy('id', 'desc')->first();

        $pdf_doc = Pdf::loadView('admin.payroll.p9.export', [
            'salaryDetails' => $p9Data['salaryDetails'],
            'employeeDetails' => $p9Data['employeeDetails'],
            'taxationData' => $p9Data['taxationData'],
            'totals' => $p9Data['totals'],
            'companySettings' => $companySettings,
            'financialYear' => $financialYear,
        ]);
        $pdf_doc->setPaper('A3', 'landscape');

        return $pdf_doc->download('P9A' . ' ' . $p9Data['employeeDetails']->first_name . ' ' . $p9Data['employeeDetails']->last_name . ' ' . $p9Data['financialYearEnd'] . '.pdf');
    }

    public function newGeneratePreview(Request $request)
    {
        $companySettings = CompanySettings::orderBy('id', 'desc')->first();

        if ($request->request_type == 'Download') {
            return $this->exportP9PDF($request);
        } elseif ($request->request_type == 'Send via eMail') {
            return $this->sendViaMail($request);
        }

        $financialYear = FinancialYear::findOrFail($request->financial_year_id);
        $startDate = Carbon::parse($financialYear->start_date);
        $endDate = Carbon::parse($financialYear->end_date);

        $p9Data = $this->buildP9Data($request, $startDate, $endDate);

        return view('admin.payroll.p9.preview', [
            'financial_year_end' => $p9Data['financialYearEnd'],
            'salaryDetails' => $p9Data['salaryDetails'],
            'employeeDetails' => $p9Data['employeeDetails'],
            'taxationData' => $p9Data['taxationData'],
            'totals' => $p9Data['totals'],
            'companySettings' => $companySettings,
            'financialYear' => $financialYear,
        ]);
    }

    public function sendViaMail(Request $request)
    {
        $financialYear = FinancialYear::findOrFail($request->financial_year_id);
        $startDate = Carbon::parse($financialYear->start_date);
        $endDate = Carbon::parse($financialYear->end_date);

        $p9Data = $this->buildP9Data($request, $startDate, $endDate);

        $pdf = PDF::loadView('admin.payroll.p9.export', [
            'financial_year_end' => $p9Data['financialYearEnd'],
            'salaryDetails' => $p9Data['salaryDetails'],
            'employeeDetails' => $p9Data['employeeDetails'],
            'taxationData' => $p9Data['taxationData'],
            'totals' => $p9Data['totals'],
        ]);
        $pdf->setPaper('A3', 'landscape');

        $mailContent = ([
            'employee_name' => $p9Data['employeeDetails']->first_name,
            'p9form' => $pdf,
            'employeeEmail' => $p9Data['employeeDetails']->email
        ]);

        try {
            Mail::send('emails.send_employee_p9', $mailContent, function($message)use($mailContent,$pdf) {
                $message->to('smaloba3@gmail.com', $mailContent["employee_name"])
                    ->subject('P9 form')
                    ->attachData($pdf->output(), "P9 Form.pdf");
            });

        } catch (JWTException $exception) {
            $this->serverstatuscode = "0";
            $this->serverstatusdes = $exception->getMessage();
        }

        return redirect()->back()->with(['success' => 'Email sent successfully.']);
    }

    public function payeReportIndex(Request $request)
    {
        $currentMonth = $request->month ?? Date('Y-m'); // Default to current month

        // Find the payroll period for the current month
        $period = PayrollPeriod::whereYear('start_date', Carbon::parse($currentMonth)->year)
                               ->whereMonth('start_date', Carbon::parse($currentMonth)->month)
                               ->first();

        $payeReportData = collect(); // Initialize as empty collection

        if ($period) {
            $payrollRecords = PayrollRecord::with(['employee', 'payrollPeriod', 'employee.employeeType'])
                                            ->where('payroll_period_id', $period->id)
                                            ->orderBy('created_at', 'DESC')
                                            ->get();

            // Loop through payroll records and prepare data for the report
            foreach ($payrollRecords as $record) {
                $rowData = [
                    'PIN of Employee' => $record->employee->KRA_Pin ?? '',
                    'Name of Employee' => ($record->employee->first_name ?? '') . ' ' . ($record->employee->last_name ?? ''),
                    'Resident Status' => ResidencyStatus::getName($record->employee->residential_status ?? ''),
                    'Type of Employee' => $record->employee->employeeType->name ?? '',
                    'Basic Salary' => $record->basic_salary ?? 0,
                    'Housing Allowance' => $record->house_allowance ?? 0,
                    'Transport Allowance' => $record->transport_allowance ?? 0,
                    'Over Time Allowance' => $record->total_overtime_amount ?? 0,
                    'Other Allowance' => $record->total_allowance ?? 0,
                    'Social Health Insurance Fund (J)' => $record->shif_contribution ?? 0,
                    'Affordable Housing Levy (N)' => $record->housing_levy ?? 0,
                    'Actual Pension Contribution (K)' => $record->pension_contribution ?? 0,
                    'Amount of Insurance Relief (Ksh) (S)' => $record->insurance_relief ?? 0,
                    'PAYE Tax' => $record->paye_tax ?? 0,
                ];
                $payeReportData[] = $rowData;
            }
        }

        if ($request->action == 'Download') {
            $fileName = 'PAYE_Report_' . $currentMonth . '.xlsx'; // Use currentMonth for filename
            return Excel::download(new PayeReportExport(['payeReportData' => $payeReportData, 'year' => $currentMonth]), $fileName); // Pass currentMonth
        }

        return view('admin.payroll.paye.index', compact('payeReportData', 'currentMonth')); // Pass currentMonth
    }
}
