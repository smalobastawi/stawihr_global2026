@extends('admin.master')
@section('content')
@section('title')
    @lang('work_shift.work_shift')
@endsection
    <div class="container-fluid">
        <div class="row bg-title">
            <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
               <ol class="breadcrumb">
                    <li class="active breadcrumbColor"><a href="{{ url('dashboard') }}"><i class="fa fa-home"></i> @lang('dashboard.dashboard')</a></li>
                    <li>@yield('title')</li>
                </ol>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">
                <div class="panel panel-info">
                    <div class="panel-heading"><i class="mdi mdi-clock fa-fw"></i> @lang('work_shift.my_work_shift')</div>
                    <div class="panel-wrapper collapse in" aria-expanded="true">
                        <div class="panel-body">
                            @if($workShift)
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label col-md-4"><strong>@lang('work_shift.shift_name'):</strong></label>
                                            <div class="col-md-8">
                                                <p class="form-control-static">{{ $workShift->shift_name }}</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label col-md-4"><strong>@lang('common.employee_name'):</strong></label>
                                            <div class="col-md-8">
                                                <p class="form-control-static">{{ $employee->full_name }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <hr>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label col-md-4"><strong>@lang('work_shift.start_time'):</strong></label>
                                            <div class="col-md-8">
                                                <p class="form-control-static">
                                                    <span class="label label-info">
                                                        <i class="fa fa-clock-o"></i>
                                                        {{ date('h:i A', strtotime($workShift->start_time)) }}
                                                    </span>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label col-md-4"><strong>@lang('work_shift.end_time'):</strong></label>
                                            <div class="col-md-8">
                                                <p class="form-control-static">
                                                    <span class="label label-info">
                                                        <i class="fa fa-clock-o"></i>
                                                        {{ date('h:i A', strtotime($workShift->end_time)) }}
                                                    </span>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label col-md-4"><strong>@lang('work_shift.late_count_time'):</strong></label>
                                            <div class="col-md-8">
                                                <p class="form-control-static">
                                                    <span class="label label-warning">
                                                        <i class="fa fa-exclamation-circle"></i>
                                                        {{ date('h:i A', strtotime($workShift->late_count_time)) }}
                                                    </span>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label col-md-4"><strong>@lang('work_shift.overtime_count_time'):</strong></label>
                                            <div class="col-md-8">
                                                <p class="form-control-static">
                                                    <span class="label label-success">
                                                        <i class="fa fa-plus-circle"></i>
                                                        {{ date('h:i A', strtotime($workShift->overtime_count_time)) }}
                                                    </span>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <hr>

                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="alert alert-info" role="alert">
                                            <i class="fa fa-info-circle"></i>
                                            <strong>@lang('work_shift.shift_info_title')</strong>
                                            <ul style="margin-top: 10px;">
                                                <li>@lang('work_shift.shift_info_late')</li>
                                                <li>@lang('work_shift.shift_info_overtime')</li>
                                                <li>@lang('work_shift.shift_info_contact')</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="alert alert-warning" role="alert">
                                    <i class="fa fa-exclamation-triangle"></i>
                                    <strong>@lang('work_shift.no_shift_assigned')</strong>
                                    <p>@lang('work_shift.contact_hr_shift')</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
