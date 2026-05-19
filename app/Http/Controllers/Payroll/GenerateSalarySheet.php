<?php
/*
 * Copyright (c) 2023/9/6 sw@stawitech
 */

namespace App\Http\Controllers\Payroll;

use App\Models\Advances;
use App\Models\Attendance;
use App\Models\Payroll\DeductionType;
use App\Models\NHIF;
use App\Models\SalaryDetailsToAdvances;
use App\Models\SalaryDetailsToBonuses;
use App\Repositories\PayrollRepository;
use App\Models\SalaryDetailsToAllowance;
use App\Lib\Enumerations\LeaveStatus;
use App\Models\CompanyAddressSetting;
use App\Models\SalaryDetailsToLeave;
use App\Models\SalaryDetailsToDeduction;
use App\Repositories\CommonRepository;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use App\Models\LeaveApplication;
use App\Models\PrintHeadSetting;
use Illuminate\Http\Request;
use App\Models\SalaryDetails;
use App\Models\Employee;
use Carbon\Carbon;
use DateTime;
use DateInterval;
use Excel;
use App\Models\PayrollFormula;
use function Composer\Autoload\includeFile;
use App\Repositories\PayrollCalculations;

use App\Services\Payroll\FormulaEvaluatorService;

class GenerateSalarySheet extends Controller
{

    protected $commonRepository;
    protected $payrollRepository;
    protected $payrollCalculations;
    protected $currentMonth;
    protected $formulaEvaluator;

    public function __construct(FormulaEvaluatorService $formulaEvaluator, PayrollCalculations $payrollCalculations, CommonRepository $commonRepository, PayrollRepository $payrollRepository)
    {
        $this->commonRepository = $commonRepository;
        $this->payrollRepository = $payrollRepository;
        $this->payrollCalculations = $payrollCalculations;
        $this->currentMonth = Carbon::now()->format('Y-m');
        $this->formulaEvaluator = $formulaEvaluator;
    }

    public function index(Request $request)
    {

        $currentMonth = Carbon::now()->format('Y-m');
        $results = SalaryDetails::with(['employee' => function ($query) {
            $query->with(['department']);
        }])->orderBy('salary_details_id', 'DESC')->paginate(50);
        if (request()->ajax()) {

            $results = SalaryDetails::with(['employee' => function ($query) {
                $query->with(['department']);
            }])->orderBy('salary_details_id', 'DESC');

            if ($request->monthField != '') {
                $results->where('month_of_salary', $request->monthField);
            }

            if ($request->status !== '') {
                $results->where('status', $request->status);
            }

            $results = $results->paginate(50);

            return View('admin.payroll.salarySheet.pagination', compact('results'))->render();
        }
        $departmentList = $this->commonRepository->departmentList();
        return view('admin.payroll.salarySheet.salaryDetails', compact('results'), ['departmentList' => $departmentList]);
    }

    public function monthSalary(Request $request)
    {
        $results = SalaryDetails::with(['employee' => function ($query) {
            $query->with('employeePayroll');
        }])->where('month_of_salary', $request->month)->get();

        return view('admin.payroll.salarySheet.salaryDetails', ['results' => $results]);
    }

    public function create()
    {

        $user = auth()->user();
        $employeeList = [];


        $employeeList = $this->commonRepository->employeeList();
        $currentMonth = Carbon::now()->format('Y-m');
        return view('admin.payroll.salarySheet.generateSalarySheet', ['employeeList' => $employeeList, 'currentMonth' => $currentMonth]);
    }

