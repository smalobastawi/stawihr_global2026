@extends('admin.master')
@section('content')
    @section('title')
        Report - Employee movements
    @endsection
    <div class="container-fluid">
        <div class="row bg-title">
            <div class="col-lg-5 col-md-5 col-sm-5 col-xs-12">
                <ol class="breadcrumb">
                    <li class="active breadcrumbColor"><a href="{{ url('dashboard') }}"><i class="fa fa-home"></i> @lang('dashboard.dashboard')</a></li>
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
                                        <th>@lang('common.employee_name')</th>
                                        <th>Date of movement</th>
                                        <th>Old department</th>
                                        <th>New department</th>
                                        <th>Old role</th>
                                        <th>New role</th>
                                        <th>JOb Group</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    {!! $sl=null !!}
                                    @foreach($results AS $value)
                                        <tr class="{!! $value->promotion_id !!}">
                                            <td style="width: 100px;">{!! ++$sl !!}</td>
                                            <td>@if(isset($value->employee->first_name)) {{ $value->employee->first_name }} {{ $value->employee->last_name }}@endif</td>
                                            <td>{!! dateConvertDBtoForm($value->movement_date) !!}</td>
                                            <td>
                                                @if(isset($value->currentDepartment->department_name)) {{ $value->currentDepartment->department_name }}@endif

                                            </td>
                                            <td>
                                                @if(isset($value->newDepartment->department_name)) {{ $value->newDepartment->department_name }}@endif
                                            </td>
                                            <td>
                                                @if(isset($value->currentDesignation->designation_name)) {{ $value->currentDesignation->designation_name }}@endif
                                            </td>
                                            <td>
                                                @if(isset($value->newDesignation->designation_name)) {{ $value->newDesignation->designation_name }}@endif
                                            </td>
                                            <td>
                                                @if(isset($value->currentDepartment->department_name)) {{ $value->currentDepartment->department_name }}@endif
                                                <b>To</b>
                                                @if(isset($value->newDepartment->department_name)) {{ $value->newDepartment->department_name }}@endif
                                            </td>
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
