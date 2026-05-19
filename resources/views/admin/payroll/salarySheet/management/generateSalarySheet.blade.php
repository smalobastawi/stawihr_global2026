@extends('admin.master')

@section('title')
    @lang('salary_sheet.management_salary_generate')
@endsection
@section('content')
<style>
    .table > tbody > tr > td {
        padding: 5px 7px;
    }

    .address {
        margin-top: 22px;
    }

    .employeeName {
        position: relative;
    }

    #employee_id-error {
        position: absolute;
        top: 66px;
        left: 0;
        width: 100% he;
        width: 100%;
        height: 100%;
    }

    .icon-question {
        color: #7460ee;
        font-size: 16px;
        vertical-align: text-bottom;
    }

</style>
<div class="container-fluid">
    <div class="row bg-title">
        <div class="col-lg-5 col-md-5 col-sm-5 col-xs-12">
            <ol class="breadcrumb">
                <li class="active breadcrumbColor"><a href="{{ url('dashboard') }}"><i
                                class="fa fa-home"></i> @lang('dashboard.dashboard')</a></li>
                <li>@yield('title')</li>

            </ol>
        </div>
        <div class="col-lg-7 col-md-7 col-sm-7 col-xs-12">
            <a href="{{route('managementPay.index')}}"
               class="btn btn-success pull-right m-l-20 hidden-xs hidden-sm waves-effect waves-light"><i
                        class="fa fa-list-ul" aria-hidden="true"></i> Go to management payroll</a>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-info">
                <div class="panel-heading"><i class="mdi mdi-clipboard-text fa-fw"></i> @yield('title')</div>
                <div class="panel-wrapper collapse in" aria-expanded="true">
                    <div class="panel-body">
                        @if($errors->any())
                            <div class="alert alert-danger alert-dismissible" role="alert">
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                                            aria-hidden="true">×</span></button>
                                @foreach($errors->all() as $error)
                                    <strong>{!! $error !!}</strong><br>
                                @endforeach
                            </div>
                        @endif
                        @if(session()->has('success'))
                            <div class="alert alert-success alert-dismissable">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                                <i class="cr-icon glyphicon glyphicon-ok"></i>&nbsp;<strong>{{ session()->get('success') }}</strong>
                            </div>
                        @endif
                        @if(session()->has('error'))
                            <div class="alert alert-danger alert-dismissable">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                                &nbsp;<strong>{{ session()->get('error') }}</strong>
                            </div>
                        @endif
{{--                        <form method="POST">
							@csrf--}}
{{--                        <div class="form-body">--}}
{{--                            <div class="row">--}}
{{--                                <div class="col-md-offset-2 col-md-3">--}}
{{--                                    <div class="form-group employeeName">--}}
{{--                                        <label for="exampleInput">@lang('common.employee')<span--}}
{{--                                                    class="validateRq">*</span></label>--}}
{{--                                        <select name="employee_id" class="form-control employee_id select2 required">
@foreach($employeeList as $__key => $__value)
<option value="{{ $__key }}" {{ (string)(isset($employee_id)) ? $employee_id : '' == (string)$__key ? 'selected' : '' }}>{{ $__value }}</option>
@endforeach
</select>--}}
{{--                                    </div>--}}
{{--                                </div>--}}
{{--                                <div class="col-md-3">--}}
{{--                                    <label for="exampleInput">@lang('common.month')<span--}}
{{--                                                class="validateRq">*</span></label>--}}
{{--                                    <div class="input-group">--}}
{{--                                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>--}}
{{--                                        <input type="text" name="month" value="{{ (isset($month)) ? $month : $currentMonth }}">--}}
{{--                                    </div>--}}
{{--                                </div>--}}
{{--                                <div class="col-md-2">--}}
{{--                                    <div class="form-group">--}}
{{--                                        <button type="submit" class="btn btn-info "--}}
{{--                                                style="margin-top: 24px"> @lang('salary_sheet.generate_salary')</button>--}}
{{--                                    </div>--}}
{{--                                </div>--}}
{{--                            </div>--}}
{{--                        </div>--}}
{{--                        </form>--}}

                            <form method="GET" action="{{ route('generateSalarySheet.calculateEmployeeSalary') }}">
                            <div class="form-body">
                                <div class="row">
                                    <div class="col-md-offset-2 col-md-3">
                                        <div class="form-group employeeName">
                                            <label for="exampleInput">@lang('common.employee')<span
                                                    class="validateRq">*</span></label>
                                            <select name="employee_id" class="form-control employee_id select2 required">
