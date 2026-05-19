@extends('admin.master')
@section('title')
    @lang('salary_sheet.generate_salary_sheet')
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
            <a href="{{route('payrollIndex')}}"
               class="btn btn-success pull-right m-l-20 hidden-xs hidden-sm waves-effect waves-light"><i
                        class="fa fa-list-ul" aria-hidden="true"></i> Go to Payroll Home</a>
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
                     
                            <form method="GET" action="{{ route('generateSalarySheet.calculateEmployeeSalary') }}">
                            <div class="form-body">
                                <div class="row">
                                    <div class="col-md-offset-2 col-md-3">
                                        <div class="form-group employeeName">
                                            <label for="exampleInput">@lang('common.employee')<span
                                                    class="validateRq">*</span></label>
                                            <select name="employee_id" class="form-control employee_id select2 required" id="employee_id">
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
                                            <button type="submit" class="btn btn-default"
                                                    style="margin-top: 24px">  Generate Salary</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            </form>
                    </div>
                </div>
            </div>
            @if(isset($employeeDetails) )

                @php
                    $totalHolidaysWorked = $employeeAllInfo['totalHolidaysWorked'];

                     $totalHolidaysWorkedPay =  ($basic_salary)*$totalHolidaysWorked;
                        $gross_salary =$employeeGrossSalary;

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
                                        </tr>
                                        <tr>
                                            <td>@lang('employee.department') :</td>
                                            <td>
                                                <b>@if(isset($employeeDetails->department->department_name)) {{$employeeDetails->department->department_name}}
                                                    <input type="hidden" name="department_id"
                                                           value="{{$employeeDetails->department->department_id}}">

                                                    @endif</b>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>@lang('employee.designation') :</td>
                                            <td>
                                                <b>@if(isset($employeeDetails->designation->designation_name)) {{$employeeDetails->designation->designation_name}} @endif</b>
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
                                                {{number_format($basic_salary)}}
                                                @php  $basic_salary; @endphp
                                            </td>
                                        </tr>
                                        @foreach($allowances['allowanceArray'] as $allowance)
                                        <tr>
                                            <td>{{strtoupper($allowance ['allowance_name'])}}:</td>
                                            <td class="text-center ">{{number_format($allowance['amount_of_allowance'])}}</td>
                                            
                                        </tr>
                                        @endforeach
                                        <tr>
                                            <td>Total Public Holidays Pay</td>
                                            <td class="text-center">{{$totalHolidaysWorkedPay}}</td>
                                        </tr>
                                        {{--                                        Bonuses calculationa here--}}
                                        @if(count($salaryBonuses['salaryBonusArray']) > 0)

                                            @foreach($salaryBonuses['salaryBonusArray'] as $salaryBonus)

                                                @php $sumTotalBonuses =0 ;
                                                  $sumTotalBonuses +=$salaryBonus['salary_bonus_amount'];
                                                @endphp

                                                @if($salaryBonus['salary_bonus_name'] == 'PRO-RATA')
                                                    @php $prorataPay = $salaryBonus['salary_bonus_amount']; @endphp
                                                    @php
                                                        $net_salary += $salaryBonus['salary_bonus_amount'];

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
                                                            $net_salary += $salaryBonus['salary_bonus_amount'];
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
                                                            $net_salary += $salaryBonus['salary_bonus_amount'];
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

                                            <td>@lang('salary_sheet.gross_salary') :</td>
                                            <td class="text-center"
                                                style="background: #ddd"> {{number_format($gross_salary2)}}
                                            </td>
                                            <input type="hidden" name="gross_pay"
                                                   value="{{$gross_salary2}}">
                                        </tr>
                                        <tr>
                                            <td>NSSF :</td>
                                            <td class="text-center">{{number_format($statutoryDeduction)}}</td>
                                        </tr>
                                        <tr>
                                            <td>Taxable salary:</td>
                                            
                                            <td class="text-center"> {{number_format($taxableIncome2)}}</td>
                                            <input type="hidden" name="taxable_salary"
                                                   value="{{$taxableIncome2}}">

                                        </tr>
                                        <tr>
                                            <td>P.A.Y.E:</td>
                                            <td class="text-center"> {{number_format($tax)}}</td>
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
                                                        <td class="text-center"> {{number_format($loan['amount'])}}</td>
                                                        <input type="hidden" name="loan_deduction_id[]"
                                                               value="{{$loan['id']}}">
                                                        <input type="hidden" readonly name="loan_deduction_amount[]"
                                                               value="{{$loan['amount']}}">
                                                    </tr>
                                                @endforeach
                                            </div>
                                        @endif
                                        @if($employeeAllInfo['totalLateAmount'] != 0)
                                            <tr>
                                                <td>@lang('salary_sheet.late_amount') :</td>
                                                <td class="text-center"> {{number_format($employeeAllInfo['totalLateAmount'])}}</td>
                                                @php
                                                    $net_salary -= $employeeAllInfo['totalLateAmount'];
                                                    $taxableIncome2 -= $employeeAllInfo['totalLateAmount'];
                                                    $sumOfTotalDeduction +=$employeeAllInfo['totalLateAmount'];
                                                @endphp
                                                <input type="hidden" name="total_late" class="form-control deduction"
                                                       value="{{$employeeAllInfo['dayOfSalaryDeduction']}}">
                                                <input type="hidden" name="total_late_amount"
                                                       class="form-control deduction"
                                                       value="{{$employeeAllInfo['totalLateAmount']}}">
                                            </tr>
                                        @endif
                                        @if($employeeAllInfo['totalAbsenceAmount'] != 0)
                                            <tr>
                                                <td>Absence Amount:</td>
                                                <td class="text-center"> {{number_format($employeeAllInfo['totalAbsenceAmount'])}}</td>
                                                @php

                                                    $sumOfTotalDeduction +=$employeeAllInfo['totalAbsenceAmount'];

                                                @endphp
                                            </tr>
                                        @endif
                                        @if($employeeAllInfo['totalOvertimeAmount'] != 0)
                                            <tr>

                                                @php
                                                    $net_salary += $employeeAllInfo['totalOvertimeAmount'];
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
                                            <td>SHIF:</td>
                                            <td class="text-center"
                                                style="background: #ddd">{{$SHIF_amount}} </td>
                                        </tr>
                                        <tr>
                                            <td>AHL:</td>
                                            <td class="text-center"
                                                style="background: #ddd">{{$housing_levy}} </td>
                                        </tr>
                                        <tr>
                                            <td>Total  Statutory Deductions:</td>
                                            <td class="text-center"
                                                style="background: #ddd">  {{number_format($nhifRate+$statutoryDeduction+$tax+$housing_levy)}} </td>
                                        </tr>
                                        <tr>
                                            <td> Net Salary :
                                            </td>
                                            <td class="text-center">   {{number_format($net_salary1)}} </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>

                                <input type="hidden" name="net_salary" value="{{$net_salary1}}">
                                <input type="hidden" name="basic_salary" value="{{$basic_salary}}">
                                <input type="hidden" name="month_of_salary" value="{{$month}}">
                                <input type="hidden" name="total_working_days"
                                       value="{{$employeeAllInfo['totalWorkingDays']}}">
                                <input type="hidden" name="total_present" value="{{$employeeAllInfo['totalPresent']}}">
                                <input type="hidden" name="total_leave" value="{{$totalDaysOfLeave}}">
                                <input type="hidden" name="employee_id" value="{{$employee_id}}">
                                <input type="hidden" name="action" value="monthlySalary">
                                <input type="hidden" name="tax" readonly class="form-control tax" value="{{$tax}}">
                                <input type="hidden" name="total_absence_amount" class="form-control deduction"
                                       value="{{$employeeAllInfo['totalAbsenceAmount']}}">
                                <input type="hidden" name="total_absence" class="form-control deduction"
                                       value="{{$employeeAllInfo['totalAbsence']}}">
                                <input type="hidden" class="form-control total_allowance" name="total_allowance"
                                       value="{{$allowances['totalAllowance']}}">
                                <input type="hidden" class="form-control total_bonuses" name="total_bonuses"
                                       value="{{$salaryBonuses['totalBonus']}}">
                                <input type="hidden" class="form-control total_advances" name="total_advances"
                                       value="{{$salaryAdvances['totalSalaryAdvances']}}">
                                <input type="hidden" class="form-control total_allowance" name="nssf_amount"
                                       value="{{$statutoryDeduction}}">
                                <input type="hidden" class="form-control no_of_holidays_worked"
                                       name="no_of_holidays_worked"
                                       value="{{number_format($totalHolidaysWorked)}}">
                                {{--                                More employee details here--}}
                                <input type="hidden" class="form-control payroll_no" name="payroll_no"
                                       value="{{$employeeDetails['payroll_number']}}">
                                <input type="hidden" class="form-control gross_pay" name="gross_pay"
                                       value="{{$gross_salary2}}">
                                <input type="hidden" class="form-control nssf_no" name="nssf_no"
                                       value="{{$employeeDetails['NSSF_no']}}">
                                <input type="hidden" class="form-control nhif_no" name="nhif_no"
                                       value="{{$employeeDetails['NHIF_no']}}">
                                <input type="hidden" class="form-control PAYE_tax" name="PAYE_tax" value="{{$tax}}">

                                <input type="hidden" class="form-control SHIF_amount" name="SHIF_amount"
                                value=" {{$SHIF_amount}}">

                                <input type="hidden" class="form-control public_holidays_pay"
                                       name="public_holidays_pay" value="{{$totalHolidaysWorkedPay}}">
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
                                            <td>Total days in the month :</td>
                                            <td class="text-center"><b>{{$employeeAllInfo['totalDaysInTheMonth']}}</b></td>
                                        </tr>
                                        <tr>
                                            <td>@lang('salary_sheet.number_of_working_days') :</td>
                                            <td class="text-center"><b>{{$employeeAllInfo['totalWorkingDays']}}</b></td>
                                        </tr>
                                        <tr>
                                            <td> Number of day worked in the month :</td>
                                            <td class="text-center">   {{$employeeAllInfo['totalPresent']}} </td>
                                        </tr>
                                        <tr>
                                            <td> Number of holidays worked:</td>
                                            <td class="text-center"> {{$employeeAllInfo['totalHolidaysWorked']}}   </td>
                                        </tr>

                                        <tr>
                                            <td> Holidays Pay Rate (per day):</td>
                                            <td class="text-center"> {{number_format((float)$basic_salary/$employeeAllInfo['totalDaysInTheMonth'], 2, '.', '')}}   </td>
                                        </tr>
                                        <tr>
                                            <td> @lang('salary_sheet.unjustified_absence') :</td>
                                            <td class="text-center">   {{$employeeAllInfo['totalAbsence']}}   </td>
                                        </tr>
                                        <tr>
                                            <td>  @lang('salary_sheet.per_day_salary') :</td>
                                            <td class="text-center">   {{number_format((float)$basic_salary/$employeeAllInfo['totalDaysInTheMonth'], 2, '.', '')}}   </td>
                                            <input type="hidden" name="per_day_salary"
                                                   value="{{number_format((float)$basic_salary/$employeeAllInfo['totalDaysInTheMonth'], 2, '.', '')}}">
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
                                            <tr>
                                                <td>Total Leave Days:</td>
                                                <td class="text-center">{{$totalDaysOfLeave}}</td>
                                            </tr>
                                            @foreach($leaveRecords as $leaveRecord)
                                                <tr>

                                                    <input type="hidden" name="num_of_day[]"
                                                           value="{{$leaveRecord->number_of_day}}">
                                                    <input type="hidden" name="leave_type_id[]"
                                                           value="{{$leaveRecord->leave_type_id}}">
                                                </tr>
                                            @endforeach
                                        @endif
                                        <tr>
                                            <td>Days Un-accounted</td>
                                            <td class="text-center"> {{$employeeAllInfo['totalDaysInTheMonth'] -($totalDaysOfLeave+$employeeAllInfo['totalAbsence']+$employeeAllInfo['totalPresent'])}}</td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <input type="hidden" name="total_deduction" value="{{($SHIF_amount+$statutoryDeduction)}}">
                                <input type="hidden" readonly name="gross_salary" class="form-control gross_salary"
                                       value="{{round($gross_salary2)}}">
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

