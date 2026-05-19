@extends('admin.master')
@section('content')
@section('title')
    User Report
@endsection

<div class="container-fluid">
    <div class="row bg-title">
        <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
            <ol class="breadcrumb">
                <li class="active breadcrumbColor"><a href="{{ url('dashboard') }}"><i
                                class="fa fa-home"></i> @lang('dashboard.dashboard')</a></li>
                <li>@yield('title')</li>
            </ol>
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

                            <table id="myTable" class="table table-bordered">
                                <thead>

                                <tr class="tr_header">
                                    <th>@lang('common.serial')</th>
                                    <th>@lang('deduction.employee_name')</th>
                                    <th>Payroll Number</th>
                                    <th>Email</th>
                                    <th>Date joined</th>
                                   
                                    
                                </tr>
                                </thead>
                                <tbody>
                                {!! $sl=null !!} <h4>Total active users : {{count($results)}} as at {{date('H:i d-M-Y')}}</h4>
                                @foreach($results AS $value)
                                    <tr class="">
                                        <td style="width: 100px;">{!! ++$sl !!}</td>
                                        <td><a href="{!! route('employee.show',$value->employee_id  ) !!}">{!! $value->first_name !!}&nbsp; {!! $value->middle_name !!} {!! $value->last_name !!}</a></td>

                                        <td>{{$value->payroll_number}}</td>
                                        <td>{{$value->email}}</td>
                                        <td>{{\Carbon::parse($value->date_of_joining)->format('d-m-Y')}}</td>
                                      
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