@foreach($employeeList as $__key => $__value)
<option value="{{ $__key }}" {{ (string)(isset($employee_id)) ? $employee_id : '' == (string)$__key ? 'selected' : '' }}>{{ $__value }}</option>
@endforeach
</select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <label for="exampleInput">@lang('common.month')<span
                                                class="validateRq">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                            <input type="text" name="month" value="{{ (isset($month)) ? $month : $currentMonth }}">
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <button type="submit" class="btn btn-info "
                                                    style="margin-top: 24px"> New Salary Generate</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            </form>
                    </div>
                </div>
            </div>
            @php
                $netSalary = 0;
                $totalOvertimeAmount = 0;
                $totalDeduction = 0;
                $sumOfTotalDeduction = 0;
                $sumOfTotalBonus =0;
                $totalBonuses = 0;
                $totalAdvances =0;
                $untaxed_alowances =0;
            @endphp
            @if(isset($employeeDetails) )

                @php
                    $totalHolidaysWorked = $employeeAllInfo['totalHolidaysWorked'];

                    // Get salary details from employee payroll or defaults
                    $basic_salary = $employeeDetails->employeePayroll->basic_salary ?? 0;
                    $gross_salary = $employeeDetails->employeePayroll->gross_salary ?? $basic_salary;
                    $daily_rate = $basic_salary / 30; // Calculate daily rate from basic salary

                    $totalHolidaysWorkedPay = $daily_rate * $totalHolidaysWorked;

                    // Get allowances from employee earnings
                    $total_allowances = 0;
                    if ($employeeDetails->payrollEarnings) {
                        $total_allowances = $employeeDetails->payrollEarnings->sum('amount');
                    }

                    $net_salary = $basic_salary + $total_allowances;

                    $taxableIncome = $taxableSalary;
                    $incomeAfterTax = $taxableIncome - $tax;
                @endphp

                <div class="panel panel-info">
                    <div class="panel-heading"><i
                                class="mdi mdi-clipboard-text fa-fw"></i> @lang('salary_sheet.salary_sheet')
                        / @lang('salary_sheet.final_balance') </div>
                    <div class="panel-wrapper collapse in" aria-expanded="true">
                        <form method="POST">
							@csrf
                        <div class="panel-body" style="    padding: 18px 49px;">
                            <br>
                            <div class="row" style="border: 1px solid #ddd;padding: 26px 9px">
                                <div class="col-md-6">
                                    <table class="table table-bordered table-hover table-striped">
                                        <tbody>
                                        <tr>
                                            <td class="col-md-6">@lang('common.name') :</td>
                                            <td class="col-md-6">
                                                <b>{{$employeeDetails->first_name}} {{$employeeDetails->last_name}}</b>
                                            </td>
                                            <input type="hidden" name="department_id" value="{{$employeeDetails->department_id}}">
                                        </tr>
                                        <tr>
                                            <td>@lang('employee.department') :</td>
                                            <td>
                                                <b>@if(isset($employeeDetails->department->department_name)) {{$employeeDetails->department->department_name}} @endif</b>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>@lang('employee.designation') :</td>
                                            <td>
                                                <b>@if(isset($employeeDetails->designation->designation_name)) {{$employeeDetails->designation->designation_name}} @endif</b>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td>Job Category :</td>
                                            <td>
                                                <b>@if(isset($employeeDetails->jobCategory->name)) {{$employeeDetails->jobCategory->name}} @endif</b>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>@lang('employee.date_of_joining') :</td>
                                            <td><b> {{date(" d-M-Y", strtotime($employeeDetails->date_of_joining))}}</b>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>@lang('salary_sheet.basic_salary') :</td>
                                            <td class="text-center adjustmentSalary">
                                                {{number_format( $basic_salary) }}
                                                @php  $basic_salary; @endphp
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>House Allowance:</td>
                                            <td class="text-center ">{{number_format($employeeDetails->jobCategory->house_allowance)}}</td>
                                            <input type="hidden" name="house_allowance" value="{{$employeeDetails->jobCategory->house_allowance}}">
                                        </tr>
                                        <tr>
                                            <td>Transport Allowance:</td>
                                            <td class="text-center ">{{$employeeDetails->jobCategory->transport_allowance}}</td>
                                            <input type="hidden" name="transport_allowance" value="{{$employeeDetails->jobCategory->transport_allowance}}">

                                        </tr>

                                        <tr>
                                            <td>Banking Allowance:</td>
                                            <td class="text-center ">{{$employeeDetails->jobCategory->banking_allowance}}</td>
                                            <input type="hidden" name="banking_allowance" value="{{$employeeDetails->jobCategory->banking_allowance}}">
                                        </tr>
{{--                                        <tr>--}}
{{--                                            <td>Total Public Holidays Pay</td>--}}
{{--                                            <td class="text-center">{{$totalHolidaysWorkedPay}}</td>--}}
{{--                                        </tr>--}}
{{--                                        Bonuses calculationa here--}}
                                        @if(count($salaryBonuses['salaryBonusArray']) > 0)

                                            @foreach($salaryBonuses['salaryBonusArray'] as $salaryBonus)

                                                @php $sumTotalBonuses =0 ;
                                                  $sumTotalBonuses +=$salaryBonus['salary_bonus_amount'];
                                                @endphp

                                                @if($salaryBonus['salary_bonus_name'] == 'PRO-RATA')
                                                    @php $prorataPay = $salaryBonus['salary_bonus_amount']; @endphp
                                                    @php
                                                        $netSalary += $salaryBonus['salary_bonus_amount'];

                                                    @endphp
                                                    <tr>
                                                        <td>{{$salaryBonus['salary_bonus_name']}} :</td>
                                                        <td class="text-center"> {{number_format($salaryBonus['salary_bonus_amount'])}}</td>
                                                    </tr>
                                                    <input type="hidden" readonly name="pro_rata"
                                                           value="{{$salaryBonus['salary_bonus_amount']}}">

                                                @elseif($salaryBonus['salary_bonus_name'] == 'AIRTIME')
                                                    @php $airtime_untaxed = $salaryBonus['salary_bonus_amount']; @endphp
                                                    @php
                                                        $untaxed_alowances += $salaryBonus['salary_bonus_amount'];

                                                    @endphp
                                                    <tr>
                                                        <td>{{$salaryBonus['salary_bonus_name']}} :</td>
                                                        <td class="text-center"> {{number_format($salaryBonus['salary_bonus_amount'])}}</td>
                                                    </tr>
                                                    <input type="hidden" readonly name="airtime_untaxed"
                                                           value="{{$salaryBonus['salary_bonus_amount']}}">
                                                @else
                                                    @php $totalBonuses+= $salaryBonus['salary_bonus_amount'];@endphp

                                                    <tr>
                                                        <td>{{$salaryBonus['salary_bonus_name']}} :</td>
                                                        <td class="text-center"> {{number_format($salaryBonus['salary_bonus_amount'])}}</td>
                                                        @php
                                                            $netSalary += $salaryBonus['salary_bonus_amount'];
                                                            $sumOfTotalBonus+=$salaryBonus['salary_bonus_amount'];
                                                        @endphp
                                                        <input type="hidden" name="salary_bonus_id[]"
                                                               value="{{$salaryBonus['salary_bonus_id']}}">
                                                        <input type="hidden" readonly name="salary_bonus_amount[]"
                                                               value="{{$salaryBonus['salary_bonus_amount']}}">
                                                        <input type="hidden" readonly name="salary_bonus_name[]"
                                                               value="{{$salaryBonus['salary_bonus_name']}}">
                                                    </tr>
                                                @endif
                                            @endforeach
                                            <!-- End of bonuses calculation-->
                                        @endif
                                        @if($employeeAllInfo['totalOvertimeAmount'] !=0)
                                            <tr>
                                                <td> Over time:</td>
                                                <td class="text-center">   {{$employeeAllInfo['totalOvertimeAmount']}}   </td>
                                            </tr>
                                        @endif

                                        <tr>
                                           @php $gross_salary1 = ($employeeDetails->jobCategory->gross_pay+$employeeAllInfo['totalOvertimeAmount']+$totalBonuses+$untaxed_alowances);

                                            @endphp
                                            <td>Gross Salary: </td>
                                            <td class="text-center"
                                                style="background: #ddd">
                                                {{number_format($gross_salary1)}}

                                            </td>
                                            <input type="hidden" name="gross_pay"
                                                   value="{{$gross_salary1}}">
                                        </tr>
                                        <tr>
                                            <td>NSSF :</td>
                                            <td class="text-center">{{number_format($statutoryDeduction, 2)}}</td>
                                        </tr>
                                        <tr>
                                            <td>@lang('salary_sheet.taxable_salary') :</td>
                                            @php
                                            $taxableSalary11 = $gross_salary1-($statutoryDeduction+$untaxed_alowances);
                                        @endphp
                                            <td class="text-center"> {{number_format($taxableSalary11)}}</td>
                                            <input type="hidden" name="taxable_salary"
                                                   value="{{$taxableSalary11}}">

                                        </tr>
                                        <tr>
                                            <td>P.A.Y.E:</td>
                                            <td class="text-center"> {{number_format($tax, 2)}}</td>
                                        </tr>
                                        {{-- Loan Deductions Section --}}
                                        @if(isset($loanDeductions) && count($loanDeductions) > 0)
                                        <div class="danger" style="background-color: olive">
                                            <tr>
                                                <td>Loan Deductions</td>
                                                <td><b>Employee Loan Deductions</b></td>
                                            </tr>
                                                @foreach($loanDeductions as $loan)
                                                    @php $totalAdvances+= $loan['amount'];@endphp
                                                    <tr>
                                                        <td>Loan - {{$loan['name']}} :</td>
                                                        <td class="text-center"> {{number_format($loan['amount'], 2)}}</td>
                                                        @php
                                                            $sumOfTotalDeduction+=$loan['amount'];
                                                        @endphp
                                                        <input type="hidden" name="loan_deduction_id[]"
                                                               value="{{$loan['id']}}">
                                                        <input type="hidden" readonly name="loan_deduction_amount[]"
                                                               value="{{$loan['amount']}}">
                                                    </tr>
                                                @endforeach
                                        </div>
                                        @endif

