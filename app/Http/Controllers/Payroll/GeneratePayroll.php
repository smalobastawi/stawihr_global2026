<?php
/*
 * Copyright (c) 2023/9/6 sw@stawitech
 */

namespace App\Http\Controllers\Payroll;

use App\Exports\ManagementPayrollDataExport;
use App\Exports\PayrollDataExport;
use App\Exports\WeeklyAttendance;
use App\Models\Advances;
use App\Models\Payroll\DeductionType;
use App\Models\JobCategory;
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
use App\Models\User;
use Cassandra\Date;
use Cassandra\Time;
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
use Maatwebsite\Excel\Facades\Excel;

use App\Models\Role;

use Illuminate\Foundation\Auth\User as Authenticatable;
use function Composer\Autoload\includeFile;

class GeneratePayroll extends Controller
{

    protected $commonRepository;
    protected $payrollRepository;

    public function __construct(CommonRepository $commonRepository, PayrollRepository $payrollRepository)
    {
        $this->commonRepository = $commonRepository;
        $this->payrollRepository = $payrollRepository;
    }

    public function payrollIndex(Request $request)
    {

        return view('admin.payroll.payroll.PayrollDashboard');
        $currentMonth = Date('Y-m');

        // $date = date('Y-m-25', strtotime('now'));
        // $now = Carbon::now()->format('Y-m-d');
        // if ($now > $date) {
        //     $currentMonth = date('Y-m', strtotime('now'));
        // } else {
        //     $currentMonth = date('Y-m', strtotime('now - 1 month'));
        // }
        $totalEmployee = Employee::where('status', 1)->count();
        $results = SalaryDetails::with(['employee' => function ($query) {
            $query->with(['department', 'jobCategory']);
        }])->orderBy('created_at', 'DESC')->paginate(60);

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

            $results = $results->paginate(100);

            return View('admin.payroll.payroll.pagination', compact('results'))->render();
        }
        $departmentList = $this->commonRepository->departmentList();

        $totalPayrollGenerated = SalaryDetails::where('month_of_salary', $currentMonth)->count();
        $totalNHIFGenerated = SalaryDetails::where('month_of_salary', $currentMonth)->where('nhifRate', "!=", "")->count();
        $totalNSSFGenerated = SalaryDetails::where('month_of_salary', $currentMonth)->where('total_nssf', "!=", "")->count();
        $totalAHLGenerated = SalaryDetails::where('month_of_salary', $currentMonth)->where('ahl_amount', "!=", "")->count();

        return view(
            'admin.payroll.payroll.PayrollIndex',
            ['results' => $results, 'departmentList' => $departmentList],
            compact(['totalPayrollGenerated', "totalNHIFGenerated", "totalNSSFGenerated", "totalAHLGenerated", "totalEmployee", "currentMonth"])
        );
    }

    public function managementPayIndex(Request $request)
    {
        $user = auth()->user();
        if (!$user->hasAnyRole(['Technical Support', 'HR'])) {
            return view('errors.permissions_denied');
        }
        $results = SalaryDetails::with(['employee' => function ($query) {
            $query->with(['department', 'jobCategory']);
        }])->where('department_id', 15)->orderBy('salary_details_id', 'DESC')->paginate(100);

        if (request()->ajax()) {

            $results = SalaryDetails::with(['employee' => function ($query) {
                $query->with(['department', 'jobCategory']);
            }])->where('department_id', 15)->orderBy('salary_details_id', 'DESC');

            if ($request->monthField != '') {
                $results->where('month_of_salary', $request->monthField);
            }

            if ($request->status != '') {
                $results->where('status', $request->status);
            }

            $results = $results->paginate(100);

            return View('admin.payroll.payroll.management.pagination', compact('results'))->render();
        }
        $departmentList = $this->commonRepository->departmentList();
        return view('admin.payroll.payroll.management.PayrollIndex', ['results' => $results, 'departmentList' => $departmentList]);
    }

    public function generatePayrollExcel(Request $request)
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

    public function payrollRequest(Request $request)
    {

        if ($request->month == '') {
            return redirect()->back()->with('error', 'Please select month of salary');
        }
        if ($request->request_type == 'Generate-excel') {
            $this->payrollRepository->generatePayrollExcel($request->month);
            return back();
        }
        if ($request->request_type == 'Generate-PDF') {
            return $this->payrollRepository->downloadFullPayrollPDF($request->month);
        }
    }

    public function managementPayrollRequest(Request $request)
    {
        $user = auth()->user();
        if (!$user->hasAnyRole(['Technical Support', 'HR'])) {
            return view('errors.permissions_denied');
        }
        if ($request->month == '') {
            return redirect()->back()->with('error', 'Please select month of salary');
        }
        if ($request->request_type == 'Generate-excel') {
            $this->payrollRepository->generateManagementPayrollExcel($request->month);
            return back();
        }
        if ($request->request_type == 'Generate-PDF') {
            return $this->payrollRepository->downloadMgntPayslips($request->month);
        }
    }

    public function newPayrollExport(Request $request)
    {
        $allSalaryDetails = SalaryDetails::where('month_of_salary', '2022-02')->with('employee')->with('jobCategory')->with('allowances')->where('department_id', '!=', 15)->get();

        return view('admin.payroll.payroll.view', ['results' => $allSalaryDetails]);
    }

    public function payrollDataExport(Request $request)
    {

        if ($request->request_type == 'Generate-excel') {
            $allSalaryDetails = SalaryDetails::where('month_of_salary', $request->month)->with(['employee' => function ($query) {
                $query->with(['department', 'jobCategory']);
            }])->with('jobCategory')->with('allowances')->with('SalaryBonuses')->where('department_id', '!=', 15)->get();


            return Excel::download(new PayrollDataExport($allSalaryDetails), 'payrollDataExport.xlsx');
        }
        if ($request->request_type == 'Generate-PDF') {
            return $this->payrollRepository->downloadFullPayrollPDF($request->month);
        }
    }

    public function managementPayrollDataExport(Request $request)
    {
        $user = auth()->user();
        if (!$user->hasAnyRole(['Technical Support', 'HR'])) {
            return view('errors.permissions_denied');
        }
        if ($request->month == '') {
            return redirect()->back()->with('error', 'Please select month of salary');
        }
        if ($request->request_type == 'Generate-excel') {
            $allSalaryDetails = SalaryDetails::where('month_of_salary', $request->month)->with(['employee' => function ($query) {
                $query->with(['department', 'jobCategory']);
            }])->with('jobCategory')->with('allowances')->with('SalaryBonuses')->where('department_id', '=', 15)->get();
            return Excel::download(new ManagementPayrollDataExport($allSalaryDetails), 'managementPayrollDataExport.xlsx');
        }
        if ($request->request_type == 'Generate-PDF') {
            return $this->payrollRepository->downloadMgntPayslips($request->month);
        }
    }
}
