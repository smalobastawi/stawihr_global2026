<?php
/*
 * Copyright (c) 2023/9/6 sw@stawitech
 */

namespace App\Repositories;

use App\Lib\Enumerations\LeaveStatus;
use App\Models\Advances;
use App\Models\Attendance;
use App\Models\Payroll\DeductionType;
use App\Models\EmployeeAttendanceApprove;
use App\Models\LeaveApplication;
use App\Models\PrintHeadSetting;
use App\Models\SalaryBonus;
use App\Models\SalaryDeductionForLateAttendance;
use App\Models\SalaryDetails;
use App\Models\SalaryDetailsToAdvances;
use App\Models\SalaryDetailsToAllowance;
use App\Models\SalaryDetailsToBonuses;
use App\Models\SalaryDetailsToDeduction;
use App\Models\SalaryDetailsToLeave;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\TaxRule;
use Carbon\Carbon;
use DateTime;
use DateInterval;
use Excel;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Arr;

class PayrollRepository
{

    protected $attendanceRepository,  $payrollCalculations, $salaryMonth1;

    public function __construct(AttendanceRepository $attendanceRepository, PayrollCalculations $payrollCalculations)
    {
        $this->attendanceRepository = $attendanceRepository;
        $this->salaryMonth1 = 0;
        $this->payrollCalculations = $payrollCalculations;
    }


    public function calculateEmployeeDeduction($basic_salary)
    {
        //$deductions     = $this->pay_grade_to_deduction($pay_grade_id);
        $deductions = DeductionType::all();
        $deductionArray = [];
        $totalDeduction = 0;
        foreach ($deductions as $key => $deduction) {
            $temp = [];
            $temp['deduction_id'] = $deduction->deduction_id;
            $temp['deduction_name'] = $deduction->deduction_name;
            $temp['deduction_type'] = $deduction->deduction_type;
            $temp['percentage_of_basic'] = $deduction->percentage_of_basic;
            $temp['limit_per_month'] = $deduction->limit_per_month;

            if ($deduction->deduction_type == 'Percentage') {
                $percentageOfDeduction = ($basic_salary * $deduction->percentage_of_basic) / 100;
                if ($deduction->limit_per_month != 0 && $percentageOfDeduction >= $deduction->limit_per_month) {
                    $temp['amount_of_deduction'] = $deduction->limit_per_month;
                } else {
                    $temp['amount_of_deduction'] = $percentageOfDeduction;
                }
            } else {
                $temp['amount_of_deduction'] = $deduction->limit_per_month;
            }
            $totalDeduction += $temp['amount_of_deduction'];
            $deductionArray[$key] = $temp;
        }
        return ['deductionArray' => $deductionArray, 'totalDeduction' => $totalDeduction];
    }

    /**
     *
     * @employee tax calculation
     *
     *
     */

    public function calculateEmployeeAdvances($employee_id, $month)
    {
        // First try to find by payroll period
        $payrollPeriod = $this->getPayrollPeriodForMonth($month);

        if ($payrollPeriod) {
            // Use payroll period for advances lookup
            $salaryAdvance = Advances::where('employee_id', $employee_id)
                ->where('recovery_period_id', $payrollPeriod->id)
                ->get();
        } else {
            // Fallback to legacy month-based lookup
            $month = date('Y-m-1', strtotime($month));
            $salaryAdvance = Advances::where('employee_id', $employee_id)
                ->where('month', $month)
                ->get();
        }

        $deductionArray = [];
        $totalDeduction = 0;
        foreach ($salaryAdvance as $key => $salaryAdvances) {
            $temp = [];
            $temp['salary_advance_id'] = $salaryAdvances->id;
            $temp['salary_advance_name'] = $salaryAdvances->name ?? 'Salary Advance';
            $temp['employee_id'] = $salaryAdvances->employee_id;
            $temp['salary_advance_month'] = $salaryAdvances->month ?? ($payrollPeriod ? $payrollPeriod->name : $month);
            $temp['salary_advance_amount'] = $salaryAdvances->amount;
            $temp['payroll_period_id'] = $salaryAdvances->payroll_period_id;
            $temp['recovery_period_id'] = $salaryAdvances->recovery_period_id;

            $totalDeduction += $temp['salary_advance_amount'];
            $deductionArray[$key] = $temp;
        }
        return ['salaryAdvanceArray' => $deductionArray, 'totalSalaryAdvances' => $totalDeduction, 'totalDeduction' => $totalDeduction];
    }

    public function calculateEmployeeBonuses($employee_id, $month)
    {
        $salaryBonus = SalaryBonus::where('employee_id', $employee_id)->where('month', $month)->get();

        $bonusArray = [];
        $totalBonus = 0;
        $airtimeBonus = 0;
        foreach ($salaryBonus as $key => $salaryBonuses) {
            if ($salaryBonuses['name'] == 'AIRTIME') {
                $airtimeBonus = $salaryBonuses->amount;
            }
            $temp = [];
            $temp['salary_bonus_id'] = $salaryBonuses->salary_bonus_id;
            $temp['salary_bonus_name'] = $salaryBonuses->name;
            $temp['employee_id'] = $salaryBonuses->employee_id;
            $temp['salary_bonus_month'] = $salaryBonuses->month;
            $temp['salary_bonus_amount'] = $salaryBonuses->amount;
            $totalBonus += $temp['salary_bonus_amount'];
            $bonusArray[$key] = $temp;
        }

        return ['salaryBonusArray' => $bonusArray, 'totalBonus' => $totalBonus, 'airtimeBonus' => $airtimeBonus];
    }