    public function store(Request $request)
    {
        $input = $request->all();

        $input['created_by'] = Auth::user()->id;
        $input['updated_by'] = Auth::user()->id;
        $queryResult11 = SalaryDetails::where('employee_id', $input['employee_id'])->where('month_of_salary', $input['month_of_salary'])->count();
        if ($queryResult11 > 0) {
            return redirect('generateSalarySheet')->with('error', 'Salary already recorded for this month.');
        } else {
            try {
                DB::beginTransaction();

                $parentData = SalaryDetails::create($input);
                $employeeSalaryDetailsToAllowance = $this->makeEmployeeSalaryDetailsToAllowanceDataFormat($request->all(), $parentData->salary_details_id);

                if (count($employeeSalaryDetailsToAllowance) > 0) {
                    SalaryDetailsToAllowance::insert($employeeSalaryDetailsToAllowance);
                }

                $employeeSalaryDetailsToDeduction = $this->makeEmployeeSalaryDetailsToDeductionDataFormat($request->all(), $parentData->salary_details_id);
                if (count($employeeSalaryDetailsToDeduction) > 0) {
                    SalaryDetailsToDeduction::insert($employeeSalaryDetailsToDeduction);
                }

                $employeeSalaryDetailsToAdvances = $this->makeEmployeeSalaryDetailsToAdvancesDataFormat($request->all(), $parentData->salary_details_id);
                if (count($employeeSalaryDetailsToAdvances) > 0) {
                    SalaryDetailsToAdvances::insert($employeeSalaryDetailsToAdvances);
                }

                $employeeSalaryDetailsToBonuses = $this->makeEmployeeSalaryDetailsToBonusesDataFormat($request->all(), $parentData->salary_details_id);
                if (count($employeeSalaryDetailsToBonuses) > 0) {
                    SalaryDetailsToBonuses::insert($employeeSalaryDetailsToBonuses);
                }

                $employeeSalaryDetailsToLeave = $this->makeEmployeeSalaryDetailsToLeaveDataFormat($request->all(), $parentData->salary_details_id);
                if (count($employeeSalaryDetailsToLeave) > 0) {
                    SalaryDetailsToLeave::insert($employeeSalaryDetailsToLeave);
                }


                DB::commit();
                $bug = 0;
                if ($bug == 0) {
                    // return redirect()->back()->with('success', 'Salary Generate successfully.');
                    return redirect()->route('generateSalarySheet.create')->with('success', 'Salary Generate successfully.');
                } else {
                    return redirect()->route('generateSalarySheet.create')->with('error', 'An error occured, please try again. If the problem persists, contact Support for assistance. ');
                }
            } catch (\Exception $e) {
                \Log::info($e->getMessage());
                DB::rollback();
                $bug = $e->getMessage();
            }
        }
    }

    public function makeEmployeeSalaryDetailsToAllowanceDataFormat($data, $salary_details_id)
    {
        $allowanceData = [];
        if (isset($data['allowance_id'])) {
            for ($i = 0; $i < count($data['allowance_id']); $i++) {
                $allowanceData[$i] = [
                    'salary_details_id' => $salary_details_id,
                    'allowance_id' => $data['allowance_id'][$i],
                    'amount_of_allowance' => $data['amount_of_allowance'][$i],
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ];
            }
        }
        return $allowanceData;
    }

    public function makeEmployeeSalaryDetailsToDeductionDataFormat($data, $salary_details_id)
    {
        $deductionData = [];
        if (isset($data['deduction_id'])) {
            for ($i = 0; $i < count($data['deduction_id']); $i++) {
                $deductionData[$i] = [
                    'salary_details_id' => $salary_details_id,
                    'deduction_id' => $data['deduction_id'][$i],
                    'amount_of_deduction' => $data['amount_of_deduction'][$i],
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ];
            }
        }
        return $deductionData;
    }

    //Salary Details to Advances here
    public function makeEmployeeSalaryDetailsToAdvancesDataFormat($data, $salary_details_id)
    {
        $salaryAdvanceData = [];
        if (isset($data['salary_advance_id'])) {
            for ($i = 0; $i < count($data['salary_advance_id']); $i++) {
                $salaryAdvanceData[$i] = [
                    'salary_details_id' => $salary_details_id,
                    'salary_advance_id' => $data['salary_advance_id'][$i],
                    'amount_of_advance' => $data['salary_advance_amount'][$i],
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ];
            }
        }
        return $salaryAdvanceData;
    }

    //Salary Details to Bonuses here
    public function makeEmployeeSalaryDetailsToBonusesDataFormat($data, $salary_details_id)
    {
        $salaryBonusData = [];
        if (isset($data['salary_bonus_id'])) {
            for ($i = 0; $i < count($data['salary_bonus_id']); $i++) {
                $salaryBonusData[$i] = [
                    'salary_details_id' => $salary_details_id,
                    'salary_bonus_id' => $data['salary_bonus_id'][$i],
                    'amount_of_bonus' => $data['salary_bonus_amount'][$i],
                    'bonus_name' => $data['salary_bonus_name'][$i],
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ];
            }
        }
        return $salaryBonusData;
    }
    public function makeEmployeeSalaryDetailsToLeaveDataFormat($data, $salary_details_id)
    {
        $leaveData = [];
        if (isset($data['num_of_day'])) {
            for ($i = 0; $i < count($data['num_of_day']); $i++) {
                $leaveData[$i] = [
                    'salary_details_id' => $salary_details_id,
                    'num_of_day' => $data['num_of_day'][$i],
                    'leave_type_id' => $data['leave_type_id'][$i],
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ];
            }
        }
        return $leaveData;
    }


