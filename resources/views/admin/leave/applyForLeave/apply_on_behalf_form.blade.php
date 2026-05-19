@extends('admin.master')

@section('title', 'Apply Leave On Behalf')

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
                    <div class="panel-heading"><i class="mdi mdi-clipboard-text fa-fw"></i>Apply Leave On Behalf of Employee</div>
                    <div class="panel-wrapper collapse in" aria-expanded="true">
                        <div class="panel-body">
                        <form method="POST" action="{{ route('applyOnBehalf.store') }}" enctype="multipart/form-data" id="leaveApplicationForm">
                            @csrf

                                <div class="form-body">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="exampleInput">@lang('common.employee_name')<span class="validateRq">*</span></label>
                                                <select name="employee_id" class="form-control employee_id select2 required" id="employee_id">
                                                    <option value="">Select Employee</option>
                                                    @foreach($employeeList as $__key => $__value)
                                                        <option value="{{ $__key }}" {{ (string)Request::old('employee_id') == (string)$__key ? 'selected' : '' }}>{{ $__value }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="exampleInput">
                                                    @lang('leave.leave_type')<span class="validateRq">*</span>
                                                </label>
                                            <select name="leave_type_id" class="form-control leave_type_id select2 required">
                                                <option value="">Select Employee First</option>
                                            </select>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="exampleInput">@lang('leave.current_balance')<span class="validateRq">*</span></label>
                                                <input type="text" name="" value="{{ '' }}" class="form-control current_balance" readonly="readonly" placeholder="Select employee and leave type">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <label for="exampleInput">@lang('common.from_date')<span class="validateRq">*</span></label>
                                            <div class="input-group">
                                                <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                                <input type="text" class="form-control application_from_date" name="application_from_date" value="{{ Request::old('application_from_date') }}" placeholder="dd/mm/yyyy">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <label for="exampleInput">
                                                @lang('common.to_date')<span class="validateRq">*</span>
                                            </label>
                                            <div class="input-group">
                                                <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                                <input type="text" class="form-control application_to_date" name="application_to_date" value="{{ Request::old('application_to_date') }}" placeholder="dd/mm/yyyy">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="exampleInput">Number of Days<span class="validateRq">*</span></label>
                                                <input type="text" class="form-control number_of_day" name="number_of_day" value="{{ '' }}" readonly>
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
                                                <label for="exampleInput">Approval Supervisor: <span id="supervisor_name">-</span></label>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="exampleInput">Upload Justification/Evidence (Optional)<span class="validateRq"></span></label>
                                                <input class="form-control" name="justification_file[]" type="file" accept=".jpeg,.pdf,.png,.gif,.docx" multiple />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-actions">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <button type="submit" id="formSubmit" class="btn btn-info" disabled><i class="fa fa-paper-plane"></i> Submit Application
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
    <script>
        function toDate(dateString) {
            if (!dateString) return null;
            let [d, m, y] = dateString.split('/');
            return new Date(parseInt(y), parseInt(m) - 1, parseInt(d));
        }

        jQuery(function() {
            // Store balance data globally for validation
            let leaveBalanceData = {};
            let employeeSupervisor = null;

            // Initialize both datepickers with dd/mm/yyyy
            $('.application_from_date, .application_to_date').datepicker({
                format: "dd/mm/yyyy",
                todayHighlight: true,
                clearBtn: true,
                autoclose: true,
                orientation: "auto"
            });

            // When FROM-DATE changes → Adjust TO-DATE limits
            $('.application_from_date').on('changeDate', function() {
                let val = $(this).val();
                let fromDate = toDate(val);
                if (!fromDate) return;
                $('.application_to_date').datepicker('setStartDate', fromDate);
            });

            // When employee selection changes → Fetch supervisor info and leave types
            $('.employee_id').on('change', function() {
                let employeeId = $(this).val();
                if (!employeeId) {
                    $('#supervisor_name').text('-');
                    employeeSupervisor = null;
                    // Reset leave type dropdown
                    $('.leave_type_id').html('<option value="">Select Employee First</option>').trigger('change');
                    return;
                }

                // Fetch employee details including supervisor
                $.ajax({
                    type: 'GET',
                    url: "{{ url('leaveManagement/applyForLeave/applyOnBehalf/employeeDetails') }}/" + employeeId,
                    dataType: 'json',
                    success: function(data) {
                        if (data.supervisor) {
                            $('#supervisor_name').text(data.supervisor.first_name + ' ' + (data.supervisor.middle_name ? data.supervisor.middle_name + ' ' : '') + data.supervisor.last_name);
                            employeeSupervisor = data.supervisor;
                        } else {
                            $('#supervisor_name').text('No supervisor assigned');
                            employeeSupervisor = null;
                        }
                    },
                    error: function() {
                        $('#supervisor_name').text('-');
                        employeeSupervisor = null;
                    }
                });

                // Fetch leave types for this employee based on their leave group
                $.ajax({
                    type: 'GET',
                    url: "{{ url('leaveManagement/applyForLeave/applyOnBehalf/employeeLeaveTypes') }}/" + employeeId,
                    dataType: 'json',
                    success: function(leaveTypes) {
                        let options = '<option value="">Select Leave Type</option>';
                        $.each(leaveTypes, function(id, name) {
                            options += '<option value="' + id + '">' + name + '</option>';
                        });
                        $('.leave_type_id').html(options).trigger('change');
                    },
                    error: function() {
                        $('.leave_type_id').html('<option value="">Error loading leave types</option>');
                    }
                });
            });

            // Validate dates and get total days
            $('.application_from_date, .application_to_date').on('change', function() {
                let fromDateStr = $('.application_from_date').val();
                let toDateStr = $('.application_to_date').val();
                let employeeId = $('.employee_id').val();

                if (!fromDateStr || !toDateStr || !employeeId) {
                    $('#formSubmit').prop('disabled', true);
                    return;
                }

                let from = toDate(fromDateStr);
                let to = toDate(toDateStr);

                // Request total days using ESS method
                $.ajax({
                    type: 'POST',
                    url: "{{ route('applyOnBehalf.totalDays') }}",
                    data: {
                        application_from_date: fromDateStr,
                        application_to_date: toDateStr,
                        leave_type_id: $('.leave_type_id').val(),
                        employee_id: employeeId,
                        _token: $('input[name=_token]').val()
                    },
                    dataType: 'json',
                    success: function(days) {
                        validateLeaveDays(days);
                    }
                });
            });

            // Validation function for leave days
            function validateLeaveDays(requestedDays) {
                if (requestedDays === 0) {
                    $.toast({
                        heading: 'Warning',
                        text: 'You cannot apply for 0 days.',
                        icon: 'warning',
                        position: 'top-right'
                    });
                    $('#formSubmit').prop('disabled', true);
                    $('.number_of_day').val('');
                    return;
                }

                $('.number_of_day').val(requestedDays);

                // Check if we have balance data
                if (!leaveBalanceData.total_available) {
                    $('#formSubmit').prop('disabled', true);
                    $.toast({
                        heading: 'Warning',
                        text: 'Please select a leave type first.',
                        icon: 'warning',
                        position: 'top-right'
                    });
                    return;
                }

                let totalAvailable = parseFloat(leaveBalanceData.total_available);
                let requested = parseFloat(requestedDays);
                let regularBalance = parseFloat(leaveBalanceData.regular_balance || 0);
                let advanceAvailable = parseFloat(leaveBalanceData.advance_available || 0);

                if (requested > totalAvailable) {
                    $.toast({
                        heading: 'Validation Error',
                        text: `You are trying to apply for ${requested} days, but the total available balance is ${totalAvailable} days (${regularBalance} regular balance + ${advanceAvailable} advance available).`,
                        icon: 'error',
                        position: 'top-right',
                        hideAfter: 10000,
                        stack: 6
                    });

                    $('#formSubmit').prop('disabled', true);
                } else {
                    $('#formSubmit').prop('disabled', false);

                    // Show success message if using advance days
                    if (requested > regularBalance && regularBalance > 0) {
                        let advanceUsed = requested - regularBalance;
                        $.toast({
                            heading: 'Info',
                            text: `This application will use ${regularBalance} regular balance days and ${advanceUsed} advance days.`,
                            icon: 'info',
                            position: 'top-right',
                            hideAfter: 10000,
                            stack: 6
                        });
                    }
                }
            }

            // When LEAVE TYPE changes → Check balance
            $('.leave_type_id').on('change', function() {
                let leaveType = $(this).val();
                let employee = $('.employee_id').val();

                if (!leaveType || !employee) {
                    $('.current_balance').val('');
                    leaveBalanceData = {};
                    $('#formSubmit').prop('disabled', true);
                    return;
                }

                $.ajax({
                    type: 'POST',
                    url: "{{ route('applyOnBehalf.balance') }}",
                    data: {
                        leave_type_id: leaveType,
                        employee_id: employee,
                        _token: $('input[name=_token]').val()
                    },
                    dataType: 'json',
                    success: function(data) {
                        leaveBalanceData = data;

                        if (data.regular_balance == 0) {
                            $.toast({
                                heading: 'Warning',
                                text: 'Employee has no leave balance!',
                                icon: 'warning',
                                position: 'top-right'
                            });
                            $('.current_balance').val('0');
                            $('#formSubmit').prop('disabled', true);
                        } else {
                            $('.current_balance').val(data.regular_balance);
                            $('#formSubmit').prop('disabled', false);
                        }

                        // Revalidate if dates are already selected
                        let currentDays = $('.number_of_day').val();
                        if (currentDays) {
                            validateLeaveDays(currentDays);
                        }
                    }
                });
            });
        });
    </script>
@endsection