    public function calculateEmployeeTax($date_of_birth, $gender, $taxableSalary, $nhifRate, $taxYear)
    {

        $birthday = $this->getEmployeeAge($date_of_birth);
        $tax = 0;
        $taxableIncome = $taxableSalary;

        $totalTax = 0;
        $personalRelief = 2400;
        $insuranceRelief  = 0.15 * $nhifRate;
        if ($taxYear == 2022) {
            $insuranceRelief = 0.15 * $nhifRate;
        }
        if ($gender == 'Female') {
            $taxRule = TaxRule::where('gender', 'Female')->get();
            $band3_top = TaxRule::where('gender', 'Female')->max('amount');
        } else {
            $taxRule = TaxRule::where('gender', 'Male')->get();
            $band3_top = TaxRule::where('gender', 'Male')->max('amount');
        }
        //the tops of each tax band
        $band1_top = $newband = TaxRule::where('amount', '>', 0)->min('amount');

        //2021 Band here
        //        $2021Band1 = 1- 24000 ; //10 %/ above
        //        $2021Band1 = 24001 - 32333; // 25% above
        //        $2021Band1 = 32333; // 30% above

        //Previous covid-19 relief rates disabled
        //        $band1_top = 24000; //
        //        $band2_top = 40667;
        //        $band3_top = 57334;

        $band1_top = 24000; //
        $band2_top = 32333;
        $band3_top = 500000;
        $band4_top = 800000;
        $band5_above = 800000;
        //no top of band 4

        //the tax rates of each band

        // Monthly Taxable Pay bands
        $bands2023 = array(
            array('min' => 0, 'max' => 24000, 'rate' => 10.0),
            array('min' => 24001, 'max' => 32333, 'rate' => 25.0),
            array('min' => 32334, 'max' => 500000, 'rate' => 30.0),
            array('min' => 500001, 'max' => 800000, 'rate' => 32.5),
            array('min' => 800001, 'max' => PHP_INT_MAX, 'rate' => 35.0)
        );



        $starting_income = $income = $taxableIncome; //set this to your income
        $band1 = $band2 = $band3 = $band4 = 0;

        if ($income > $band3_top) {
            $bandRate = TaxRule::max('percentage_of_tax') / 100;

            $band4 = ($income - $band3_top) * $bandRate;
            $income = $band3_top;
        }

        if ($income > $band2_top) {
            $bandRate = TaxRule::where('amount', '>', $band2_top)->where('amount', '>=', $income)->max('percentage_of_tax') / 100;

            $band3 = ($income - $band2_top) * $bandRate;
            $income = $band2_top;
        }

        if ($income > $band1_top) {
            $bandRate = TaxRule::where('amount', '>', $band1_top)->where('amount', '>=', $income)->min('percentage_of_tax') / 100;
            $band2 = ($income - $band1_top) * $bandRate;
            $income = $band1_top;
        }
        $bandRate = TaxRule::where('amount', $income)->max('percentage_of_tax') / 100;
        $band1 = $income * $bandRate;


        $total_tax_due = $band1 + $band2 + $band3 + $band4;
        if ($total_tax_due < ($personalRelief + $insuranceRelief)) {
            $total_tax_due = 0;
        } else {
            $total_tax_due = $total_tax_due - ($personalRelief + $insuranceRelief);
        }
        $data = [
            'monthlyTax' => $total_tax_due,
            'taxAbleIncome' => $taxableIncome,
        ];

        return $data;
    }

    public function calculateEmployeeTax2023($date_of_birth, $gender, $taxableSalary, $nhifRate, $taxYear)
    {

        $birthday = $this->getEmployeeAge($date_of_birth);
        $tax = 0;

        $totalTax = 0;
        $personalRelief = 2400;
        $insuranceRelief  = 0.15 * $nhifRate;
        if ($taxYear == 2022) {
            $insuranceRelief = 0.15 * $nhifRate;
        }
        if ($gender == 'Female') {
            $taxRule = TaxRule::where('gender', 'Female')->get();
            $band3_top = TaxRule::where('gender', 'Female')->max('amount');
        } else {
            $taxRule = TaxRule::where('gender', 'Male')->get();
            $band3_top = TaxRule::where('gender', 'Male')->max('amount');
        }
        //the tops of each tax band
        $band1_top = $newband = TaxRule::where('amount', '>', 0)->min('amount');

        //2021 Band here
        //        $2021Band1 = 1- 24000 ; //10 %/ above
        //        $2021Band1 = 24001 - 32333; // 25% above
        //        $2021Band1 = 32333; // 30% above

        //Previous covid-19 relief rates disabled
        //        $band1_top = 24000; //
        //        $band2_top = 40667;
        //        $band3_top = 57334;

        $band1_top = 24000; //
        $band2_top = 32333;
        $band3_top = 500000;
        $band4_top = 800000;
        //no top of band 4

        //the tax rates of each band

        // Monthly Taxable Pay bands
        $bands2023 = array(
            array('min' => 0, 'max' => 24000, 'rate' => 10.0),
            array('min' => 24001, 'max' => 32333, 'rate' => 25.0),
            array('min' => 32334, 'max' => 500000, 'rate' => 30.0),
            array('min' => 500001, 'max' => 800000, 'rate' => 32.5),
            array('min' => 800001, 'max' => PHP_INT_MAX, 'rate' => 35.0)
        );

        $starting_income = $income = $taxableSalary; //set this to your income
        $band1 = $band2 = $band3 = $band4  = $band5  = 0;

        if ($income > $band4_top) {
            $bandRate = 35 / 100;
            $band5 = ($income - $band4_top) * $bandRate;
            $income = $band4_top;
        }

        if ($income > $band3_top) {
            $bandRate = 32.5 / 100;
            $band4 = ($income - $band3_top) * $bandRate;
            $income = $band3_top;
        }

        if ($income > $band2_top) {
            //$bandRate = TaxRule::where('amount', '>', $band2_top)->where('amount', '>=', $income)->max('percentage_of_tax') / 100;
            $bandRate = 30 / 100;

            $band3 = ($income - $band2_top) * $bandRate;
            $income = $band2_top;
        }

        if ($income > $band1_top) {
            //$bandRate = TaxRule::where('amount', '>', $band1_top)->where('amount', '>=', $income)->min('percentage_of_tax') / 100;
            $bandRate = 25 / 100;
            $band2 = ($income - $band1_top) * $bandRate;
            $income = $band1_top;
        }
        $bandRate = 10 / 100;
        $band1 = $income * $bandRate;

        $total_tax_due = $band1 + $band2 + $band3 + $band4 + $band5;
        if ($total_tax_due < ($personalRelief + $insuranceRelief)) {
            $total_tax_due = 0;
        } else {
            $total_tax_due = $total_tax_due - ($personalRelief + $insuranceRelief);
        }
        $data = [
            'monthlyTax' => $total_tax_due,
            'taxAbleIncome' => $taxableSalary,
        ];

        return $data;
    }