    public function makePayment(Request $request)
    {
        $data['status'] = 1;
        $data['comment'] = $request->comment;
        $data['payment_method'] = $request->payment_method;
        $data['created_by'] = Auth::user()->id;
        $data['updated_by'] = Auth::user()->id;
        $data['created_at'] = Carbon::now();
        $data['updated_at'] = Carbon::now();
        try {
            SalaryDetails::where('salary_details_id', $request->salary_details_id)->update($data);
            $bug = 0;
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            \Log::info($e->getMessage());
        }

        if ($bug == 0) {
            echo "success";
        } else {
            echo "error";
        }
    }




    public function paySlipDataFormat($id)
    {

        $printHeadSetting = PrintHeadSetting::first();
        $salaryDetails = SalaryDetails::select('salary_details.*', 'employee.employee_id', 'employee.department_id', 'employee.designation_id', 'department.department_name', 'designation.designation_name', 'employee.first_name', 'employee.last_name', 'employee.date_of_joining')
            ->join('employee', 'employee.employee_id', 'salary_details.employee_id')
            ->join('department', 'department.department_id', 'employee.department_id')
            ->join('designation', 'designation.designation_id', 'employee.designation_id')
            ->where('salary_details_id', '=', $id)->first();


        $salaryDetailsToAllowance = SalaryDetailsToAllowance::join('allowance', 'allowance.allowance_id', 'salary_details_to_allowance.allowance_id')
            ->where('salary_details_id', $id)->get();

        $salaryDetailsToDeduction = SalaryDetailsToDeduction::join('deduction', 'deduction.deduction_id', 'salary_details_to_deduction.deduction_id')
            ->where('salary_details_id', $id)->get();

        $salaryDetailsToAdvances = SalaryDetailsToAdvances::join('salary_advances', 'salary_advances.id', 'salary_details_to_advances.salary_advance_id')
            ->where('salary_details_id', $id)->get();

        $salaryDetailsToBonuses = SalaryDetailsToBonuses::join('salary_bonuses', 'salary_bonuses.salary_bonus_id', 'salary_details_to_bonuses.salary_bonus_id')
            ->where('salary_details_id', $id)->get();
        $salaryDetailsToBonuses1 = SalaryDetailsToBonuses::join('salary_bonuses', 'salary_bonuses.salary_bonus_id', 'salary_details_to_bonuses.salary_bonus_id')
            ->where('salary_details_id', $id)->where('bonus_name', 'not like', '%PRO-RATA%')->pluck('amount_of_bonus')->first();

        $salaryDetailsToProrata = SalaryDetailsToBonuses::join('salary_bonuses', 'salary_bonuses.salary_bonus_id', 'salary_details_to_bonuses.salary_bonus_id')
            ->where('salary_details_id', $id)->where('bonus_name', 'like', '%PRO-RATA%')->pluck('amount_of_bonus')->first();

        $salaryDetailsToLeave = SalaryDetailsToLeave::select('salary_details_to_leave.*', 'leave_type.leave_type_name')
            ->join('leave_type', 'leave_type.leave_type_id', 'salary_details_to_leave.leave_type_id')
            ->where('salary_details_id', $id)->get();


        $monthAndYear = explode('-', $salaryDetails->month_of_salary);
        $start_year = $monthAndYear[0] . '-01';
        $end_year = $salaryDetails->month_of_salary;

        $financialYearTax = SalaryDetails::select(DB::raw('SUM(tax) as totalTax'))
            ->where('status', 1)
            ->where('employee_id', $salaryDetails->employee_id)
            ->whereBetween('month_of_salary', [$start_year, $end_year])
            ->first();

        return $data = [
            'salaryDetails' => $salaryDetails,
            'salaryDetailsToAllowance' => $salaryDetailsToAllowance,
            'salaryDetailsToDeduction' => $salaryDetailsToDeduction,
            'paySlipId' => $id,
            'financialYearTax' => $financialYearTax,
            'salaryDetailsToLeave' => $salaryDetailsToLeave,
            'printHeadSetting' => $printHeadSetting,
            'salaryDetailsToAdvances' => $salaryDetailsToAdvances,
            'salaryDetailsToBonuses' => $salaryDetailsToBonuses,
            'salaryDetailsToBonuses1' => $salaryDetailsToBonuses1,
            'salaryDetailsToProrata' => $salaryDetailsToProrata,
        ];
    }