{{--                                        Removed absent calculation from management pay--}}

                                        @if($employeeAllInfo['totalAbsence'] > 11)
                                            <tr>
                                                <td>@lang('salary_sheet.absence_amount') :</td>
                                                <td class="text-center"> {{number_format($employeeAllInfo['totalAbsenceAmount'])}}</td>
                                                @php

                                                   // $sumOfTotalDeduction +=$employeeAllInfo['totalAbsenceAmount'];
                                                @endphp
                                            </tr>
                                        @endif
                                        @if($employeeAllInfo['totalOvertimeAmount'] != 0)
                                            <tr>

                                                @php
                                                    $netSalary += $employeeAllInfo['totalOvertimeAmount'];
                                                    $totalOvertimeAmount +=$employeeAllInfo['totalOvertimeAmount'];
                                                @endphp
                                                <input type="hidden" name="overtime_rate" class="form-control allowance"
                                                       value="{{$employeeAllInfo['overtime_rate']}}">
                                                <input type="hidden" name="total_over_time_hour"
                                                       class="form-control allowance"
                                                       value="{{$employeeAllInfo['totalOverTimeHour']}}">
                                                <input type="hidden" name="total_overtime_amount"
                                                       class="form-control allowance"
                                                       value="{{$employeeAllInfo['totalOvertimeAmount']}}">
                                            </tr>
                                        @endif
                                        <tr>
                                            <td>NHIF:</td>
                                            <td class="text-center"
                                                style="background: #ddd">{{$nhifRate}}</td>
                                        </tr>
                                        <tr>
                                            <td> All Deductions:</td>
                                            <td class="text-center"
                                                style="background: #ddd">  {{number_format($sumOfTotalDeduction +$nhifRate+$tax+$statutoryDeduction)}} </td>
                                        </tr>
                                        @php
                                            //$bAO is Bonuses, allowances and Overtime
                                        $bAO = $netSalary;
                                            $finalSalary = ($taxableSalary11 + $untaxed_alowances)-($sumOfTotalDeduction +$nhifRate+$tax);
                                        @endphp
                                        <tr>
                                            <td> Net Salary :
                                            </td>
                                            <td class="text-center">   {{($finalSalary)}} </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>

                                <input type="hidden" name="net_salary" value="{{$finalSalary}}">
                                <input type="hidden" name="basic_salary" value="{{$basic_salary}}">
                                <input type="hidden" name="month_of_salary" value="{{$month}}">
                                <input type="hidden" name="total_working_days"
                                       value="{{$employeeAllInfo['totalWorkingDays']}}">
                                <input type="hidden" name="total_present" value="{{$employeeAllInfo['totalPresent']}}">
                                <input type="hidden" name="total_leave" value="{{$employeeAllInfo['totalLeave']}}">
                                <input type="hidden" name="employee_id" value="{{$employee_id}}">
                                <input type="hidden" name="action" value="monthlySalary">
                                <input type="hidden" name="tax" readonly class="form-control tax" value="{{$tax}}">
                                <input type="hidden" name="total_absence_amount" class="form-control deduction"
                                       value="{{$employeeAllInfo['totalAbsenceAmount']}}">
                                <input type="hidden" name="total_absence" class="form-control deduction"
                                       value="{{$employeeAllInfo['totalAbsence']}}">
                                <input type="hidden" class="form-control total_allowance" name="total_allowance"
                                       value="{{$total_allowances}}">
                                <input type="hidden" class="form-control total_bonuses" name="total_bonuses"
                                       value="{{$totalBonuses}}">
                                <input type="hidden" class="form-control total_advances" name="total_advances"
                                       value="{{$totalAdvances}}">
                                <input type="hidden" class="form-control total_allowance" name="nssf_amount"
                                       value="{{$statutoryDeduction}}">
                                <input type="hidden" class="form-control no_of_holidays_worked"
                                       name="no_of_holidays_worked"
                                       value="{{number_format($totalHolidaysWorked)}}">
                                {{--                                More employee details here--}}
                                <input type="hidden" class="form-control payroll_no" name="payroll_no"
                                       value="{{$employeeDetails['payroll_number']}}">
                                <input type="hidden" class="form-control gross_pay" name="gross_pay"
                                       value="{{$employeeDetails->jobCategory->gross_pay+($employeeAllInfo['totalOvertimeAmount'])}}">
                                <input type="hidden" class="form-control nssf_no" name="nssf_no"
                                       value="{{$employeeDetails['NSSF_no']}}">
                                <input type="hidden" class="form-control nhif_no" name="nhif_no"
                                       value="{{$employeeDetails['NHIF_no']}}">
                                <input type="hidden" class="form-control PAYE_tax" name="PAYE_tax" value="{{$tax}}">

                                <input type="hidden" class="form-control nhifRate" name="nhifRate"
                                       value=" {{$nhifRate}}">

                                <input type="hidden" class="form-control public_holidays_pay"
                                       name="public_holidays_pay" value="0">
                                <input type="hidden" class="form-control employee_id_no" name="employee_id_no"
                                       value="{{$employeeDetails['national_id']}}">
                                <input type="hidden" class="form-control KRA_Pin" name="kra_pin"
                                       value="{{$employeeDetails['KRA_Pin']}}">

                                <!-- nssf-tiers-here -->
                                <input type="hidden" class="form-control nssf_tier_1" name="nssf_tier_1"
                                       value="{{$nssf_tier1}}">
                                <input type="hidden" class="form-control nssf_tier_2" name="nssf_tier_2"
                                       value="{{$nssf_tier2}}">
                                <input type="hidden" class="form-control total_nssf" name="total_nssf"
                                       value="{{$total_nssf}}">

                                <div class="col-md-6">
                                    <table class="table table-bordered table-hover table-striped">
                                        <tbody>
                                        <tr>
                                            <td class="col-md-6">Payroll No. :</td>
                                            <td class="col-md-6"><b>{{$employeeDetails->payroll_number}}</b></td>
                                        </tr>
                                        <tr>
                                            <td>@lang('common.month') :</td>
                                            <td><b>{{convartMonthAndYearToWord($month)}}</b></td>
                                        </tr>
                                        <tr>
                                            <td>@lang('salary_sheet.number_of_working_days') :</td>
                                            <td><b>{{$employeeAllInfo['totalWorkingDays']}}</b></td>
                                        </tr>
                                        <tr>
                                            <td> @lang('salary_sheet.number_of_worked_in_them_month') :</td>
                                            <td class="text-center">   {{$employeeAllInfo['totalPresent']}} </td>
                                        </tr>
{{--                                        <tr>--}}
{{--                                            <td> Number of govt. holidays worked in the month :</td>--}}
{{--                                            <td class="text-center"> {{$employeeAllInfo['totalHolidaysWorked']}}   </td>--}}
{{--                                        </tr>--}}

