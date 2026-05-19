@extends('admin.master')
@section('content')
@section('title')
    @lang('leave.rollover_leaves')
@endsection

<div class="container-fluid">
    <div class="row bg-title">
        <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
            <ol class="breadcrumb">
                <li class="active breadcrumbColor"><a href="{{ url('dashboard') }}"><i class="fa fa-home"></i>
                        @lang('dashboard.dashboard')</a></li>
                <li>@yield('title')</li>
            </ol>
        </div>
        <div class="col-lg-9 col-sm-8 col-md-8 col-xs-12">
            <a href="{{ route('addRolloverLeave1') }}"
                class="btn btn-success pull-right m-l-20 hidden-xs hidden-sm waves-effect waves-light">
                <i class="fa fa-plus-circle" aria-hidden="true"></i> @lang('leave.add_rollover_leave')
            </a>
            <a href="{{ route('updateDefaultRollovers') }}"
                class="btn btn-info pull-right m-l-20 hidden-xs hidden-sm waves-effect waves-light">
                <i class="fa fa-refresh" aria-hidden="true"></i> @lang('leave.update_default_rollovers')
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12">
            <div class="panel panel-info">
                <div class="panel-heading"><i class="mdi mdi-table fa-fw"></i> @yield('title')</div>
                <div class="panel-wrapper collapse in" aria-expanded="true">
                    <div class="panel-body">
                        @if (session()->has('success'))
                            <div class="alert alert-success alert-dismissable">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                                <i
                                    class="cr-icon glyphicon glyphicon-ok"></i>&nbsp;<strong>{{ session()->get('success') }}</strong>
                            </div>
                        @endif
                        @if (session()->has('error'))
                            <div class="alert alert-danger alert-dismissable">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                                <i
                                    class="glyphicon glyphicon-remove"></i>&nbsp;<strong>{{ session()->get('error') }}</strong>
                            </div>
                        @endif
                        <div class="table-responsive">
                            <table id="myTable" class="table table-bordered">
                                <thead class="tr_header">
                                    <tr>
                                        <th>@lang('common.serial')</th>
                                        <th>@lang('employee.name')</th>
                                        <th>@lang('employee.department')</th>
                                        <th>@lang('employee.designation')</th>
                                        <th>Leave Type</th>
                                        <th>Previous Fiscal Year</th>
                                        <th>Fiscal Year</th>
                                        <th>Days Rolled Over</th>
                                        <th>@lang('common.status')</th>
                                        <th style="text-align: center;">@lang('common.action')</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $sl = 1 @endphp
                                    @foreach ($rolloverLeaves as $rolloverLeave)
                                        <tr>
                                            <td>{{ $sl++ }}</td>
                                            <td>{{ optional($rolloverLeave->employee)->fullName() ?? 'N/A' }}</td>
                                            <td>{{ optional($rolloverLeave->employee->department)->department_name ?? 'N/A' }}
                                            </td>
                                            <td>{{ optional($rolloverLeave->employee->designation)->designation_name ?? 'N/A' }}
                                            </td>
                                            <td>{{ optional($rolloverLeave->leaveType)->leave_type_name ?? 'N/A' }}</td>
                                            <td>{{ optional($rolloverLeave->previousFiscalYear)->name ?? 'N/A' }}</td>
                                            <td>{{ optional($rolloverLeave->fiscalYear)->name ?? 'N/A' }}</td>
                                            <td>{{ $rolloverLeave->days_requested ?? 0 }}</td>
                                            <td>{{ $rolloverLeave->final_status == 2 ? __('common.approved') : __('common.pending') }}
                                            </td>
                                            <td style="width: 150px;">
                                                <a href="{{ route('employee.show', $rolloverLeave->employee_id) }}"
                                                    class="btn btn-primary btn-xs btnColor" title="@lang('common.view')">
                                                    <i class="glyphicon glyphicon-th-large" aria-hidden="true"></i>
                                                </a>
                                                @if ($rolloverLeave->final_status == 2)
                                                    <a href="{!! route('rolloverLeave.delete', $rolloverLeave->id) !!}"
                                                        data-token="{!! csrf_token() !!}"
                                                        data-id="{!! $rolloverLeave->id !!}"
                                                        class="btnColor delete btn btn-danger btn-xs deleteBtn"><i
                                                            class="fa fa-trash-o" aria-hidden="true"></i></a>
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