    public function managementPaySlipDataFormat($id)
    {
        $printHeadSetting = PrintHeadSetting::first();
        $salaryDetails = SalaryDetails::select('salary_details.*', 'employee.employee_id', 'employee.department_id', 'employee.designation_id', 'department.department_name', 'designation.designation_name', 'employee.first_name', 'employee.last_name', 'job_categories.name', 'employee.date_of_joining')
            ->join('employee', 'employee.employee_id', 'salary_details.employee_id')
            ->join('department', 'department.department_id', 'employee.department_id')
            ->join('designation', 'designation.designation_id', 'employee.designation_id')
            ->join('job_categories', 'job_categories.id', 'employee.job_category')
            ->where('salary_details_id', $id)->first();

        $salaryDetailsToAllowance = SalaryDetailsToAllowance::join('allowance', 'allowance.allowance_id', 'salary_details_to_allowance.allowance_id')
            ->where('salary_details_id', $id)->get();

        $salaryDetailsToDeduction = SalaryDetailsToDeduction::join('deduction', 'deduction.deduction_id', 'salary_details_to_deduction.deduction_id')
            ->where('salary_details_id', $id)->get();

        $salaryDetailsToAdvances = SalaryDetailsToAdvances::join('salary_advances', 'salary_advances.salary_advance_id', 'salary_details_to_advances.salary_advance_id')
            ->where('salary_details_id', $id)->get();

        $salaryDetailsToBonuses = SalaryDetailsToBonuses::join('salary_bonuses', 'salary_bonuses.salary_bonus_id', 'salary_details_to_bonuses.salary_bonus_id')
            ->where('salary_details_id', $id)->get();

        $salaryDetailsToLeave = SalaryDetailsToLeave::select('salary_details_to_leave.*', 'leave_type.leave_type_name')
            ->join('leave_type', 'leave_type.leave_type_id', 'salary_details_to_leave.leave_type_id')
            ->where('salary_details_id', $id)->get();

        $monthAndYear = explode('-', $salaryDetails->month_of_salary);
        $start_year = $monthAndYear[0] . '-01';
        $end_year = $salaryDetails->month_of_salary;

        $financialYearTax = SalaryDetails::select(DB::raw('SUM(tax) as totalTax'))
            ->where('status', 1)
            ->where('employee_id', $salaryDetails->employee_id)
            ->whereBetween('month_of_salary', [$start_year, $end_year])
            ->first();

        return $data = [
            'salaryDetails' => $salaryDetails,
            'salaryDetailsToAllowance' => $salaryDetailsToAllowance,
            'salaryDetailsToDeduction' => $salaryDetailsToDeduction,
            'paySlipId' => $id,
            'financialYearTax' => $financialYearTax,
            'salaryDetailsToLeave' => $salaryDetailsToLeave,
            'printHeadSetting' => $printHeadSetting,
            'salaryDetailsToAdvances' => $salaryDetailsToAdvances,
            'salaryDetailsToBonuses' => $salaryDetailsToBonuses,
        ];
    }

    public function downloadPayslip($id)
    {
        $payslipId = $id;
        $ifHourly = SalaryDetails::with(['employee' => function ($q) {
            $q->with(['hourlySalaries', 'department', 'designation']);
        }])->where('salary_details_id', $payslipId)->first();


        if ($ifHourly->action == 'monthlySalary') {
            $result = $this->paySlipDataFormat($payslipId);
        } else {
            $printHeadSetting = PrintHeadSetting::first();
            $data = [
                'salaryDetails' => $ifHourly,
                'printHeadSetting' => $printHeadSetting,
            ];
            $pdf = Pdf::loadView('admin.payroll.salarySheet.hourlyPaySlipPdf', $data);
            $pdf->setPaper('A4', 'portrait');
            return $pdf->download("payslip.pdf");
        }

        $pdf = Pdf::loadView('admin.payroll.salarySheet.monthlyPaySlipPdf', $result);
        $pdf->setPaper('A4', 'portrait');
        return $pdf->download("payslip.pdf");
    }

    public function downloadMyPayroll()
    {
        $printHeadSetting = PrintHeadSetting::first();
        $results = SalaryDetails::with(['employee'])
            ->where('status', 1)
            ->where('employee_id', session('logged_session_data.employee_id'))
            ->orderBy('salary_details_id', 'DESC')
            ->get();

        $data = [
            'printHead' => $printHeadSetting,
            'results' => $results,
        ];

        $pdf = Pdf::loadView('admin.payroll.report.pdf.myPayrollPdf', $data);

        $pdf->setPaper('A4', 'landscape');
        return $pdf->download("my-payroll-Pdf.pdf");
    }