    public function calculateManagementTax($gross_salary_for_the_month, $date_of_birth, $gender, $jobCategory, $total_nssf, $taxableSalary, $nhifRate, $taxYear)
    {

        $birthday = $this->getEmployeeAge($date_of_birth);
        $tax = 0;
        $taxableIncome = $taxableSalary;

        $totalTax = 0;
        $personalRelief = 2400;
        $insuranceRelief = 0.15 * $nhifRate;
        if ($taxYear == 2022) {
            $insuranceRelief = 0.15 * $nhifRate;
        }
        if ($gender == 'Female') {
            $taxRule = TaxRule::where('gender', 'Female')->get();
            $band3_top = TaxRule::where('gender', 'Female')->max('amount');
        } else {
            $taxRule = TaxRule::where('gender', 'Male')->get();
            $band3_top = TaxRule::where('gender', 'Male')->max('amount');
        }
        //the tops of each tax band
        $band1_top = $newband = TaxRule::where('amount', '>', 0)->min('amount');

        //2021 Band here
        //        $2021Band1 = 1- 24000 ; //10 %/ above
        //        $2021Band1 = 24001 - 32333; // 25% above
        //        $2021Band1 = 32333; // 30% above

        //Previous covid-19 relief rates disabled
        //        $band1_top = 24000; //
        //        $band2_top = 40667;
        //        $band3_top = 57334;

        $band1_top = 24000; //
        $band2_top = 32333;
        $band3_top = 32333;
        //no top of band 4

        //the tax rates of each band


        $starting_income = $income = $taxableIncome; //set this to your income
        $band1 = $band2 = $band3 = $band4 = 0;

        if ($income > $band3_top) {
            $bandRate = TaxRule::max('percentage_of_tax') / 100;
            $band4 = ($income - $band3_top) * $bandRate;
            $income = $band3_top;
        }
        if ($income > $band2_top) {
            $bandRate = TaxRule::where('amount', '>', $band2_top)->where('amount', '>=', $income)->max('percentage_of_tax') / 100;
            $band3 = ($income - $band2_top) * $bandRate;
            $income = $band2_top;
        }

        if ($income > $band1_top) {
            $bandRate = TaxRule::where('amount', '>', $band1_top)->where('amount', '>=', $income)->min('percentage_of_tax') / 100;
            $band2 = ($income - $band1_top) * $bandRate;
            $income = $band1_top;
        }
        $bandRate = TaxRule::where('amount', $income)->max('percentage_of_tax') / 100;
        $band1 = $income * $bandRate;


        $total_tax_due = $band1 + $band2 + $band3 + $band4;

        if ($total_tax_due < ($personalRelief + $insuranceRelief)) {
            $total_tax_due = 0;
        } else {
            $total_tax_due = $total_tax_due - ($personalRelief + $insuranceRelief);
        }
        $data = [
            'monthlyTax' => $total_tax_due,
            'taxAbleIncome' => $taxableIncome,
        ];

        return $data;
    }

    public function getEmployeeAge($date_of_birth)
    {

        $birthday = new DateTime($date_of_birth);
        $currentDate = new DateTime('now');
        $interval = $birthday->diff($currentDate);
        return $interval->y;
    }


    /**
     *
     * @employee total working days
     * @employee total leave
     * @employee total late             @@ getEmployeeOtmAbsLvLtAndWokDays()
     * @employee total late amount
     * @employee total over time
     * @employee total present
     *
     */

    public function getEmployeeOtmAbsLvLtAndWokDays($employeeDetails, $employee_id, $month, $overtime_rate, $monthly_gross_salary, $daily_gross_pay)
    {

        $getDate = $this->getMonthToStartDateAndEndDate($month);

        $queryResult = $this->attendanceRepository->getEmployeeMonthlyAttendance($getDate['firstDate'], $getDate['lastDate'], $employee_id);


        //        if ($queryResult == []) {
        //            abort(404, 'Employee records for the month not found');
        //        }

        //Calculate total number of days oin the month.
        $datetime1 = new DateTime($getDate['firstDate']);
        $datetime2 = new DateTime($getDate['lastDate']);
        $interval = $datetime1->diff($datetime2);
        $totalDaysInTheMonth = $interval->format('%a');

        // return $queryResult;
        $overTime = [];
        $totalPresent = 0;
        $totalAbsence = 0;
        $totalLeave = 0;
        $totalLate = 0;
        $totalLateAmount = 0;
        $totalAbsenceAmount = 0;
        $totalWorkingDays = count($queryResult);
        $totalHolidaysWorked = 0;

        foreach ($queryResult as $value) {

            if ($value['action'] == 'Absence') {
                $totalAbsence += 1;
            } elseif ($value['action'] == 'Leave') {
                $totalLeave += 1;
            } else {
                $totalPresent += 1;
            }

            if ($value['ifLate'] == 'Yes') {
                $totalLate += 1;
            }

            $workingHour = new DateTime($value['workingHours']);
            $workingTime = new DateTime($value['working_time']);
            if ($workingHour < $workingTime) {
                $interval = $workingHour->diff($workingTime);
                $overTime[] = $interval->format('%H:%I');
            }
            $totalHolidaysWorked = $value['holidaysWorked'];
        }

        /**
         * @employee Salary Deduction For Late Attendance
         */
        $salaryDeduction = SalaryDeductionForLateAttendance::where('status', 'Active')->first();
        $dayOfSalaryDeduction = 0;
        $oneDaysSalary = 0;
        if ($monthly_gross_salary != 0 && $totalWorkingDays != 0 && $totalLate != 0 && !empty($salaryDeduction)) {
            $numberOfDays = 0;
            for ($i = 1; $i <= $totalLate; $i++) {
                $numberOfDays++;
                if ($numberOfDays == $salaryDeduction->for_days) {
                    $dayOfSalaryDeduction += 1;
                    $numberOfDays = 0;
                }
            }

            $oneDaysSalary = $daily_gross_pay;
            $totalLateAmount = $oneDaysSalary * $dayOfSalaryDeduction;
        }

        /**
         * @employee Salary Deduction For absence
         */

        if ($totalAbsence != 0 && $monthly_gross_salary != 0 && $totalWorkingDays != 0) {
            $totalAbsenceAmount = $daily_gross_pay * $totalAbsence;
        }


        $oneDaySalary = $daily_gross_pay;
        $overTime = $this->calculateEmployeeTotalOverTime($overTime, $overtime_rate);

        $data = [
            'overtime_rate' => $overtime_rate,
            'totalOverTimeHour' => $overTime['totalOverTimeHour'],
            'totalOvertimeAmount' => $overTime['overtimeAmount'],
            'totalPresent' => $totalPresent,
            'totalAbsence' => $totalAbsence,
            'totalAbsenceAmount' => round($totalAbsenceAmount),
            'totalLeave' => $totalLeave,
            'totalLate' => $totalLate,
            'dayOfSalaryDeduction' => $dayOfSalaryDeduction,
            'totalLateAmount' => round($totalLateAmount),
            'totalWorkingDays' => $totalWorkingDays,
            'oneDaysSalary' => $oneDaySalary,
            'totalHolidaysWorked' => $totalHolidaysWorked,
            'totalDaysInTheMonth' => $totalDaysInTheMonth,
        ];

        return $data;
    }


