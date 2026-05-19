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
                    <li class="active breadcrumbColor"><a href="#"><i class="fa fa-home"></i> @lang('dashboard.dashboard')
                        </a></li>
                    <li>@yield('title')</li>

                </ol>
            </div>
            <div class="col-lg-7 col-md-7 col-sm-7 col-xs-12">
                @if (isset($ess))
                    <a href="{{ route('ess.leave.index') }}"
                        class="btn btn-success pull-right m-l-20 hidden-xs hidden-sm waves-effect waves-light"><i
                            class="fa fa-list-ul" aria-hidden="true"></i> @lang('leave.view_leave_applicaiton')</a>
                @else
                    <a href="{{ route('applyForLeave.index') }}"
                        class="btn btn-success pull-right m-l-20 hidden-xs hidden-sm waves-effect waves-light"><i
                            class="fa fa-list-ul" aria-hidden="true"></i> @lang('leave.view_leave_applicaiton')</a>
                @endif
            </div>
        </div>

        <div class="row">
            <div class="col-md-offset-2 col-md-6">
                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                                aria-hidden="true">×</span></button>
                        @foreach ($errors->all() as $error)
                            <strong>{!! $error !!}</strong><br>
                        @endforeach
                    </div>
                @endif
                @if (session()->has('success'))
                    <div class="alert alert-success alert-dismissable">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                        <i class="cr-icon glyphicon glyphicon-ok"></i>&nbsp;<strong>{{ session()->get('success') }}</strong>
                    </div>
                @endif
                @if (session()->has('error'))
                    <div class="alert alert-danger alert-dismissable">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                        <i class="glyphicon glyphicon-remove"></i>&nbsp;<strong>{{ session()->get('error') }}</strong>
                    </div>
                @endif
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-info">
                    <div class="panel-heading"><i class="mdi mdi-clipboard-text fa-fw"></i>@lang('leave.leave_application_form')</div>
                    <div class="panel-wrapper collapse in" aria-expanded="true">
                        <div class="panel-body">
                            @if ($errors->any())
                                <div class="alert alert-danger alert-dismissible" role="alert">
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                                            aria-hidden="true">×</span></button>
                                    @foreach ($errors->all() as $error)
                                        <strong>{!! $error !!}</strong><br>
                                    @endforeach
                                </div>
                            @endif
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
                                    <strong>{{ session()->get('error') }}</strong>
                                </div>
                            @endif
                            @php
                                $postUrl = '';
                                if (!isset($ess)) {
                                    $postUrl = 'applyForLeave.store';
                                } else {
                                    $postUrl = 'ess.leave.apply.store';
                                }
                            @endphp
                            <form method="POST" action="{{ route($postUrl) }}" enctype="multipart/form-data" id="leaveApplicationForm">
                                @csrf

                            <div class="form-body">
                                <div class="row">

                                    <div class="col-md-4">
                                        @role('Super-Admin')
                                            <div class="form-group">
                                                <label for="exampleInput">@lang('common.employee_name')<span
                                                        class="validateRq">*</span></label>
                                                <select name="employee_id" class="form-control employee_id select2 required">
@foreach($employeeList as $__key => $__value)
<option value="{{ $__key }}" {{ (string)Request::old('employee_id') == (string)$__key ? 'selected' : '' }}>{{ $__value }}</option>
@endforeach
</select>
                                            </div>
                                        @else
                                            <input type="hidden" name="employee_id" value="{{ isset($getEmployeeInfo) ? $getEmployeeInfo->employee_id : '' }}" class="employee_id">

                                            <div class="form-group">
                                                <label for="exampleInput">@lang('common.employee_name')<span
                                                        class="validateRq">*</span></label>
                                                <input type="text" name="" value="{{ isset($getEmployeeInfo) ? $getEmployeeInfo->first_name . ' ' . $getEmployeeInfo->last_name : '' }}" class="form-control" readonly="readonly">
                                            </div>
                                        @endrole
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="exampleInput">
                                                @lang('leave.leave_type')<span class="validateRq">*</span>
                                            </label>
                                            <select name="leave_type_id" class="form-control leave_type_id select2 required">