    public function paymentHistory(Request $request)
    {

        $results = '';
        if ($request->month) {
            $results = SalaryDetails::select(
                'salary_details.basic_salary',
                'salary_details.gross_salary',
                'salary_details.month_of_salary',
                DB::raw('CONCAT(COALESCE(employee.first_name,\'\'),\' \',COALESCE(employee.last_name,\'\')) AS fullName'),
                'employee.photo',
                'hourly_salaries.hourly_grade',
                'department.department_name'
            )
                ->join('employee', 'employee.employee_id', 'salary_details.employee_id')
                ->join('department', 'department.department_id', 'employee.department_id')
                ->leftJoin('hourly_salaries', 'hourly_salaries.hourly_salaries_id', 'employee.hourly_salaries_id')
                ->where('salary_details.status', 1)
                ->where('salary_details.month_of_salary', $request->month)
                ->orderBy('salary_details_id', 'DESC')
                ->get();
        }

        return view('admin.payroll.report.paymentHistory', ['results' => $results, 'month' => $request->month]);
    }



    public function viewGeneratePayroll()
    {
        return view('admin.payroll.payroll.generatePayroll.index');
    }

    public function massGenerate()
    {
        $staffSalaryDetails = Employee::where('status', 1)->get();

        return view('admin.payroll.payroll.generatePayroll.index', ['staffSalaryDetails' => $staffSalaryDetails]);
    }

    public function downloadFullPayroll()
    {
        $AllEmployeeDetails = [];
        $date = date('Y-m-15', strtotime('now'));
        $now = Carbon::now()->format('Y-m-d');
        if ($now > $date) {
            $month = date('Y-m', strtotime('now'));
        } else {
            $month = date('Y-m', strtotime('now - 1 month'));
        }

        $allSalaryDetails = SalaryDetails::where('month_of_salary', $month)->with('SalaryBonuses')->where('department_id', 16)->get();

        foreach ($allSalaryDetails as $allSalaries) {

            $id = $allSalaries->salary_details_id;
            $ifHourly = SalaryDetails::with(['employee' => function ($q) {
                $q->with(['hourlySalaries', 'department', 'designation']);
            }])->where('salary_details_id', $id)->first();

            if ($ifHourly->action == 'monthlySalary') {
                $result = $this->paySlipDataFormat($id);
            }


            $AllEmployeeDetails[] = $result;
        }

        $pdf = Pdf::loadView('admin.payroll.salarySheet.multiPayslipPdf', compact('AllEmployeeDetails'));
        $pdf->setPaper('A4', 'portrait');
        return $pdf->download("Full_Payroll_" . "$month" . ".pdf");
    }

    public function downloadMgntPayslips()
    {
        $user = auth()->user();
        if (!$user->hasAnyRole(['Technical Support', 'HR'])) {
            return view('errors.permissions_denied');
        }
        $AllEmployeeDetails = [];
        $date = date('Y-m-15', strtotime('now'));
        $now = Carbon::now()->format('Y-m-d');
        if ($now > $date) {
            $month = date('Y-m', strtotime('now'));
        } else {
            $month = date('Y-m', strtotime('now - 1 month'));
        }

        $allSalaryDetails = SalaryDetails::where('month_of_salary', $month)->where('department_id', 15)->get();

        foreach ($allSalaryDetails as $allSalaries) {
            $id = $allSalaries->salary_details_id;
            $ifHourly = SalaryDetails::with(['employee' => function ($q) {
                $q->with(['hourlySalaries', 'department', 'designation']);
            }])->where('salary_details_id', $id)->first();

            if ($ifHourly->action == 'monthlySalary') {
                $result = $this->paySlipDataFormat($id);
            }
            $AllEmployeeDetails[] = $result;
        }

        //  return view('admin.payroll.salarySheet.multiPayslipPdf', compact('AllEmployeeDetails'));
        $pdf = Pdf::loadView('admin.payroll.salarySheet.management.multiPayslipPdf', compact('AllEmployeeDetails'));
        $pdf->setPaper('A4', 'portrait');
        return $pdf->download("Management Full_PaySlips" . "$month" . ".pdf");
    }

