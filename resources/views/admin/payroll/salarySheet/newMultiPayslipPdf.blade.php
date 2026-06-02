<!DOCTYPE html>
@php
    $companyLogo = companyLogoUrl();
    $companyName = companyDisplayName();
@endphp
<html lang="en">
<head>
    <title>@lang('salary_sheet.employee_payslip')</title>
    <meta charset="utf-8">
</head>
<style>
    body {
        font-size: 9px;
    }

    div.breakNow {
        page-break-inside: avoid;
        page-break-after: always;
    }

    table {
        margin: 0 0 40px 0;
        width: 100%;
        display: table;
        border-spacing: 0px;
    }

    table, td, th {
        border: 1px solid #ddd;
    }

    td {
        padding: 3px;
    }

    th {
        padding: 3px;
    }

    .text-center {
        text-align: center;
    }

    .companyAddress {
        width: 367px;
        margin: 0 auto;
    }

    .container {
        padding-right: 15px;
        padding-left: 15px;
        margin-right: auto;
        margin-left: auto;
        width: 95%;
    }

    .row {
        margin-right: -15px;
        margin-left: -15px;
    }

    .col-md-6 {
        width: 49%;
        float: left;
        padding-right: .5%;
        padding-left: .5%;
    }

    .div1 {
        position: relative;
        margin-top: -4%;
    }

    .div2 {
        position: absolute;
        width: 100%;
        /*border: 1px solid;*/
        padding: 1px 12px 0px 12px;
    }

    .col-md-4 {
        width: 33.33333333%;
        float: left;
    }

    .clearFix {
        clear: both;
    }

    .padding {
        margin-bottom: 32px;
    }
</style>

