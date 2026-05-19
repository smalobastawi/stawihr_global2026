@extends('admin.master')
@section('content')
@section('title')
    @if (isset($editModeData))
        Edit Leave Schedule
    @else
        Add Leave Schedule
    @endif
@endsection

<style>
    .schedule-form-card {
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
    }
    .section-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 20px 25px;
        border-radius: 8px 8px 0 0;
    }
    .required-mark {
        color: #dc3545;
    }
</style>

<div class="container-fluid">
    <div class="row bg-title">
        <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
            <ol class="breadcrumb">
                <li class="active breadcrumbColor">
                    <a href="{{ url('dashboard') }}"><i class="fa fa-home"></i> @lang('dashboard.dashboard')</a>
                </li>
                <li><a href="{{ route('leave.schedule.index') }}">Leave Schedule</a></li>
                <li>@yield('title')</li>
            </ol>
        </div>
        <div class="col-lg-9 col-md-8 col-sm-8 col-xs-12">
            <a href="{{ route('leave.schedule.index') }}" class="btn btn-success pull-right m-l-20">
                <i class="fa fa-list-ul"></i> View Schedules
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="schedule-form-card">
                <div class="section-header">
                    <h4 style="margin: 0;"><i class="mdi mdi-calendar-plus fa-fw"></i> @yield('title')</h4>
                </div>

                <div class="panel-body" style="padding: 30px;">
                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible" role="alert">
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">×</span>
                            </button>
                            @foreach ($errors->all() as $error)
                                <strong>{!! $error !!}</strong><br>
                            @endforeach
                        </div>
                    @endif

                    @if (isset($editModeData))
                        <form action="{{ route('leave.schedule.update', $schedule->id) }}" method="POST" class="form-horizontal">
                            @csrf
                            @method('PUT')
                    @else
                        <form action="{{ route('leave.schedule.store') }}" method="POST" class="form-horizontal">
                            @csrf
                    @endif

                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label class="control-label col-md-4">
                                        Employee <span class="required-mark">*</span>
                                    </label>
                                    <div class="col-md-8">
                                        <select name="employee_id" class="form-control select2" required>
                                            <option value="">Select Employee</option>
                                            @foreach ($employees as $employee)
                                                <option value="{{ $employee->employee_id }}"
                                                    {{ old('employee_id', isset($schedule) ? $schedule->employee_id : '') == $employee->employee_id ? 'selected' : '' }}>
                                                    {{ $employee->first_name }} {{ $employee->last_name }}
                                                    ({{ $employee->payroll_number }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label class="control-label col-md-4">
                                        Leave Type <span class="required-mark">*</span>
                                    </label>
                                    <div class="col-md-8">
                                        <select name="leave_type_id" class="form-control select2" required>
                                            <option value="">Select Leave Type</option>
                                            @foreach ($leaveTypes as $leaveType)
                                                <option value="{{ $leaveType->leave_type_id }}"
                                                    {{ old('leave_type_id', isset($schedule) ? $schedule->leave_type_id : '') == $leaveType->leave_type_id ? 'selected' : '' }}>
                                                    {{ $leaveType->leave_type_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label class="control-label col-md-4">
                                        From Date <span class="required-mark">*</span>
                                    </label>
                                    <div class="col-md-8">
                                        <div class="input-group">
                                            <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                            <input type="text" name="scheduled_from_date"
                                                class="form-control dateField"
                                                value="{{ old('scheduled_from_date', isset($schedule) ? dateConvertDBtoForm($schedule->scheduled_from_date) : '') }}"
                                                placeholder="DD/MM/YYYY" readonly="readonly" required>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label class="control-label col-md-4">
                                        To Date <span class="required-mark">*</span>
                                    </label>
                                    <div class="col-md-8">
                                        <div class="input-group">
                                            <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                            <input type="text" name="scheduled_to_date"
                                                class="form-control dateField"
                                                value="{{ old('scheduled_to_date', isset($schedule) ? dateConvertDBtoForm($schedule->scheduled_to_date) : '') }}"
                                                placeholder="DD/MM/YYYY" readonly="readonly" required>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label class="control-label col-md-4">Purpose</label>
                                    <div class="col-md-8">
                                        <textarea name="purpose" class="form-control" rows="3">{{ old('purpose', isset($schedule) ? $schedule->purpose : '') }}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label class="control-label col-md-4">Remarks</label>
                                    <div class="col-md-8">
                                        <textarea name="remarks" class="form-control" rows="2">{{ old('remarks', isset($schedule) ? $schedule->remarks : '') }}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>

                        @if (isset($editModeData))
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="form-group">
                                        <label class="control-label col-md-4">
                                            Status <span class="required-mark">*</span>
                                        </label>
                                        <div class="col-md-8">
                                            <select name="status" class="form-control select2" required>
                                                <option value="scheduled" {{ $schedule->status == 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                                                <option value="applied" {{ $schedule->status == 'applied' ? 'selected' : '' }}>Applied</option>
                                                <option value="cancelled" {{ $schedule->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                                <option value="completed" {{ $schedule->status == 'completed' ? 'selected' : '' }}>Completed</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <div class="form-actions" style="margin-top: 30px;">
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="col-md-offset-4 col-md-8">
                                        @if (isset($editModeData))
                                            <button type="submit" class="btn btn-info btn_style">
                                                <i class="fa fa-pencil"></i> Update Schedule
                                            </button>
                                        @else
                                            <button type="submit" class="btn btn-info btn_style">
                                                <i class="fa fa-check"></i> Save Schedule
                                            </button>
                                        @endif
                                        <a href="{{ route('leave.schedule.index') }}" class="btn btn-default" style="margin-left: 10px;">
                                            Cancel
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