    //for management


    public function getManagementOtmAbsLvLtAndWokDays($employeeDetails, $employee_id, $month, $overtime_rate, $monthly_gross_salary, $daily_gross_pay)
    {

        $getDate = $this->getMonthToStartDateAndEndDate($month);
        $queryResult = $this->attendanceRepository->getEmployeeMonthlyAttendance($getDate['firstDate'], $getDate['lastDate'], $employee_id);

        if ($employeeDetails->date_of_joining > $getDate['firstDate']) {
            $getDate['firstDate'] = $employeeDetails->date_of_joining;
        }
        if ($employeeDetails->date_of_leaving != '') {
            if ($employeeDetails->date_of_leaving < $getDate['lastDate']) {
                $getDate['lastDate'] = $employeeDetails->date_of_leaving;
            }
        }

        // $queryResult = $this->attendanceRepository->getEmployeeMonthlyAttendance($getDate['firstDate'], $getDate['lastDate'], $employee_id);

        if ($queryResult == []) {
            abort(404, 'Employee records for the month not found');
        }

        //Calculate total number of days oin the month.
        $datetime1 = new DateTime($getDate['firstDate']);
        $datetime2 = new DateTime($getDate['lastDate']);
        $interval = $datetime1->diff($datetime2);
        $totalDaysInTheMonth = $interval->format('%a');


        $overTime = [];
        $totalPresent = 0;
        $totalAbsence = 0;
        $totalLeave = 0;
        $totalLate = 0;
        $totalLateAmount = 0;
        $totalAbsenceAmount = 0;
        $totalWorkingDays = count($queryResult);
        $totalHolidaysWorked = 0;
        foreach ($queryResult as $value) {
            if ($value['action'] == 'Absence') {
                $totalAbsence += 1;

                // dd($queryResult);
            } elseif ($value['action'] == 'Leave') {
                $totalLeave += 1;
            } else {
                $totalPresent += 1;
            }

            if ($value['ifLate'] == 'Yes') {
                $totalLate += 1;
            }

            $workingHour = new DateTime($value['workingHours']);
            $workingTime = new DateTime($value['working_time']);

            if ($workingHour < $workingTime) {
                $interval = $workingHour->diff($workingTime);
                $overTime[] = $interval->format('%H:%I');
            }
            $totalHolidaysWorked = $value['holidaysWorked'];
        }

        /**
         * @employee Salary Deduction For Late Attendance
         */
        $salaryDeduction = SalaryDeductionForLateAttendance::where('status', 'Active')->first();
        $dayOfSalaryDeduction = 0;
        $oneDaysSalary = 0;
        if ($monthly_gross_salary != 0 && $totalWorkingDays != 0 && $totalLate != 0 && !empty($salaryDeduction)) {
            $numberOfDays = 0;
            for ($i = 1; $i <= $totalLate; $i++) {
                $numberOfDays++;
                if ($numberOfDays == $salaryDeduction->for_days) {
                    $dayOfSalaryDeduction += 1;
                    $numberOfDays = 0;
                }
            }

            $oneDaysSalary = $daily_gross_pay;
            $totalLateAmount = $oneDaysSalary * $dayOfSalaryDeduction;
        }

        /**
         * @employee Salary Deduction For absence
         */

        if ($totalAbsence != 0 && $monthly_gross_salary != 0 && $totalWorkingDays != 0) {
            $perDaySalary = $daily_gross_pay;
            $totalAbsenceAmount = $perDaySalary * $totalAbsence;
        }

        $oneDaySalary = $daily_gross_pay;
        $overTime = $this->calculateEmployeeTotalOverTime($overTime, $overtime_rate);

        $data = [
            'overtime_rate' => $overtime_rate,
            'totalOverTimeHour' => $overTime['totalOverTimeHour'],
            'totalOvertimeAmount' => $overTime['overtimeAmount'],
            'totalPresent' => $totalPresent,
            'totalAbsence' => $totalAbsence,
            'totalAbsenceAmount' => round($totalAbsenceAmount),
            'totalLeave' => $totalLeave,
            'totalLate' => $totalLate,
            'dayOfSalaryDeduction' => $dayOfSalaryDeduction,
            'totalLateAmount' => round($totalLateAmount),
            'totalWorkingDays' => $totalWorkingDays,
            'oneDaysSalary' => $oneDaySalary,
            'totalHolidaysWorked' => $totalHolidaysWorked,
            'totalDaysInTheMonth' => $totalDaysInTheMonth,
        ];

        return $data;
    }

