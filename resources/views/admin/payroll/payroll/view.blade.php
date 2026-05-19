@extends('admin.master')

@section('title')
    Payroll Record
@endsection
@section('content')
<div class="container-fluid">
    <div class="row bg-title">
        <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
            <ol class="breadcrumb">
                <li class="active breadcrumbColor"><a href="{{ url('dashboard') }}"><i
                            class="fa fa-home"></i> @lang('dashboard.dashboard')</a></li>
                <li>@yield('title')</li>
            </ol>
        </div>
        <div class="col-lg-9 col-sm-8 col-md-8 col-xs-12">

            <a href="{{route('payrollDataExport')}}" class="btn btn-success pull-right m-l-20 waves-effect waves-light">
                <i class="fa fa-plus-circle" aria-hidden="true"></i>Download Excel</a>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12">
            <div class="panel panel-info">
                <div class="panel-heading"><i class="mdi mdi-table fa-fw"></i> @yield('title')</div>
                <div class="panel-wrapper collapse in" aria-expanded="true">
                    <div class="panel-body">
                        @if(session()->has('success'))
                            <div class="alert alert-success alert-dismissable">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                                <i class="cr-icon glyphicon glyphicon-ok"></i>&nbsp;<strong>{{ session()->get('success') }}</strong>
                            </div>
                        @endif
                        @if(session()->has('error'))
                            <div class="alert alert-danger alert-dismissable">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                                <i class="glyphicon glyphicon-remove"></i>&nbsp;<strong>{{ session()->get('error') }}</strong>
                            </div>
                        @endif
                        <div class="table-responsive">

                            <table id="myTablePayrollDetails" class="table table-bordered">
                                <thead>

                                <tr class="tr_header">
                                    <th>@lang('common.serial')</th>
                                    <th>Month</th>
                                    <th>Payroll Number</th>
                                    <th>Name</th>
                                    <th>JD</th>
                                    <th>Basic</th>
                                    <th>H.A</th>
                                    <th>T.A</th>
                                    <th>Overtime</th>
                                    <th>Bonuses</th>
                                    <th>Airtime-non-taxable</th>
                                    <th>Pro-rata</th>
                                    <th>Public Holiday</th>
                                    <th>B/A</th>
                                    <th>Gross</th>
                                    <th>Lost Days</th>
                                    <th>Lost Days Amount</th>
                                    <th>Total Advance</th>
                                    <th>NHIF</th>
                                    <th>NSSF</th>
                                    <th>PAYE</th>
                                    <th>Net Pay</th>
                                    <th>Sign</th>

                                </tr>
                                </thead>
                                <tbody>
                                {!! $sl=null !!} <h4>Total records: {{count($results)}} as at {{date('H:i d-M-Y')}}</h4>
                                @foreach($results AS $value)
                                    <tr class="">
                                        <td style="width: 100px;">{!! ++$sl !!}</td>
                                        <td>{!! $value->month_of_salary !!}</td>
                                        <td>{{$value->payroll_no}}</td>
                                        <td>{{$value->employee->first_name}} {{$value->employee->last_name}}</td>
                                        <td>{{$value->name}}</td>
                                        <td>{{$value->basic_salary}}</td>
                                        <td>{{$value->house_allowance}}</td>
                                        <td>{{$value->transport_allowance}}</td>
                                        <td>{{$value->total_overtime_amount}}</td>
                                        <td>{{$value->salaryDetailsToBonuses1}}</td>
                                        <td>{{$value->airtime_untaxed}}</td>
                                        <td>{{$value->pro_rata}}</td>
                                        <td>{{$value->public_holidays_pay}}</td>
                                        <td>{{$value->banking_allowance}}</td>
                                        <td>{{$value->gross_salary}}</td>
                                        <td>{{$value->total_absence}}</td>
                                        <td>{{$value->total_absence_amount}}</td>
                                        <td>{{$value->total_advances}}</td>
                                        <td>{{$value->nhifRate}}</td>
                                        <td>{{$value->nssf_amount}}</td>
                                        <td>{{$value->PAYE_tax}}</td>
                                        <td>{{$value->net_salary}}</td>
                                        <td></td>


                                    </tr>
                                @endforeach

                                </tbody>

                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