<body>
<div class="container">
    <div class="row">
    <?php  $counter = 1; ?>
    @foreach($AllEmployeeDetails as  $salaryDetails)

        <div class="row">

            <div class="div">
                <div class="div">
                    <div class="clearFix">
                        <div class="companyAddress">
                            <div class="row" style="margin-left: 30%">
                                <div class="">
                                    @if($companyLogo)
                                        <img src="{{ $companyLogo }}" alt="{{ $companyName }}"
                                             class="logo-light"
                                             style="height: 70px;width: 150px; object-fit: contain;"/>
                                    @endif
                                </div>

                                <h3 style="text-align: center;  margin-left: -30%;"><strong>{{ $companyName }}
                                        @if(companyDisplayPhone())<br>{{ companyDisplayPhone() }}@endif

                                        <hr>
                                        Payslip: {{convartMonthAndYearToWord($salaryDetails['salaryDetails']->month_of_salary)}}
                                    </strong></h3>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <table>
                                <tbody>
                                <tr>
                                    <td>Employee Name:</td>
                                    <td class="text-center">
                                        <b>{{$salaryDetails['salaryDetails']->first_name}} {{$salaryDetails['salaryDetails']->last_name}}</b>
                                    </td>
                                </tr>
                                <tr>
                                    <td>@lang('employee.department') :</td>
                                    <td class="text-center"><b>{{$salaryDetails['salaryDetails']->department_name}}</b>
                                    </td>
                                </tr>
                                <tr>
                                    <td>@lang('employee.designation') :</td>
                                    <td class="text-center"><b>{{$salaryDetails['salaryDetails']->designation_name}}</b>
                                    </td>
                                </tr>
                                <tr>
                                    <td>@lang('employee.date_of_joining') :</td>
                                    <td class="text-center">
                                        <b>{{date(" d-M-Y", strtotime($salaryDetails['salaryDetails']->date_of_joining))}} </b>
                                    </td>
                                </tr>
                                <tr>
                                    <td>@lang('salary_sheet.basic_salary') :</td>
                                    <td class="text-center">{{number_format($salaryDetails['salaryDetails']->basic_salary)}}</td>
                                </tr>
                                <tr>
                                    <td>House Allowance:</td>
                                    <td class="text-center ">{{number_format($salaryDetails['salaryDetails']->house_allowance)}}</td>
                                </tr>
                                <tr>
                                    <td>Transport Allowance:</td>
                                    <td class="text-center ">{{$salaryDetails['salaryDetails']->transport_allowance}}</td>

                                </tr>

                                <tr>
                                    <td>Banking Allowance:</td>
                                    <td class="text-center ">{{$salaryDetails['salaryDetails']->banking_allowance}}</td>
                                </tr>
                                <tr>
                                    <td>Total Public Holidays Pay</td>
                                    <td class="text-center">{{$salaryDetails['salaryDetails']['public_holidays_pay']}}</td>
                                </tr>
                                {{--                                        Bonuses disply here--}}

                                @foreach($salaryDetails['salaryDetailsToBonuses'] as $bonus)
                                    <tr>
                                        <td>{{$bonus->bonus_name}}:</td>
                                        <td class="text-center"> {{number_format($bonus->amount_of_bonus)}}</td>
                                    </tr>
                                @endforeach


                                <!-- End of bonuses calculation-->
                                <!-- airtime untaxed -->
                                @if($salaryDetails['salaryDetails']['airtime_untaxed'] !=null)
                                    <tr>
                                        <td> Airtime(Bonus):</td>

                                        <td class="text-center">{{$salaryDetails['salaryDetails']['airtime_untaxed']}}   </td>

                                    </tr>
                                @endif


                                <tr>
                                    <td> Over time: ({{$salaryDetails['salaryDetails']->total_over_time_hour}})</td>

                                    <td class="text-center">{{$salaryDetails['salaryDetails']['total_overtime_amount']}}   </td>

                                </tr>
                                <tr>
                                    <td> PRO-RATA:</td>

                                    <td class="text-center">@if(!$salaryDetails['salaryDetails']['pro_rata'])
                                            0 @else {{$salaryDetails['salaryDetails']['pro_rata']}} @endif   </td>

                                </tr>

                                {{--								@if(count($salaryDetailsToAllowance) > 0)--}}
                                {{--									@foreach($salaryDetailsToAllowance as $allowance)--}}
                                {{--										<tr>--}}
                                {{--											<td>{{$allowance->allowance_name}}: </td>--}}
                                {{--											<td class="text-center"> {{number_format($allowance->amount_of_allowance)}}</td>--}}
                                {{--										</tr>--}}
                                {{--									@endforeach--}}
                                {{--								@endif--}}


                                <tr>
                                    <td>@lang('salary_sheet.gross_salary') :</td>
                                    <td class="text-center"
                                        style="background: #ddd"> {{number_format($salaryDetails['salaryDetails']->gross_pay)}}</td>
                                </tr>

                                {{--								SS--}}

                                </tbody>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table">
                                <tbody>
                                <tr>
                                    <td>Payroll No. :</td>
                                    <td><b>{{$salaryDetails['salaryDetails']->payroll_no}}</b></td>
                                </tr>
                                <tr>
                                    <td>@lang('common.month') :</td>
                                    <td>
                                        <b>{{convartMonthAndYearToWord($salaryDetails['salaryDetails']->month_of_salary)}}</b>
                                    </td>
                                </tr>
                                <tr>
                                    <td>@lang('common.date') :</td>
                                    <td><b>{{date(" d-M-Y", strtotime(date('Y-m-d')))}} </b></td>
                                </tr>
                                <tr>
                                    <td>@lang('salary_sheet.number_of_working_days') :</td>
                                    <td class="text-center">
                                        <b>{{$salaryDetails['salaryDetails']->total_working_days}}</b></td>
                                </tr>
                                <tr>
                                    <td>  @lang('salary_sheet.number_of_worked_in_them_month') :</td>
                                    <td class="text-center">   {{$salaryDetails['salaryDetails']->total_present+$salaryDetails['salaryDetails']->no_of_holidays_worked}}   </td>
                                </tr>

                                <tr>
                                    <td>  @lang('salary_sheet.per_day_salary') :</td>
                                    <td class="text-center">   {{number_format($salaryDetails['salaryDetails']->per_day_salary)}}   </td>
                                </tr>
                                @if($salaryDetails['salaryDetails']->total_late !=0)
                                    <tr>
                                        <td>  @lang('salary_sheet.salary_deduction_for_late_attendance') :</td>
                                        <td class="text-center">   {{$salaryDetails['salaryDetails']->total_late}}   </td>
                                    </tr>
                                @endif

                                @if(count($salaryDetails['salaryDetailsToLeave']) > 0)
                                    <tr>
                                        <td> Total leaves days :</td>
                                        <td class="text-center">   {{$salaryDetails['salaryDetails']->total_leave}}   </td>
                                    </tr>
                                @endif
                                <tr style="background: #ddd">
                                    <td>@lang('salary_sheet.taxable_salary') :</td>
                                    <td class="text-center"> {{number_format($salaryDetails['salaryDetails']->taxable_salary)}}</td>
                                </tr>
                                <tr>
                                    <td>P.A.Y.E :</td>
                                    <td class="text-center"> {{number_format($salaryDetails['salaryDetails']->tax)}}</td>
                                </tr>
                                @php
                                    $companyTaxDeduction = 0;
                                    $companyTaxDeduction = ($salaryDetails['salaryDetails']->tax * 70) / 100;

                                    $employeeTaxDeduction = 0;
                                    $employeeTaxDeduction = ($salaryDetails['salaryDetails']->tax * 30) / 100;
                                @endphp
                                {{--                                <tr>--}}
                                {{--                                    <td>@lang('salary_sheet.company_tax_deduction') :</td>--}}
                                {{--                                    <td class="text-center"> {{number_format(round($companyTaxDeduction))}}</td>--}}
                                {{--                                </tr>--}}
                                {{--                                <tr>--}}
                                {{--                                    <td>@lang('salary_sheet.employee_tax_payable'):</td>--}}
                                {{--                                    <td class="text-center"> {{number_format(round($employeeTaxDeduction))}}</td>--}}
                                {{--                                </tr>--}}
                                @if(count($salaryDetails['salaryDetailsToDeduction']) > 0)
                                    @foreach($salaryDetails['salaryDetailsToDeduction'] as $deduction)
                                        <tr>
                                            <td>{{$deduction->deduction_name}} :</td>
                                            <td class="text-center"> {{number_format($deduction->amount_of_deduction)}}</td>
                                        </tr>
                                    @endforeach
                                @endif

                                {{-- Loan Deductions --}}
                                @if(isset($salaryDetails['loanDeductions']) && count($salaryDetails['loanDeductions']) > 0)
                                    @foreach($salaryDetails['loanDeductions'] as $loan)
                                        <tr>
                                            <td>Loan - {{$loan->name}} :</td>
                                            <td class="text-center"> {{number_format($loan->amount)}}</td>
                                        </tr>
                                    @endforeach
                                @endif
                                <tr>
                                    <td>NSSF:</td>
                                    <td class="text-center">{{$salaryDetails['salaryDetails']->nssf_amount}}</td>
                                </tr>
                                <tr>
                                    <td>NHIF:</td>
                                    <td class="text-center">{{$salaryDetails['salaryDetails']->nhifRate}}</td>
                                </tr>
                                @if($salaryDetails['salaryDetails']->total_late_amount !=0)
                                    <tr>
                                        <td>@lang('salary_sheet.late_amount') :</td>
                                        <td class="text-center"> {{number_format($salaryDetails['salaryDetails']->total_late_amount)}}</td>
                                    </tr>
                                @endif
                                @if($salaryDetails['salaryDetails']->total_absence_amount !=0)
                                    <tr>
                                        <td>@lang('salary_sheet.absence_amount') (  {{$salaryDetails['salaryDetails']->total_absence}} days):</td>
                                        <td class="text-center"> {{number_format($salaryDetails['salaryDetails']->total_absence_amount)}}</td>
                                    </tr>
                                @endif
                                {{--                                removed on 01/06/2021 by request from elosi@if($salaryDetails['salaryDetails']->total_overtime_amount != 0)--}}
                                {{--                                    <tr>--}}
                                {{--                                        <td>@lang('salary_sheet.over_time') :</td>--}}
                                {{--                                        <td class="text-center"> {{number_format($salaryDetails['salaryDetails']->total_overtime_amount)}}</td>--}}
                                {{--                                    </tr>--}}
                                {{--                                @endif--}}
                                <tr>
                                    <td> @lang('salary_sheet.net_salary_to_be_paid') :</td>
                                    <td class="text-center"
                                        style="background: #ddd">  {{number_format($salaryDetails['salaryDetails']->net_salary)}}   </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="clearFix" style=" margin-bottom: 15px;">
                        <div class="col-md-4" style="text-align: center;">
                            <strong>@lang('salary_sheet.adminstrator_signature') ...............</strong>
                        </div>
                        <div class=" col-md-4" style="text-align: center;">
                            {{--                            <strong>@lang('common.date') ...</strong>--}}
                        </div>
                        <div class=" col-md-4" style="text-align: center;">
                            {{--                            <strong>@lang('salary_sheet.employee_signature') ...</strong>--}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @if($counter % 2 == 0)
            <div class="breakNow"></div>
        @endif
        {{$counter++}}
    @endforeach
</div>
</div>
</body>
</html>