    public function newGetManagementOtmAbsLvLtAndWokDays($employeeDetails, $employee_id, $month, $overtime_rate, $monthly_gross_salary, $daily_gross_pay)
    {

        $getDate = $this->getMonthToStartDateAndEndDate($month);
        // $queryResult = $this->attendanceRepository->getEmployeeMonthlyAttendance($getDate['firstDate'], $getDate['lastDate'], $employee_id);

        $startingDate = $getDate['firstDate'];
        $endingDate = $getDate['lastDate'];
        $attendanceData11 = Attendance::where('employee_id', $employee_id)->whereBetween('date', [$startingDate, $endingDate])->get();

        if ($employeeDetails->date_of_joining > $getDate['firstDate']) {
            $getDate['firstDate'] = $employeeDetails->date_of_joining;
        }
        if ($employeeDetails->date_of_leaving != '') {
            if ($employeeDetails->date_of_leaving < $getDate['lastDate']) {
                $getDate['lastDate'] = $employeeDetails->date_of_leaving;
            }
        }

        // $queryResult = $this->attendanceRepository->getEmployeeMonthlyAttendance($getDate['firstDate'], $getDate['lastDate'], $employee_id);

        if ($attendanceData11 == []) {
            abort(404, 'Employee records for the month not found');
        }
        $overTime = [];
        $totalPresent = 0;
        $totalAbsence = 0;
        $totalLeave = 0;
        $totalLate = 0;
        $totalLateAmount = 0;
        $totalAbsenceAmount = 0;
        $totalWorkingDays = count($attendanceData11);
        $totalHolidaysWorked = 0;
        foreach ($attendanceData11 as $value) {

            if ($value['presence_status'] == 'ABSENT') {
                $totalAbsence += 1;
            } elseif ($value['presence_status'] == 'PRESENT') {
                $totalPresent += 1;
            } else {
                $totalLeave += 1;
            }

            if ($value['is_late'] == 'Yes') {
                $totalLate += 1;
            }

            if ($value->time_out != null) {
                $hours = 9 * 60 * 60;
                $clockOut = \Carbon\Carbon::parse($value->time_out);
                $clockIn = \Carbon\Carbon::parse($value->time_in);
                $totalDuration1 = $clockOut->diffInSeconds($clockIn);

                if ($totalDuration1 > $hours) {
                    $interval = $totalDuration1 - $hours;

                    if ($interval > 3600) {
                        $overTime[] = gmdate('H:i', $interval);
                    } else {
                        $overTime[] = gmdate('H:i', 0);
                    }
                }
            }


            $totalHolidaysWorked = $value['holidaysWorked'];
        }

        /**
         * @employee Salary Deduction For Late Attendance
         */
        $salaryDeduction = SalaryDeductionForLateAttendance::where('status', 'Active')->first();
        $dayOfSalaryDeduction = 0;
        $oneDaysSalary = 0;
        if ($monthly_gross_salary != 0 && $totalWorkingDays != 0 && $totalLate != 0 && !empty($salaryDeduction)) {
            $numberOfDays = 0;
            for ($i = 1; $i <= $totalLate; $i++) {
                $numberOfDays++;
                if ($numberOfDays == $salaryDeduction->for_days) {
                    $dayOfSalaryDeduction += 1;
                    $numberOfDays = 0;
                }
            }

            $oneDaysSalary = $daily_gross_pay;
            $totalLateAmount = $oneDaysSalary * $dayOfSalaryDeduction;
        }

        /**
         * @employee Salary Deduction For absence
         */

        if ($totalAbsence != 0 && $monthly_gross_salary != 0 && $totalWorkingDays != 0) {
            $perDaySalary = $daily_gross_pay;
            $totalAbsenceAmount = $perDaySalary * $totalAbsence;
        }

        $oneDaySalary = $daily_gross_pay;
        $overTime = $this->calculateEmployeeTotalOverTime($overTime, $overtime_rate);

        $data = [
            'overtime_rate' => $overtime_rate,
            'totalOverTimeHour' => $overTime['totalOverTimeHour'],
            'totalOvertimeAmount' => $overTime['overtimeAmount'],
            'totalPresent' => $totalPresent,
            'totalAbsence' => $totalAbsence,
            'totalAbsenceAmount' => round($totalAbsenceAmount),
            'totalLeave' => $totalLeave,
            'totalLate' => $totalLate,
            'dayOfSalaryDeduction' => $dayOfSalaryDeduction,
            'totalLateAmount' => round($totalLateAmount),
            'totalWorkingDays' => $totalWorkingDays,
            'oneDaysSalary' => $oneDaySalary,
            'totalHolidaysWorked' => $totalHolidaysWorked,
        ];

        return $data;
    }

    public function calculateEmployeeTotalOverTime($overTime, $overtime_rate)
    {

        $totalMinute = 0;
        $minuteWiseAmount = 0;
        $hour = 0;
        $minutes = 0;
        foreach ($overTime as $key => $value) {

            $value = explode(':', $value);
            $hour += $value[0];
            $minutes += $value[1];
            if ($minutes >= 60) {
                $minutes -= 60;
                $hour++;
            }
        }
        $hours = $hour . ':' . (($minutes < 10) ? '0' . $minutes : $minutes);
        $value = explode(':', $hours);
        $totalMinute = $value[1];
        if ($totalMinute != 0 && $overtime_rate != 0) {

            $perMinuteAmount = $overtime_rate / 60;
            $minuteWiseAmount = $perMinuteAmount * $totalMinute;
        }
        $overtimeAmount = ($value[0] * $overtime_rate) + $minuteWiseAmount;


        return ['totalOverTimeHour' => $hours, 'overtimeAmount' => round($overtimeAmount)];
    }


    public function getMonthToStartDateAndEndDate($month)
    {
        // First try to get payroll period from system settings
        $payrollPeriod = $this->getPayrollPeriodForMonth($month);

        if ($payrollPeriod) {
            return [
                'firstDate' => $payrollPeriod->start_date->format('Y-m-d'),
                'lastDate' => $payrollPeriod->end_date->format('Y-m-d')
            ];
        }

        // Fallback to legacy hardcoded logic if no payroll period found
        $from_date = $month . '-26';
        $end_date = date("Y-m-25", strtotime($from_date));
        $firstOfCurrentMonth = date($month . '-01');
        $endOfCurrentMonth = date($month . "-t");

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
        }