    public function generatePayrollExcel()
    {
        $date = date('Y-m-15', strtotime('now'));
        $now = Carbon::now()->format('Y-m-d');
        if ($now > $date) {
            $month = date('Y-m', strtotime('now'));
        } else {
            $month = date('Y-m', strtotime('now - 1 month'));
        }
        $allSalaryDetails = SalaryDetails::where('month_of_salary', $month)->get();

        if ($allSalaryDetails->isEmpty()) {

            return redirect('generateSalarySheet')->with('error', 'No Salary Info Available for the current month.');
        } else {


            Excel::create('Full Payroll -' . $month, function ($excel) {
                $date = date('Y-m-15', strtotime('now'));
                $now = Carbon::now()->format('Y-m-d');
                if ($now > $date) {
                    $month = date('Y-m', strtotime('now'));
                } else {
                    $month = date('Y-m', strtotime('now - 1 month'));
                }
                $excel->sheet('Full Payroll' . $month, function ($sheet) {
                    $AllEmployeeDetails = [];
                    $date = date('Y-m-15', strtotime('now'));
                    $now = Carbon::now()->format('Y-m-d');
                    if ($now > $date) {
                        $month = date('Y-m', strtotime('now'));
                    } else {
                        $month = date('Y-m', strtotime('now - 1 month'));
                    }
                    $allSalaryDetails = SalaryDetails::where('month_of_salary', $month)->with('allowances')->where('department_id', '!=', 15)->get();

                    if ($allSalaryDetails->isEmpty()) {
                        return redirect('generateSalarySheet')->with('error', 'No Salary Info Available for.' . $month);
                    }

                    foreach ($allSalaryDetails as $allSalaries) {

                        $id = $allSalaries->salary_details_id;
                        $ifHourly = SalaryDetails::with(['employee' => function ($q) {
                            $q->with(['hourlySalaries', 'department', 'designation']);
                        }])->where('salary_details_id', $id)->first();

                        if ($ifHourly->action == 'monthlySalary') {
                            $result = $this->paySlipDataFormat($id);
                        }
                        $AllEmployeeDetails[] = $result;
                    }
                    foreach ($AllEmployeeDetails as $result1) {

                        $details[] = [
                            'Month' => $result1['salaryDetails']->month_of_salary,
                            'Payroll No' => $result1['salaryDetails']->payroll_no,
                            'Staff Name' => $result1['salaryDetails']->first_name . ' ' . $result1['salaryDetails']->last_name,
                            'Job Category' => $result1['salaryDetails']->name,
                            'Basic' => $result1['salaryDetails']->basic_salary,
                            'House A' => $result1['salaryDetails']->house_allowance,
                            'Transport A' => $result1['salaryDetails']->transport_allowance,
                            'Overtime' => $result1['salaryDetails']->total_overtime_amount,
                            'Bonuses' => $result1['salaryDetailsToBonuses1'],
                            'Airtime-non-taxable' => $result1['salaryDetails']->airtime_untaxed,
                            'Pro-rata' => $result1['salaryDetails']->pro_rata,
                            'Public Holiday' => $result1['salaryDetails']->public_holidays_pay,
                            'B/A' => $result1['salaryDetails']->banking_allowance,
                            'Gross' => $result1['salaryDetails']->gross_salary,
                            'Lost Days' => $result1['salaryDetails']->total_absence,
                            'Lost Days Amount' => $result1['salaryDetails']->total_absence_amount,
                            'Total Advance' => $result1['salaryDetails']->total_advances,
                            'NHIF' => $result1['salaryDetails']->nhifRate,
                            'NSSF' => $result1['salaryDetails']->nssf_amount,
                            'PAYE' => $result1['salaryDetails']->PAYE_tax,
                            'Net Pay' => $result1['salaryDetails']->net_salary,
                            'Sign' => '',
                        ];
                    }
                    $sheet->fromArray($details);
                });
            })->export('xls');
        }
    }


    public function deletePayrollDetails($id)
    {
        try {
            SalaryDetails::find($id)->delete();
            SalaryDetailsToAllowance::where('salary_details_id', '=', $id)->delete();
            SalaryDetailsToDeduction::where('salary_details_id', '=', $id)->delete();
            SalaryDetailsToAdvances::where('salary_details_id', '=', $id)->delete();
            SalaryDetailsToBonuses::where('salary_details_id', '=', $id)->delete();
            SalaryDetailsToLeave::where('salary_details_id', '=', $id)->delete();
            SalaryDetailsToAdvances::where('salary_details_id', '=', $id)->delete();

            $date = date('Y-m-25', strtotime('now'));
            $now = Carbon::now()->format('Y-m-d');

            if ($now > $date) {
                $month = date('Y-m', strtotime('now'));
            } else {
                $month = date('Y-m', strtotime('now - 1 month'));
            }

            return redirect()->back()->with(['success' => 'Salary record deleted successfully.', 'month' => $month]);
        } catch (\Exception $e) {
            \Log::info($e->getMessage());
            $bug = $e->getMessage();
            return redirect()->back()->with('error', 'An error occurred. Contact support for assistance');
        }
    }

    //New function for employee salary calculation

