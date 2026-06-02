@extends('admin.master')

@section('title', trans('leave.leave_application_form'))

@section('content')
    <style>
        .datepicker table tr td.disabled,
        .datepicker table tr td.disabled:hover {
            background: none;
            color: red !important;
            cursor: default;
        }

        td {
            color: black !important;
        }
    </style>

    <div class="container-fluid">
        <div class="row bg-title">
            <div class="col-lg-5 col-md-5 col-sm-5 col-xs-12">
                <ol class="breadcrumb">
                    <li class="active breadcrumbColor">
                        <a href="#">
                            <i class="fa fa-home"></i> @lang('dashboard.dashboard')
                        </a>
                    </li>
                    <li>@yield('title')</li>
                </ol>
            </div>

            <div class="col-lg-7 col-md-7 col-sm-7 col-xs-12">
                @if (isset($ess))
                    <a href="{{ route('ess.leave.index') }}"
                       class="btn btn-success pull-right m-l-20 hidden-xs hidden-sm waves-effect waves-light">
                        <i class="fa fa-list-ul" aria-hidden="true"></i>
                        @lang('leave.view_leave_applicaiton')
                    </a>
                @else
                    <a href="{{ route('applyForLeave.index') }}"
                       class="btn btn-success pull-right m-l-20 hidden-xs hidden-sm waves-effect waves-light">
                        <i class="fa fa-list-ul" aria-hidden="true"></i>
                        @lang('leave.view_leave_applicaiton')
                    </a>
                @endif
            </div>
        </div>

        <div class="row">
            <div class="col-md-offset-2 col-md-6">
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

                @if (session()->has('success'))
                    <div class="alert alert-success alert-dismissable">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                        <i class="cr-icon glyphicon glyphicon-ok"></i>&nbsp;
                        <strong>{{ session()->get('success') }}</strong>
                    </div>
                @endif

                @if (session()->has('error'))
                    <div class="alert alert-danger alert-dismissable">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                        <i class="glyphicon glyphicon-remove"></i>&nbsp;
                        <strong>{{ session()->get('error') }}</strong>
                    </div>
                @endif
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-info">
                    <div class="panel-heading">
                        <i class="mdi mdi-clipboard-text fa-fw"></i>
                        @lang('leave.leave_application_form')
                    </div>

                    <div class="panel-wrapper collapse in" aria-expanded="true">
                        <div class="panel-body">
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

                            @if (session()->has('success'))
                                <div class="alert alert-success alert-dismissable">
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                                    <i class="cr-icon glyphicon glyphicon-ok"></i>&nbsp;
                                    <strong>{{ session()->get('success') }}</strong>
                                </div>
                            @endif

                            @if (session()->has('error'))
                                <div class="alert alert-danger alert-dismissable">
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                                    <strong>{{ session()->get('error') }}</strong>
                                </div>
                            @endif

                            @php
                                $postUrl = !isset($ess)
                                    ? 'applyForLeave.store'
                                    : 'ess.leave.apply.store';
                            @endphp

                            <form action="{{ route($postUrl) }}"
                                  method="POST"
                                  enctype="multipart/form-data"
                                  id="leaveApplicationForm">
                                @csrf

                                <div class="form-body">
                                    <div class="row">
                                        <div class="col-md-4">
                                            @role('Super-Admin')
                                                <div class="form-group">
                                                    <label for="employee_id">
                                                        @lang('common.employee_name')
                                                        <span class="validateRq">*</span>
                                                    </label>

                                                    <select name="employee_id"
                                                            id="employee_id"
                                                            class="form-control employee_id select2 required">
                                                        @foreach ($employeeList as $key => $value)
                                                            <option value="{{ $key }}"
                                                                {{ old('employee_id') == $key ? 'selected' : '' }}>
                                                                {{ $value }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            @else
                                                <input type="hidden"
                                                       name="employee_id"
                                                       class="employee_id"
                                                       value="{{ isset($getEmployeeInfo) ? $getEmployeeInfo->employee_id : '' }}">

                                                <div class="form-group">
                                                    <label for="employee_name">
                                                        @lang('common.employee_name')
                                                        <span class="validateRq">*</span>
                                                    </label>

                                                    <input type="text"
                                                           id="employee_name"
                                                           class="form-control"
                                                           readonly
                                                           value="{{ isset($getEmployeeInfo) ? $getEmployeeInfo->first_name . ' ' . $getEmployeeInfo->last_name : '' }}">
                                                </div>
                                            @endrole
                                        </div>

                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="leave_type_id">
                                                    @lang('leave.leave_type')
                                                    <span class="validateRq">*</span>
                                                </label>

                                                <select name="leave_type_id"
                                                        id="leave_type_id"
                                                        class="form-control leave_type_id select2 required">
                                                    <option value="">Select an option</option>

                                                    @foreach ($leaveTypeList->toArray() as $key => $value)
                                                        <option value="{{ $key }}"
                                                            {{ old('leave_type_id') == $key ? 'selected' : '' }}>
                                                            {{ $value }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="current_balance">
                                                    @lang('leave.current_balance')
                                                    <span class="validateRq">*</span>
                                                </label>

                                                <input type="text"
                                                       id="current_balance"
                                                       class="form-control current_balance"
                                                       readonly
                                                       placeholder="0"
                                                       value="">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-4">
                                            <label for="application_from_date">
                                                @lang('common.from_date')
                                                <span class="validateRq">*</span>
                                            </label>

                                            <div class="input-group">
                                                <span class="input-group-addon">
                                                    <i class="fa fa-calendar"></i>
                                                </span>

                                                <input type="text"
                                                       name="application_from_date"
                                                       id="application_from_date"
                                                       class="form-control application_from_date"
                                                       readonly
                                                       placeholder="{{ __('common.from_date') }}"
                                                       value="{{ old('application_from_date') }}">
                                            </div>
                                        </div>

                                        <div class="col-md-4">
                                            <label for="application_to_date">
                                                @lang('common.to_date')
                                                <span class="validateRq">*</span>
                                            </label>

                                            <div class="input-group">
                                                <span class="input-group-addon">
                                                    <i class="fa fa-calendar"></i>
                                                </span>

                                                <input type="text"
                                                       name="application_to_date"
                                                       id="application_to_date"
                                                       class="form-control application_to_date"
                                                       required
                                                       readonly
                                                       placeholder="{{ __('common.to_date') }}"
                                                       value="{{ old('application_to_date') }}">
                                            </div>
                                        </div>

                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="number_of_day">
                                                    Number of Days
                                                    <span class="validateRq">*</span>
                                                </label>

                                                <input type="text"
                                                       name="number_of_day"
                                                       id="number_of_day"
                                                       class="form-control number_of_day"
                                                       readonly
                                                       placeholder="{{ __('leave.number_of_day') }}"
                                                       value="">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="purpose">
                                                    @lang('leave.purpose') (Optional)
                                                </label>

                                                <textarea name="purpose"
                                                          id="purpose"
                                                          class="form-control purpose"
                                                          placeholder="{{ __('leave.purpose') }}"
                                                          cols="30"
                                                          rows="3">{{ old('purpose') }}</textarea>
                                            </div>
                                        </div>

                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="exampleInput">
                                                    Approval Supervisor: (
                                                    @isset($getEmployeeInfo)
                                                        {{ optional($getEmployeeInfo->supervisor)->first_name }}
                                                        {{ optional($getEmployeeInfo->supervisor)->middle_name }}
                                                        {{ optional($getEmployeeInfo->supervisor)->last_name }}
                                                    @endisset
                                                    )<br>
                                                </label>

                                                <br>

                                                <label for="exampleInput">
                                                    P&C In Charge: (
                                                    @isset($getEmployeeInfo)
                                                        @if ($getEmployeeInfo->getLocationLeaveApprovers()->isNotEmpty())
                                                            <ul>
                                                                @foreach ($getEmployeeInfo->getLocationLeaveApprovers() as $approver)
                                                                    <li>
                                                                        {{ $approver->first_name }}
                                                                        {{ $approver->middle_name }}
                                                                        {{ $approver->last_name }}
                                                                    </li>
                                                                @endforeach
                                                            </ul>
                                                        @endif
                                                    @endisset
                                                    )<br>
                                                </label>
                                            </div>
                                        </div>

                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="justification_file">
                                                    Upload Justification/Evidence(Optional)
                                                    <span class="validateRq"></span>
                                                </label>

                                                <input name="justification_file[]"
                                                       id="justification_file"
                                                       type="file"
                                                       accept=".jpeg,.pdf,.png,.gif,.docx"
                                                       multiple>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row"></div>
                                </div>

                                <div class="form-actions">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <button type="submit" id="formSubmit" class="btn btn-info">
                                                <i class="fa fa-paper-plane"></i>
                                                Send Application
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('page_scripts')

@endsection