        $month = explode('-', $month);
        $current_year = $month[0];
        $lastMonth = $month[1];

        $firstDate = $current_year . "-" . $lastMonth . "-01";
        $lastDateOfMonth = date('t', strtotime($firstDate));
        $lastDate = $current_year . "-" . $lastMonth . "-" . $lastDateOfMonth;

        // Changed the first and last date to reflect the Monthly periods
        $firstDate = $from_date;
        $lastDate = $end_date;

        return ['firstDate' => $firstDate, 'lastDate' => $lastDate];
    }

    /**
     * Get payroll period that matches the given month
     *
     * @param string $month Format: YYYY-MM
     * @return \App\Models\Payroll\PayrollPeriod|null
     */
    private function getPayrollPeriodForMonth($month)
    {
        try {
            $targetDate = Carbon::createFromFormat('Y-m', $month)->startOfMonth();

            // First try to find a period where the target month falls within the period
            $payrollPeriod = \App\Models\Payroll\PayrollPeriod::where(function ($query) use ($targetDate) {
                $query->where('start_date', '<=', $targetDate->endOfMonth())
                    ->where('end_date', '>=', $targetDate->startOfMonth());
            })
                ->where('period_type', \App\Models\Payroll\PayrollPeriod::PERIOD_MONTHLY)
                ->orderBy('start_date', 'desc')
                ->first();

            if ($payrollPeriod) {
                return $payrollPeriod;
            }

            // If no exact match, try to find the current period and use its pattern
            $currentPeriod = \App\Models\Payroll\PayrollPeriod::getCurrentPeriod();
            if ($currentPeriod && $currentPeriod->period_type === \App\Models\Payroll\PayrollPeriod::PERIOD_MONTHLY) {
                // Calculate period dates based on current period pattern
                return $this->calculatePeriodFromPattern($currentPeriod, $month);
            }

            return null;
        } catch (\Exception $e) {
            // Log error and return null to fallback to legacy logic
            Log::error('Error getting payroll period for month: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Calculate payroll period dates based on existing period pattern
     *
     * @param \App\Models\Payroll\PayrollPeriod $referencePeriod
     * @param string $targetMonth
     * @return object|null
     */
    private function calculatePeriodFromPattern($referencePeriod, $targetMonth)
    {
        try {
            $targetDate = Carbon::createFromFormat('Y-m', $targetMonth);
            $referenceStart = $referencePeriod->start_date;
            $referenceEnd = $referencePeriod->end_date;

            // Calculate the day of month for start and end
            $startDay = $referenceStart->day;
            $endDay = $referenceEnd->day;

            // Determine if the period crosses month boundary
            $crossesMonth = $referenceEnd->month !== $referenceStart->month ||
                $referenceEnd->year !== $referenceStart->year;

            if ($crossesMonth) {
                // Period crosses month (e.g., 26th to 25th of next month)
                $startDate = $targetDate->copy()->subMonth()->day($startDay);
                $endDate = $targetDate->copy()->day($endDay);
            } else {
                // Period within same month
                $startDate = $targetDate->copy()->day($startDay);
                $endDate = $targetDate->copy()->day($endDay);
            }

            return (object)[
                'start_date' => $startDate,
                'end_date' => $endDate
            ];
        } catch (\Exception $e) {
            Log::error('Error calculating period from pattern: ' . $e->getMessage());
            return null;
        }
    }


    public function getEmployeeHourlySalary($employee_id, $month, $hourly_rate)
    {
        $getDate = $this->getMonthToStartDateAndEndDate($month);
        $queryResult = EmployeeAttendanceApprove::where('employee_id', $employee_id)->whereBetween('date', [$getDate['firstDate'], $getDate['lastDate']])->get()->toArray();

        $totalAmountOfSalary = 0;
        $hour = 0;
        $minutes = 0;
        foreach ($queryResult as $value) {
            if ($value['approve_working_hour'] == '00:00' || $value['approve_working_hour'] == '') {
                continue;
            }
            $value = explode(':', date('H:i', strtotime($value['approve_working_hour'])));
            $hour += $value[0];
            $minutes += $value[1];
            if ($minutes >= 60) {
                $minutes -= 60;
                $hour++;
            }
        }

        $totalTime = $hour . ':' . (($minutes < 10) ? '0' . $minutes : $minutes);
        $perMinuteAmount = $hourly_rate / 60;
        $minuteWiseAmount = $perMinuteAmount * (($minutes < 10) ? '0' . $minutes : $minutes);

        $totalAmountOfSalary = ($hour * $hourly_rate) + $minuteWiseAmount;;

        $data = [
            'totalWorkingHour' => $totalTime,
            'totalSalary' => round($totalAmountOfSalary),
        ];
        return $data;
    }

    public function generatePayrollExcel($salaryMonth)
    {
        $this->salaryMonth1 = $salaryMonth;
        $allSalaryDetails = SalaryDetails::where('month_of_salary', $salaryMonth)->get();

        if ($allSalaryDetails->isEmpty()) {
            return redirect()->back()->with('error', 'No Salary Info Available for the selected month.');
        } else {

            Excel::raw('Full Payroll -' . $salaryMonth, function ($excel) {
                $salaryMonth = $this->salaryMonth1;
                $excel->sheet('Staff Payroll-' . $salaryMonth, function ($sheet) {
                    $salaryMonth = $this->salaryMonth1;
                    $AllEmployeeDetails = [];
                    $allSalaryDetails = SalaryDetails::where('month_of_salary', $salaryMonth)->with('allowances')->where('department_id', '!=', 15)->get();

                    if ($allSalaryDetails->isEmpty()) {
                        return redirect('generateSalarySheet')->with('error', 'No Salary Info Available for.' . $salaryMonth);
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

    public function generateManagementPayrollExcel($salaryMonth)
    {
        $this->salaryMonth1 = $salaryMonth;
        $allSalaryDetails = SalaryDetails::where('month_of_salary', $salaryMonth)->where('department_id', 15)->get();

        if ($allSalaryDetails->isEmpty()) {
            return redirect('managementPayIndex')->with('error', 'No Salary Info Available for the current month.');
        } else {
            $this->salaryMonth1 = $salaryMonth;
            Excel::create('Management Payroll -' . $salaryMonth, function ($excel) {

                $salaryMonth = $this->salaryMonth1;
                $excel->sheet('Management Payroll -' . $salaryMonth, function ($sheet) {
                    $salaryMonth = $this->salaryMonth1;
                    $AllEmployeeDetails = [];
                    $date = date('Y-m-25', strtotime('now'));
                    $now = Carbon::now()->format('Y-m-d');
                    if ($now > $date) {
                        $month = date('Y-m', strtotime('now'));
                    } else {
                        $month = date('Y-m', strtotime('now - 1 month'));
                    }
                    $allSalaryDetails = SalaryDetails::where('month_of_salary', $salaryMonth)->where('department_id', 15)->get();

                    if ($allSalaryDetails->isEmpty()) {
                        return redirect()->back()->with('error', 'No Salary Info Available.');
                    }

                    foreach ($allSalaryDetails as $allSalaries) {
                        $id = $allSalaries->salary_details_id;
                        $ifHourly = SalaryDetails::with(['employee' => function ($q) {
                            $q->with(['hourlySalaries', 'department', 'designation']);
                        }])->where('salary_details_id', $id)->first();

                        if ($ifHourly->action == 'monthlySalary') {
                            $result = $this->managementPaySlipDataFormat($id);
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
                            'Bonuses' => $result1['salaryDetails']->total_bonuses,
                            'Airtime-non-taxable' => $result1['salaryDetails']->airtime_untaxed,
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

    public function downloadFullPayrollPDF($salaryMonth)
    {
        $AllEmployeeDetails = [];
        $allSalaryDetails = SalaryDetails::where('month_of_salary', $salaryMonth)->with('SalaryBonuses')->where('department_id', '!=', 15)->get();

        if ($allSalaryDetails->isEmpty()) {
            return redirect()->back()->with('error', 'No Salary Info Available for the selected month.');
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

        //return view ('admin.payroll.salarySheet.newMultiPayslipPdf', compact('AllEmployeeDetails'));
        $generated_at = Carbon::now()->format('YmdHis');
        $pdf = Pdf::loadView('admin.payroll.salarySheet.newMultiPayslipPdf', compact('AllEmployeeDetails'));
        $pdf->setPaper('A4', 'portrait');
        return $pdf->download("Staff_PaySlips_" . "$salaryMonth" . '-' . $generated_at . ".pdf");
    }

    public function downloadMgntPayslips($salaryMonth)
    {
        $AllEmployeeDetails = [];
        $allSalaryDetails = SalaryDetails::where('month_of_salary', $salaryMonth)->where('department_id', 15)->get();

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
        $pdf = Pdf::loadView('admin.payroll.salarySheet.management.newMultiPayslipPdf', compact('AllEmployeeDetails'));
        $pdf->setPaper('A4', 'portrait');
        return $pdf->download("Management Full_PaySlips_" . "$salaryMonth" . ".pdf");
    }

    public function paySlipDataFormat($id)
    {
        $printHeadSetting = PrintHeadSetting::first();
        $salaryDetails = SalaryDetails::select('salary_details.*', 'employee.employee_id', 'employee.department_id', 'employee.designation_id', 'department.department_name', 'designation.designation_name', 'employee.first_name', 'employee.last_name', 'job_categories.name', 'employee.date_of_joining')
            ->join('employee', 'employee.employee_id', 'salary_details.employee_id')
            ->join('department', 'department.department_id', 'employee.department_id')
            ->join('designation', 'designation.designation_id', 'employee.designation_id')
            ->where('salary_details_id', $id)->first();

        $salaryDetailsToAllowance = SalaryDetailsToAllowance::join('allowance', 'allowance.allowance_id', 'salary_details_to_allowance.allowance_id')
            ->where('salary_details_id', $id)->get();

        $salaryDetailsToDeduction = SalaryDetailsToDeduction::join('deduction', 'deduction.deduction_id', 'salary_details_to_deduction.deduction_id')
            ->where('salary_details_id', $id)->get();

        $salaryDetailsToAdvances = SalaryDetailsToAdvances::join('salary_advances', 'salary_advances.salary_advance_id', 'salary_details_to_advances.salary_advance_id')
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

    public function getEmployeeOtmAbsLvLtAndWokDaysForMigrating($employee_id, $month)
    {

        $getDate = $this->getMonthToStartDateAndEndDate($month);
        $queryResult = $this->attendanceRepository->getEmployeeMonthlyAttendance($getDate['firstDate'], $getDate['lastDate'], $employee_id);

        return $queryResult;
    }

    public function newGetEmployeeOtmAbsLvLtAndWokDays($employee_id, $month, $overtime_rate, $monthly_gross_salary, $daily_gross_pay)
    {
        $getDate = $this->getMonthToStartDateAndEndDate($month);
        $startingDate = $getDate['firstDate'];
        $endingDate = $getDate['lastDate'];

        $attendanceData11 = Attendance::where('employee_id', $employee_id)->whereBetween('date', [$startingDate, $endingDate])->orderBy('date', 'asc')->groupBy('date')->get();
        $workingDates = $this->attendanceRepository->new_number_of_working_days_date($startingDate, $endingDate);


        //Calculate total number of days oin the month.
        $datetime1 = new DateTime($startingDate);
        $datetime2 = new DateTime($endingDate);
        $interval = $datetime1->diff($datetime2)->format('%a');
        $totalDaysInTheMonth = (int)$interval + 1;

        $workedholidays2 = [];
        $holidaysWorked = 0;
        foreach ($attendanceData11 as $monthlyAttendanceDatas) {

            if (!in_array($monthlyAttendanceDatas->date, $workingDates) && $monthlyAttendanceDatas->presence_status == 'PRESENT') {
                $holidaysWorked += 1;
                $workedholidays2[] = $monthlyAttendanceDatas->date;
            }
        }

        $overTime = [];
        $totalPresent = 0;
        $totalAbsence = 0;
        $totalLeave = 0;
        $totalLate = 0;
        $totalLateAmount = 0;
        $totalAbsenceAmount = 0;
        $totalWorkingDays = count($workingDates);
        $totalHolidaysWorked = 0;
        $wasOnLeave = 0;
        $totalAbsence2 = 0;

        $leaveRecord = LeaveApplication::select('application_from_date', 'application_to_date')
            ->where('leave_application.status', LeaveStatus::APPROVE)
            ->where('application_from_date', '>=', $getDate['firstDate'])
            ->where('application_to_date', '<=', $getDate['lastDate'])
            ->where('employee_id', $employee_id)
            ->get()
            ->toArray();

        $newLeaves = Arr::flatten($leaveRecord);
        $wasOnleave2[] = 0;

        foreach ($attendanceData11 as $value) {

            if (in_array($value->date, $newLeaves)) {
                $wasOnLeave += 1;
            } elseif ($value['presence_status'] == 'OFF') {
                $wasOnLeave += 1;
            } elseif ($value['presence_status'] == 'AL') {
                $wasOnLeave += 1;
            } elseif ($value['presence_status'] == 'ML') {
                $wasOnLeave += 1;
            } elseif ($value['presence_status'] == 'PL') {
                $wasOnLeave += 1;
            } elseif ($value['presence_status'] == 'SICK') {
                $wasOnLeave += 1;
            } elseif ($value['presence_status'] == 'Training') {
                $wasOnLeave += 1;
            } elseif ($value['presence_status'] == 'PRESENT') {
                $totalPresent += 1;
            } elseif ($value['presence_status'] == 'ABSENT') {
                $totalAbsence2 += 1;
            } elseif ($value['presence_status'] == 'AWP') {
                $totalAbsence2 += 1;
            }

            if ($value['is_late'] == 'Yes') {
                $totalLate += 1;
            }

            if ($value->time_out != null) {
                $hours = 9 * 60 * 60;
                $clockOut = \Carbon\Carbon::parse($value->time_out);
                $clockIn = \Carbon\Carbon::parse($value->time_in);
                $totalDuration1 = $clockOut->diffInSeconds($clockIn);

                if ($totalDuration1 > $hours) {
                    $interval = $totalDuration1 - $hours; //the value is in seconds

                    //check if the value is at least 1hour otherwise assign zero
                    if ($interval >= 60) {
                        $overTime[] = gmdate('H:i', $interval);
                    } else {
                        $overTime[] = gmdate('H:i', 0);
                    }
                }
            }


            //            $workingHour = new DateTime($value['workingHours']);
            //
            //            $workingTime = new DateTime($value['working_time']);
            //            if ($workingHour < $workingTime) {
            //                $interval = $workingHour->diff($workingTime);
            //                $overTime[] = $interval->format('%H:%I');
            //            }
            $totalHolidaysWorked = $holidaysWorked;
        }


        $totalDaysOfLeave = $wasOnLeave;

        /**
         * @employee Salary Deduction For Late Attendance
         */
        $salaryDeduction = SalaryDeductionForLateAttendance::where('status', 'Active')->first();
        $dayOfSalaryDeduction = 0;
        $oneDaysSalary = 0;
        if ($monthly_gross_salary != 0 && $totalWorkingDays != 0 && $totalLate != 0 && !empty($salaryDeduction)) {
            $numberOfDays = 0;
            for ($i = 1; $i <= $totalLate; $i++) {
                $numberOfDays++;
                if ($numberOfDays == $salaryDeduction->for_days) {
                    $dayOfSalaryDeduction += 1;
                    $numberOfDays = 0;
                }
            }

            $oneDaysSalary = $daily_gross_pay;
            $totalLateAmount = $oneDaysSalary * $dayOfSalaryDeduction;
        }
        $totalAbsents1 = $totalAbsence2;
        if ($totalAbsents1 < 0) $totalAbsents1 = -$totalAbsents1;

        /**
         * @employee Salary Deduction For absence
         */

        $totalDaysUnAccounted = $totalDaysInTheMonth - ($totalDaysOfLeave + $totalAbsence2 + $totalPresent);


        if ($totalAbsence2 != 0 && $monthly_gross_salary != 0 && $totalWorkingDays != 0) {
            $totalAbsenceAmount = ($daily_gross_pay * $totalAbsence2) + ($daily_gross_pay * $totalDaysUnAccounted);
        }

        $oneDaySalary = $daily_gross_pay;
        $overTime = $this->calculateEmployeeTotalOverTime($overTime, $overtime_rate);

        $data = [
            'overtime_rate' => $overtime_rate,
            'totalOverTimeHour' => $overTime['totalOverTimeHour'],
            'totalOvertimeAmount' => $overTime['overtimeAmount'],
            'totalPresent' => $totalPresent,
            'totalAbsence' => $totalAbsence2,
            'totalAbsenceAmount' => round($totalAbsenceAmount),
            'totalLeave' => $totalDaysOfLeave,
            'totalLate' => $totalLate,
            'dayOfSalaryDeduction' => $dayOfSalaryDeduction,
            'totalLateAmount' => round($totalLateAmount),
            'totalWorkingDays' => $totalWorkingDays,
            'oneDaysSalary' => $oneDaySalary,
            'totalHolidaysWorked' => $totalHolidaysWorked,
            'totalDaysInTheMonth' => $totalDaysInTheMonth,
        ];

        return $data;
    }

    public function migrateGetEmployeeOtmAbsLvLtAndWokDaysForMigrating($employee_id, $month)
    {

        $getDate = $this->getMonthToStartDateAndEndDate($month);
        $queryResult = $this->attendanceRepository->migrateGetEmployeeMonthlyAttendance($getDate['firstDate'], $getDate['lastDate'], $employee_id);

        return $queryResult;
    }

    public function calculateSHIF($gross_amount)
    {
        $shifRate = $this->payrollCalculations->calculateSHIF($gross_amount);
        if ($shifRate < 300) {
            $shifRate = 300;
        }
        return $shifRate;
    }
}