{{--                                        <tr>--}}
{{--                                            <td> Holidays Pay Rate (per day):</td>--}}
{{--                                            <td class="text-center"> {{$employeeAllInfo['oneDaysSalary']}}   </td>--}}
{{--                                        </tr>--}}
{{--                                        <tr>--}}
{{--                                            <td> @lang('salary_sheet.unjustified_absence') :</td>--}}
{{--                                            <td class="text-center">   {{$employeeAllInfo['totalAbsence']}}   </td>--}}
{{--                                        </tr>--}}
                                        <tr>
                                            <td>  @lang('salary_sheet.per_day_salary') :</td>
                                            <td class="text-center">   {{number_format($employeeAllInfo['oneDaysSalary'])}}   </td>
                                            <input type="hidden" name="per_day_salary"
                                                   value="{{$employeeAllInfo['oneDaysSalary']}}">
                                        </tr>
                                        @if($employeeAllInfo['dayOfSalaryDeduction'] !=0)
                                            <tr>
                                                <td>  @lang('salary_sheet.salary_deduction_for_late_attendance') :</td>
                                                <td class="text-center">   {{$employeeAllInfo['dayOfSalaryDeduction']}}   </td>
                                            </tr>
                                        @endif
                                        @if($employeeAllInfo['totalOvertimeAmount'] !=0)
                                            <tr>
                                                <td>  @lang('salary_sheet.over_rate') :</td>
                                                <td class="text-center">   {{$employeeAllInfo['overtime_rate']}}   </td>
                                            </tr>
                                        @endif
                                        @if(count($leaveRecords) > 0)
                                            @foreach($leaveRecords as $leaveRecord)
                                                <tr>
                                                    <td>  {{$leaveRecord->leave_type_name}} :</td>
                                                    <td class="text-center">   {{$leaveRecord->number_of_day}}   </td>
                                                    <input type="hidden" name="num_of_day[]"
                                                           value="{{$leaveRecord->number_of_day}}">
                                                    <input type="hidden" name="leave_type_id[]"
                                                           value="{{$leaveRecord->leave_type_id}}">
                                                </tr>
                                            @endforeach
                                        @endif
                                        </tbody>
                                    </table>
                                </div>
                                <input type="hidden" class="form-control total_deduction" name="total_deduction"
                                       value="{{($sumOfTotalDeduction +$nhifRate+$tax+$statutoryDeduction)}}">
                                <input type="hidden" readonly name="gross_salary" class="form-control gross_salary"
                                       value="{{round($gross_salary1)}}">
                            </div>
                            <br>
                            <div class="col-md-12 text-center">
                                <div class="form-group">
                                    <button type="submit" class="btn btn-info btn_style"><i
                                                class="fa fa-check"></i> @lang('common.save')</button>
                                </div>
                            </div>
                        </div>
                        </form>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@section('page_scripts')
    <script type="text/javascript">
        jQuery(function () {
            $("#calculateEmployeeSalaryForm").validate();
        });

        jQuery(function (){
            $(document).ready(function() {
                $('.select2').select2();
            });
        });
    </script>
@endsection