    public function newEmployeeSalaryCalculator(Request $request)
    {

        $Datetime0 = new DateTime($request->month);
        $Datetime0->modify('-1 months');
        $from_date = $Datetime0->format('Y-m-26');
        $to_date = date($request->month . '-25');

        $query1 = Attendance::whereBetween('date', [$from_date, $to_date])->where('employee_id', $request->employee_id)->get();
        $salaryResult = SalaryDetails::where('employee_id', $request->employee_id)->where('month_of_salary', $request->month)->count();
        if ($salaryResult > 0) {
            return redirect()->back()->with('error', 'Salary already generated for this month. Please delete the existing one before generating again');
        }


        $employeeList = $this->commonRepository->employeeList();
        $employeeDetails = Employee::with('department', 'designation', 'employeePayroll')->where('employee_id', $request->employee_id)->first();

        if ($employeeDetails->employeePayroll == null) {
            return redirect()->route('generateSalarySheet.create')->with('error', 'No employee payroll record found. Please set up employee payroll first.');
        }
        if ($employeeDetails->payroll_number == null) {
            return redirect()->route('generateSalarySheet.create')->with('error', 'Employee has no payroll number.');
        }

        $employeeAllInfo = [];
        $allowance = [];
        $deduction = [];
        $tax = 0;

        $from_date = $request->month . "-01";
        $to_date = date('Y-m-t', strtotime($from_date));

        $from_date = $request->month . '-26';
        $end_date = date("Y-m-25", strtotime($from_date));
        $firstOfCurrentMonth = date($request->month . '-01');
        $endOfCurrentMonth = date($request->month . "-t");

        if (strtotime($from_date) > strtotime($firstOfCurrentMonth)) {
            $from_date = new DateTime($from_date);
            $interval = new DateInterval('P1M');
            $from_date->sub($interval);
            $from_date = $from_date->format('Y-m-d');
        } else {
            $end_date = new DateTime($end_date);
            $interval = new DateInterval('P1M');
            $end_date->add($interval);
            $end_date = $end_date->format('Y-m-d');
            dd($from_date, $end_date);
        }

        $leaveRecord = LeaveApplication::select('leave_type.leave_type_id', 'leave_type_name', 'number_of_day', 'application_from_date', 'application_to_date')
            ->join('leave_type', 'leave_type.leave_type_id', 'leave_application.leave_type_id')
            ->where('leave_application.status', LeaveStatus::APPROVE)
            ->where('application_from_date', '>=', $from_date)
            ->where('application_to_date', '<=', $to_date)
            ->where('employee_id', $request->employee_id)
            ->get();

        $monthAndYear = explode('-', $request->month);
        $start_year = $monthAndYear[0] . '-01';
        $end_year = $monthAndYear[0] . '-12';


        //fiscal year calculation here
        $fiscal_start_year = null;
        $fiscal_year_end = null;
        $fiscal_start_date = null;
        $fiscal_end_date = null;
        if (date('m') > 6) {
            $fiscal_start_year = date('Y');
            $fiscal_start_date = date($fiscal_start_year . '-7-1');
            $fiscal_year_end = (date('Y') + 1);
            $fiscal_end_date = date($fiscal_year_end . '-6-30');
        } else {
            $fiscal_start_year = (date('Y') - 1);
            $fiscal_start_date = date($fiscal_start_year . '-7-1');
            $fiscal_year_end = date('Y');
            $fiscal_end_date = date('Y-6-30');
        }
        $fiscal_start_year = $fiscal_start_year . '-07';
        $fiscal_year_end = $fiscal_year_end . '-06';

        $financialYearTax = SalaryDetails::select(DB::raw('SUM(tax) as totalTax'))
            ->where('status', 1)
            ->where('employee_id', $request->employee_id)
            ->whereBetween('month_of_salary', [$fiscal_start_year, $fiscal_year_end])
            ->first();

        // Get employee payroll data
        $basicSalary = $employeeDetails->employeePayroll->basic_salary ?? 0;
        $grossSalary = $employeeDetails->employeePayroll->gross_salary ?? $basicSalary;
        $overtimeRate = $employeeDetails->employeePayroll->overtime_rate ?? 0;

        // Calculate allowances from employee earnings
        $totalAllowance = 0;
        if ($employeeDetails->payrollEarnings) {
            $totalAllowance = $employeeDetails->payrollEarnings->sum('amount');
        }
        $allowances = ['totalAllowance' => $totalAllowance];

        $salaryAdvances = $this->payrollRepository->calculateEmployeeAdvances($employeeDetails->employee_id, $request->month);
        $salaryBonuses = $this->payrollRepository->calculateEmployeeBonuses($employeeDetails->employee_id, $request->month);

        $employeeAllInfo = $this->payrollRepository->newGetEmployeeOtmAbsLvLtAndWokDays(
            $employeeDetails,
            $employeeDetails->employee_id,
            $request->month,
            $overtimeRate,
            $grossSalary,
            $basicSalary / 30, // daily rate
        );

        $totalDaysOfLeave = $employeeAllInfo['totalLeave'];
        $totalOverTimePay = $employeeAllInfo['totalOvertimeAmount'];
        $totalHolidaysWorkedPay = $basicSalary * $employeeAllInfo['totalHolidaysWorked'];

        $grossSalary1 = ($basicSalary + $totalHolidaysWorkedPay + $totalOverTimePay + $salaryBonuses['totalBonus'] + $allowances['totalAllowance']);
        $nssf_tier1 = 0;
        $nssf_tier2 = 0;
        $total_nssf = 0;

        $nssf_rates =  $this->payrollCalculations->calculateNSSF($grossSalary1, $employeeDetails->nssf_rate_type);
        $taxable_amount =  $this->payrollCalculations->calculateTAXABLE_AMOUNT($grossSalary1, $nssf_rates);
        $nhif = $this->payrollCalculations->calculateNHIF($grossSalary1);
        $statutoryDeduction = $nssf_rates['total_nssf'];
        $nhifRate = $nhif;
        $total_nssf = $nssf_rates['total_nssf'];
        //Gather here all the details -using the daily rates


        $sumTotalBonuses = 0;
        $prorataPay = 0;
        $airtime_untaxed = 0;
        foreach ($salaryBonuses['salaryBonusArray'] as $salaryBonus) {
            $sumTotalBonuses += $salaryBonus['salary_bonus_amount'];
        }

        $basicSalary1 = $employeeDetails->employeePayroll->basic_salary ?? 0;
        $affordableHousingLevy = (1.5 / 100) * $grossSalary1;
        $affordableHousingLevyEmployer = (1.5 / 100) * $grossSalary1;
        $affordableHousingLevyJulyAdjustment = 0;
        $gross_after_ahl = $grossSalary1 - $affordableHousingLevy;


        $taxableSalary1 = $grossSalary1 - ($total_nssf);
        $totalAdvances1 = 0;
        foreach ($salaryAdvances['salaryAdvanceArray'] as $salaryAdvance) {
            $totalAdvances1 += $salaryAdvance['salary_advance_amount'];
        }
        $SHIF_amount = $this->payrollRepository->calculateSHIF($grossSalary1);
        $ahl_relief =  $this->payrollCalculations->calculateAHLRelief($affordableHousingLevy);
        $formulae = PayrollFormula::where('country_id', $employeeDetails->country_id)->get();

        $evaluatedFormulae = [];
        foreach ($formulae as $formula) {
            $evaluatedFormulae[$formula->name] = $this->formulaEvaluator->evaluate($formula->formula, [
                'gross_salary' => $grossSalary1,
                'nssf' => $total_nssf,
            ]);
        }

        $tax = $evaluatedFormulae['PAYE'] ?? 0;
        $nhifRate = $evaluatedFormulae['SHIF'] ?? 0;
        $total_nssf = $evaluatedFormulae['NSSF'] ?? 0;
        $affordableHousingLevy = $evaluatedFormulae['AHL'] ?? 0;

        // Calculate total employee deductions from the EmployeeDeductions module
        $totalEmployeeDeductions = \App\Models\EmployeeDeductions::calculateTotalDeductionsForEmployee(
            $request->employee_id,
            Carbon::parse($request->month)->year,
            Carbon::parse($request->month)->month
        );

        $netSalary = $grossSalary1 - ($nhifRate + $tax + $affordableHousingLevy + $total_nssf + $salaryAdvances['totalDeduction'] + $totalEmployeeDeductions);


        $data = [
            'employeeList' => $employeeList,
            'allowances' => $allowances,
            'deductions' => $deduction,
            'tax' => $tax,
            'taxAbleSalary' => $taxable_amount,
            'employee_id' => $request->employee_id,
            'month' => $request->month,
            'employeeAllInfo' => $employeeAllInfo,
            'employeeDetails' => $employeeDetails,
            'leaveRecords' => $leaveRecord,
            'financialYearTax' => $financialYearTax,
            'employeeGrossSalary' => $grossSalary1,
            'statutoryDeduction' => $statutoryDeduction,
            'nssf_tier1' => $nssf_tier1,
            'nssf_tier2' => $nssf_tier1,
            'total_nssf' => $total_nssf,
            'nhifRate' => $nhifRate,
            'salaryAdvances' => $salaryAdvances,
            'salaryBonuses' => $salaryBonuses,
            'totalDaysOfLeave' => $totalDaysOfLeave,
            'taxableIncome2' => $taxable_amount,
            'basic_salary2' => $basicSalary1,
            'gross_salary2' => $grossSalary1,
            'housing_levy' => $affordableHousingLevy,
            'SHIF_amount' => $SHIF_amount,
            'gross_salary_after_ahl' => $gross_after_ahl,
            'net_salary1' => $netSalary,
        ];


        return view('admin.payroll.salarySheet.generateSalarySheet', $data);
    }
}