<option value="">Select an option</option>
@foreach($leaveTypeList->toArray() as $__key => $__value)
<option value="{{ $__key }}" {{ (string)Request::old('leave_type_id') == (string)$__key ? 'selected' : '' }}>{{ $__value }}</option>
@endforeach
</select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="exampleInput">@lang('leave.current_balance')<span
                                                    class="validateRq">*</span></label>
                                            <input type="text" name="" value="{{ '' }}" class="form-control current_balance" readonly="readonly">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <label for="exampleInput">@lang('common.from_date')<span
                                                class="validateRq">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                            <input type="text" class="form-control application_from_date" name="application_from_date" value="{{ Request::old('application_from_date') }}">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="exampleInput">
                                            @lang('common.to_date')<span class="validateRq">*</span>
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                            <input type="text" class="form-control application_to_date" name="application_to_date" value="{{ Request::old('application_to_date') }}">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="exampleInput">Number of Days<span
                                                    class="validateRq">*</span></label>
                                            <input type="text" class="form-control number_of_day" name="number_of_day" value="{{ '' }}">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="exampleInput">@lang('leave.purpose') (Optional)</label>
                                            <textarea class="form-control" name="purpose">{{ Request::old('purpose') }}</textarea>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="exampleInput">
                                                Approval Supervisor: (
                                                @isset($getEmployeeInfo)
                                                    {{ $getEmployeeInfo->supervisor->first_name }}
                                                    {{ $getEmployeeInfo->supervisor->middle_name }}
                                                    {{ $getEmployeeInfo->supervisor->last_name }}
                                                @endisset
                                                )<br />
                                            </label><br>
                                           
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="exampleInput">Upload Justification/Evidence(Optional)<span
                                                class="validateRq"></span></label>
                                        <input class="form-control" name="justification_file[]" type="file"
                                            accept=".jpeg,.pdf, .png,.gif,.docx" multiple />
                                    </div>
                                </div>
                            </div>


                            <div class="row">

                            </div>
                        </div>
                        <div class="form-actions">
                            <div class="row">
                                <div class="col-md-6">
                                    <button type="submit" id="formSubmit" class="btn btn-info "><i
                                            class="fa fa-paper-plane"></i> Send Application
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
{{-- <script>
    jQuery(function() {

        $(document).on("focus", ".application_from_date", function() {
            $(this).datepicker({
                format: 'dd/mm/yyyy',
                todayHighlight: true,
                clearBtn: true,
                // startDate: new Date(),
            }).on('changeDate', function(e) {
                $(this).datepicker('hide');
            });
        });

        $(document).on("focus", ".application_to_date", function() {
            $(this).datepicker({
                format: 'dd/mm/yyyy',
                todayHighlight: true,
                clearBtn: true,
                // startDate: new Date(),
            }).on('changeDate', function(e) {
                $(this).datepicker('hide');
            });
        });

        $(document).on("change", ".application_from_date,.application_to_date  ", function() {

            var application_from_date = $('.application_from_date ').val();
            var application_to_date = $('.application_to_date ').val();
            var leave_type_id = $('.leave_type_id ').val();

            if (application_from_date != '' && application_to_date != '') {
                var action =
                    "{{ URL::to('leaveManagement/applyForLeave/applyForTotalNumberOfDays') }}";
                $.ajax({
                    type: 'POST',
                    url: action,
                    data: {
                        'application_from_date': application_from_date,
                        'application_to_date': application_to_date,
                        'leave_type_id': leave_type_id,

                        '_token': $('input[name=_token]').val()
                    },
                    dataType: 'json',
                    success: function(data) {
                        var currentBalance = $('.current_balance').val();
                        if (data > currentBalance) {
                            $.toast({
                                heading: 'Warning',
                                text: 'You have to apply ' + $('.current_balance')
                                    .val() + ' days!',
                                position: 'top-right',
                                loaderBg: '#ff6849',
                                icon: 'warning',
                                hideAfter: 3000,
                                stack: 6
                            });
                            $('body').find('#formSubmit').attr('disabled', true);
                            $('.number_of_day').val('');
                        } else if (data == 0) {
                            $.toast({
                                heading: 'Warning',
                                text: 'You can not apply for leave !',
                                position: 'top-right',
                                loaderBg: '#ff6849',
                                icon: 'warning',
                                hideAfter: 3000,
                                stack: 6
                            });
                            $('body').find('#formSubmit').attr('disabled', true);
                            $('.number_of_day').val('');
                        } else {
                            $('.number_of_day').val(data);
                            $('body').find('#formSubmit').attr('disabled', false);
                        }
                    }
                });
            } else {
                $('body').find('#formSubmit').attr('disabled', true);
            }
        });

        $(document).on("change", ".leave_type_id  ", function() {
            var leave_type_id = $('.leave_type_id ').val();
            var employee_id = $('.employee_id ').val();
            if (leave_type_id != '' && employee_id != '') {
                var action = "{{ URL::to('leaveManagement/applyForLeave/getEmployeeLeaveBalance') }}";
                $.ajax({
                    type: 'POST',
                    url: action,
                    data: {
                        'leave_type_id': leave_type_id,
                        'employee_id': employee_id,
                        '_token': $('input[name=_token]').val()
                    },
                    dataType: 'json',
                    success: function(data) {
                        if (data == 0) {
                            $.toast({
                                heading: 'Warning',
                                text: 'You have no leave balance !',
                                position: 'top-right',
                                loaderBg: '#ff6849',
                                icon: 'warning',
                                hideAfter: 3000,
                                stack: 6
                            });
                            $('.current_balance').val(data);
                            $('body').find('#formSubmit').attr('disabled', true);
                        } else {
                            $('.current_balance').val(data);
                            $('body').find('#formSubmit').attr('disabled', false);
                        }
                    }
                });
            } else {
                $('body').find('#formSubmit').attr('disabled', true);
                $.toast({
                    heading: 'Warning',
                    text: 'Please select leave type !',
                    position: 'top-right',
                    loaderBg: '#ff6849',
                    icon: 'warning',
                    hideAfter: 3000,
                    stack: 6
                });
                $('.current_balance').val('');
            }
        });

    });
</script> --}}
@endsection
