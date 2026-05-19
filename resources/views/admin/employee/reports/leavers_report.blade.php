@extends('admin.master')
@section('content')
    @section('title')
        Employee - Leavers report
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
                                        <th>Payroll No.</th>
                                        <th>National Id</th>
                                        <th>Date of leaving</th>
                                        <th>Created By</th>
                                        <th>@lang('common.status')</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    {!! $sl=null !!}
                                    @foreach($results AS $value)
                                        <tr class="{!! $value->termination_id !!}">
                                            <td style="width: 100px;">{!! ++$sl !!}</td>
                                            <td>@if(isset($value->employee->first_name )) {{ $value->employee->first_name }} {{ $value->employee->last_name }} @endif</td>
                                            <td>{!! $value->employee->payroll_number !!}</td>
                                            <td>{!! $value->employee->national_id !!}</td>
                                            <td>{!! dateConvertDBtoForm($value->date_of_movement) !!}</td>
                                            <td>@if(isset($value->createdBy->first_name )) {{ $value->createdBy->first_name }} {{ $value->createdBy->last_name }} @endif</td>
                                            <td>
                                                @if($value->status == 1)
                                                    <span class="label label-info">Pending</span>
                                                @else
                                                    <span class="label label-success">Approved</span>
                                                @endif
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
