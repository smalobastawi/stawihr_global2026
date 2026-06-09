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
use App\Models\Company;
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
        $activeFinancialYear = getActiveFinancialYear();

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
     * Get calendar months (Jan–Dec) for a tax year formatted as Y-m.
     */
    private function getCalendarMonthsForTaxYear(int $taxYear): array
    {
        $months = [];
        for ($month = 1; $month <= 12; $month++) {
            $months[] = sprintf('%d-%02d', $taxYear, $month);
        }

        return $months;
    }

    /**
     * Monthly statutory caps per KRA P9A rules (effective Dec 2024).
     */
    private function getP9MonthlyCaps(string $month): array
    {
        $isPostNov2024 = $month >= '2024-12';

        return [
            'pension_cap' => $isPostNov2024 ? 30000 : 20000,
            'interest_cap' => $isPostNov2024 ? 30000 : 25000,
            'prmf_cap' => $isPostNov2024 ? 15000 : 0,
            'e3_fixed' => $isPostNov2024 ? 30000 : 20000,
            'apply_shif_ahl' => $isPostNov2024,
        ];
    }

    private function findPayrollRecordForMonth($payrollRecords, string $month): ?PayrollRecord
    {
        return $payrollRecords->first(function ($rec) use ($month) {
            if (!$rec->payrollPeriod) {
                return false;
            }

            $startMonth = $rec->payrollPeriod->start_date->format('Y-m');
            $endMonth = $rec->payrollPeriod->end_date->format('Y-m');

            return $startMonth === $month || $endMonth === $month;
        });
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
        $taxYear = (int) $endDate->format('Y');
        $months = $this->getCalendarMonthsForTaxYear($taxYear);
        $calendarStart = Carbon::create($taxYear, 1, 1)->startOfDay();
        $calendarEnd = Carbon::create($taxYear, 12, 31)->endOfDay();

        $payrollPeriods = PayrollPeriod::whereBetween('start_date', [$calendarStart, $calendarEnd])
            ->orderBy('start_date', 'asc')
            ->get();

        $periodIds = $payrollPeriods->pluck('id')->toArray();

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
            $caps = $this->getP9MonthlyCaps($month);
            $record = $this->findPayrollRecordForMonth($payrollRecords, $month);

            if ($record) {
                $A = (float) ($record->basic_salary ?? 0);
                $B = $this->getNonCashBenefits($record);
                $C = 0;
                $D = (float) ($record->gross_salary ?? 0);

                $E1 = 0.3 * $A;
                $E2 = min((float) ($record->pension_contribution ?? 0), $caps['pension_cap']);
                $E3 = $caps['e3_fixed'];
                $E_total = min($E1, $E2, $E3);

                $F = $caps['apply_shif_ahl'] ? (float) ($record->housing_levy ?? 0) : 0;
                $G = $caps['apply_shif_ahl'] ? (float) ($record->shif_contribution ?? 0) : 0;
                $H = min($this->getPRMFContribution($record), $caps['prmf_cap']);
                $I = min($this->getOwnerOccupiedInterest($record), $caps['interest_cap']);

                $J = $E_total + $F + $G + $H + $I;
                $K = max($D - $J, 0);

                $O = (float) ($record->paye_tax ?? 0);
                $M = $D > 0 ? 2400 : 0;
                $N = $this->getInsuranceRelief($record);
                $L = $O + $M + $N;

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

                foreach (['A', 'B', 'C', 'D', 'E1', 'E2', 'E3', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O'] as $col) {
                    $totals[$col] += $rowData[$col];
                }
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
            'tax_year' => $taxYear,
        ];

        return [
            'salaryDetails' => $data,
            'employeeDetails' => $employeeDetails,
            'taxationData' => $taxationData,
            'totals' => $totals,
            'months' => $months,
            'financialYearEnd' => (string) $taxYear,
        ];
    }

    /**
     * Get non-cash benefits from payroll record details
     */
    private function getNonCashBenefits(PayrollRecord $record): float
    {
        return (float) $record->details
            ->where('type', 'allowance')
            ->filter(function ($detail) {
                $name = strtolower($detail->name ?? '');
                return str_contains($name, 'benefit')
                    || str_contains($name, 'non-cash')
                    || str_contains($name, 'noncash');
            })
            ->sum('amount');
    }

    private function getPRMFContribution(PayrollRecord $record): float
    {
        return (float) $record->details
            ->filter(function ($detail) {
                $name = strtolower($detail->name ?? '');
                $code = strtolower($detail->code ?? '');

                return str_contains($name, 'prmf')
                    || str_contains($name, 'post retirement')
                    || str_contains($name, 'medical fund')
                    || str_contains($code, 'prmf');
            })
            ->sum('amount');
    }

    private function getOwnerOccupiedInterest(PayrollRecord $record): float
    {
        return (float) $record->details
            ->filter(function ($detail) {
                $name = strtolower($detail->name ?? '');
                $code = strtolower($detail->code ?? '');

                return str_contains($name, 'owner occup')
                    || str_contains($name, 'mortgage interest')
                    || str_contains($name, 'home loan interest')
                    || str_contains($code, 'interest');
            })
            ->sum('amount');
    }

    private function getInsuranceRelief(PayrollRecord $record): float
    {
        $insurancePremium = (float) $record->details
            ->filter(function ($detail) {
                $name = strtolower($detail->name ?? '');

                return str_contains($name, 'insurance')
                    || str_contains($name, 'life')
                    || str_contains($name, 'education');
            })
            ->sum('amount');

        if ($insurancePremium <= 0) {
            return 0;
        }

        return min(0.15 * $insurancePremium, 5000);
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
        $company = $this->resolveCompanyForFinancialYear($financialYear);

        $pdf_doc = Pdf::loadView('admin.payroll.p9.export', [
            'salaryDetails' => $p9Data['salaryDetails'],
            'employeeDetails' => $p9Data['employeeDetails'],
            'taxationData' => $p9Data['taxationData'],
            'totals' => $p9Data['totals'],
            'company' => $company,
            'financialYear' => $financialYear,
        ]);
        $pdf_doc->setPaper('A3', 'landscape');

        return $pdf_doc->download('P9A' . ' ' . $p9Data['employeeDetails']->first_name . ' ' . $p9Data['employeeDetails']->last_name . ' ' . $p9Data['financialYearEnd'] . '.pdf');
    }

    public function newGeneratePreview(Request $request)
    {
        if ($request->request_type == 'Download') {
            return $this->exportP9PDF($request);
        } elseif ($request->request_type == 'Send via eMail') {
            return $this->sendViaMail($request);
        }

        $financialYear = FinancialYear::findOrFail($request->financial_year_id);
        $startDate = Carbon::parse($financialYear->start_date);
        $endDate = Carbon::parse($financialYear->end_date);

        $p9Data = $this->buildP9Data($request, $startDate, $endDate);
        $company = $this->resolveCompanyForFinancialYear($financialYear);

        return view('admin.payroll.p9.preview', [
            'financial_year_end' => $p9Data['financialYearEnd'],
            'salaryDetails' => $p9Data['salaryDetails'],
            'employeeDetails' => $p9Data['employeeDetails'],
            'taxationData' => $p9Data['taxationData'],
            'totals' => $p9Data['totals'],
            'company' => $company,
            'financialYear' => $financialYear,
        ]);
    }

    public function sendViaMail(Request $request)
    {
        $financialYear = FinancialYear::findOrFail($request->financial_year_id);
        $startDate = Carbon::parse($financialYear->start_date);
        $endDate = Carbon::parse($financialYear->end_date);

        $p9Data = $this->buildP9Data($request, $startDate, $endDate);
        $company = $this->resolveCompanyForFinancialYear($financialYear);

        $pdf = Pdf::loadView('admin.payroll.p9.export', [
            'financial_year_end' => $p9Data['financialYearEnd'],
            'salaryDetails' => $p9Data['salaryDetails'],
            'employeeDetails' => $p9Data['employeeDetails'],
            'taxationData' => $p9Data['taxationData'],
            'totals' => $p9Data['totals'],
            'company' => $company,
            'financialYear' => $financialYear,
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
        $currentMonth = $request->month ?? Date('Y-m');
        $companyId = resolveReportCompanyId($request);

        $period = PayrollPeriod::whereYear('start_date', Carbon::parse($currentMonth)->year)
            ->whereMonth('start_date', Carbon::parse($currentMonth)->month)
            ->first();

        $payeReportData = collect();

        if ($period) {
            $query = PayrollRecord::with(['employee', 'payrollPeriod', 'employee.employeeType'])
                ->where('payroll_period_id', $period->id);

            $payrollRecords = applyCompanyFilterToPayrollRecords($query, $companyId)
                ->orderBy('created_at', 'DESC')
                ->get();

            foreach ($payrollRecords as $record) {
                $payeReportData[] = [
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
            }
        }

        if ($request->action == 'Download') {
            $fileName = 'PAYE_Report_' . $currentMonth . '.xlsx';
            return Excel::download(new PayeReportExport(['payeReportData' => $payeReportData, 'year' => $currentMonth]), $fileName);
        }

        return view('admin.payroll.paye.index', array_merge(
            compact('payeReportData', 'currentMonth'),
            reportCompanyViewData($companyId)
        ));
    }

    private function resolveCompanyForFinancialYear(FinancialYear $financialYear): ?Company
    {
        if ($financialYear->company_id) {
            return Company::find($financialYear->company_id);
        }

        return getActiveCompany();
    }
}
