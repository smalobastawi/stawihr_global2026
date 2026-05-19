@extends('admin.master')
@section('content')
@section('title')
    @lang('leave.leave_application_form')
@endsection
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

    .existing-files ul {
        list-style-type: none;
        padding: 0;
    }

    .existing-files li {
        margin-bottom: 5px;
    }

    .existing-files a.remove-file {
        color: #ff0000;
        margin-left: 10px;
    }
</style>
<style>
    .datepicker table tr td.disabled,
    .datepicker table tr td.disabled:hover {
        background: #ffe6e6 !important;
        color: #ff0000 !important;
        cursor: not-allowed !important;
        text-decoration: line-through;
        opacity: 0.6;
    }

    .datepicker table tr td.disabled:before {
        content: "✗";
        margin-right: 2px;
        font-size: 10px;
    }

    /* Highlight financial year info */
    .text-info {
        color: #31708f !important;
        font-style: italic;
    }

    /* Style for current balance field */
    .current_balance {
        background-color: #f8f9fa;
        font-weight: bold;
        color: #28a745 !important;
    }
</style>

<div class="container-fluid">
    <div class="row bg-title">
        <div class="col-lg-5 col-md-5 col-sm-5 col-xs-12">
            <ol class="breadcrumb">
                <li class="active breadcrumbColor"><a href="#"><i class="fa fa-home"></i> @lang('dashboard.dashboard')</a></li>
                <li>@yield('title')</li>
            </ol>
        </div>
        <div class="col-lg-7 col-md-7 col-sm-7 col-xs-12">
            <a href="{{ route('ess.leave.index') }}"
                class="btn btn-success pull-right m-l-20 hidden-xs hidden-sm waves-effect waves-light">
                <i class="fa fa-list-ul" aria-hidden="true"></i> Go to leave applications
            </a>
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

                        <form action="{{ route('ess.leave.update') }}" method="POST" enctype="multipart/form-data" id="leaveApplicationForm">
                            @csrf
                            <div class="form-body">
                                <div class="row">
                                    <input type="hidden" name="employee_id" value="{{ $getEmployeeInfo->employee_id }}" class="employee_id">
                                    <input type="hidden" name="leave_application_id" value="{{ $leaveApplication->leave_application_id }}" class="leave_application_id">

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="exampleInput">@lang('common.employee_name')<span
                                                    class="validateRq">*</span></label>
                                            <input type="text" value="{{ $getEmployeeInfo->first_name . ' ' . $getEmployeeInfo->last_name }}"
                                                class="form-control" readonly>
                                        </div>
                                    </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="exampleInput">@lang('leave.leave_type')<span
                                                class="validateRq">*</span></label>
                                        <select name="leave_type_id"
                                            class="form-control leave_type_id select2 required">
                                            @foreach ($leaveTypeList as $value => $label)
                                                <option value="{{ $value }}"
                                                    @if ($value == $leaveApplication->leave_type_id) selected="selected" @endif>
                                                    {{ $label }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="exampleInput">@lang('leave.current_balance')<span
                                                class="validateRq">*</span></label>
                                        <input type="text" value=""
                                            class="form-control current_balance"
                                            readonly
                                            placeholder="{{ __('leave.current_balance') }}">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <label for="exampleInput">@lang('common.from_date')<span class="validateRq">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                        <input type="text" name="application_from_date"
                                            value="{{ dateConvertDBtoForm($leaveApplication->application_from_date) }}"
                                            class="form-control application_from_date"
                                            readonly
                                            placeholder="{{ __('common.from_date') }}">
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <label for="exampleInput">@lang('common.to_date')<span class="validateRq">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                        <input type="text" name="application_to_date"
                                            value="{{ dateConvertDBtoForm($leaveApplication->application_to_date) }}"
                                            class="form-control application_to_date"
                                            readonly
                                            placeholder="{{ __('common.to_date') }}">
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="exampleInput">@lang('leave.number_of_day')<span
                                                class="validateRq">*</span></label>
                                        <input type="text" name="number_of_day"
                                            value="{{ $leaveApplication->number_of_day }}"
                                            class="form-control number_of_day"
                                            readonly
                                            placeholder="{{ __('leave.number_of_day') }}">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="exampleInput">@lang('leave.purpose')</label>
                                        <textarea name="purpose" id="purpose"
                                            class="form-control purpose"
                                            placeholder="{{ __('leave.purpose') }}"
                                            cols="30" rows="3">{{ old('purpose', $leaveApplication->purpose) }}</textarea>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="exampleInput">Upload Justification<span
                                                class="validateRq"></span></label>
                                        <input name="justification_file[]" type="file"
                                            accept=".jpeg,.pdf, .png,.gif,.docx" multiple />

                                        @if ($leaveApplication->justification->count() > 0)
                                            <div class="existing-files">
                                                <p>Existing Files:</p>
                                                <ul>
                                                    @foreach ($leaveApplication->justification as $document)
                                                        <li>
                                                            <a href="{{ asset('uploads/leaveApplication/' . $document->file_name) }}"
                                                                target="_blank">
                                                                {{ $document->file_name }}
                                                            </a>
                                                            <a href="#" class="remove-file"
                                                                data-id="{{ $document->id }}">
                                                                <i class="fa fa-trash"></i>
                                                            </a>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                            <div class="form-actions">
                                <div class="row">
                                    <div class="col-md-6">
                                        <button type="submit" id="formSubmit" class="btn btn-info">
                                            <i class="fa fa-paper-plane"></i> Update Application
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

        /** --------------------------------------------------------
         *  1️⃣ INITIALIZE BOTH DATEPICKERS *ONCE* WITH dd/mm/yyyy
         * -------------------------------------------------------- */
        $('.application_from_date, .application_to_date').datepicker({
            format: "dd/mm/yyyy",
            todayHighlight: true,
            clearBtn: true,
            autoclose: true,
            orientation: "auto"
        });

        /** --------------------------------------------------------
         *  2️⃣ WHEN FROM-DATE CHANGES → ADJUST TO-DATE LIMITS
         * -------------------------------------------------------- */
        $('.application_from_date').on('changeDate', function() {
            let val = $(this).val();
            let fromDate = toDate(val);

            if (!fromDate) return;

            // Update TO-DATE picker
            $('.application_to_date').datepicker('setStartDate', fromDate);
        });

        /** --------------------------------------------------------
         *  3️⃣ VALIDATE DATES + GET TOTAL DAYS
         * -------------------------------------------------------- */
        $('.application_from_date, .application_to_date').on('change', function() {

            let fromDateStr = $('.application_from_date').val();
            let toDateStr = $('.application_to_date').val();

            if (!fromDateStr || !toDateStr) {
                $('#formSubmit').prop('disabled', true);
                return;
            }

            let from = toDate(fromDateStr);
            let to = toDate(toDateStr);

            // Request total days
            $.ajax({
                type: 'POST',
                url: "{{ route('ess.leave.leave.employee.apply.totaldays') }}",
                data: {
                    application_from_date: fromDateStr,
                    application_to_date: toDateStr,
                    leave_type_id: $('.leave_type_id').val(),
                    _token: $('input[name=_token]').val()
                },
                dataType: 'json',
                success: function(days) {
                    validateLeaveDays(days);
                }
            });

        });

        /** --------------------------------------------------------
         *  4️⃣ VALIDATION FUNCTION FOR LEAVE DAYS
         * -------------------------------------------------------- */
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

            if (requested > totalAvailable) {
                let regularBalance = parseFloat(leaveBalanceData.regular_balance || 0);
                let advanceAvailable = parseFloat(leaveBalanceData.advance_available || 0);

                $.toast({
                    heading: 'Validation Error',
                    text: `You are trying to apply for ${requested} days, but your total available balance is ${totalAvailable} days (${regularBalance} regular balance + ${advanceAvailable} advance available). Please reduce your request or contact HR.`,
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
                        hideAfter: 5000,
                        stack: 6
                    });
                }
            }
        }

        /** --------------------------------------------------------
         *  5️⃣ WHEN LEAVE TYPE CHANGES → CHECK BALANCE
         * -------------------------------------------------------- */
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
                url: "{{ route('ess.leave.balance') }}",
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
                            text: 'You have no leave balance!',
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

        // Handle file removal
        $(document).on("click", ".remove-file", function(e) {
            e.preventDefault();
            var fileId = $(this).data('id');
            var $li = $(this).closest('li');

            if (confirm('Are you sure you want to delete this file?')) {
                $.ajax({
                    url: "{{ route('ess.leave.justification.delete') }}",
                    type: "POST",
                    data: {
                        id: fileId,
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        if (response.success) {
                            $li.remove();
                            $.toast({
                                heading: 'Success',
                                text: 'File deleted successfully',
                                position: 'top-right',
                                loaderBg: '#ff6849',
                                icon: 'success',
                                hideAfter: 3000,
                                stack: 6
                            });
                        }
                    },
                    error: function(xhr) {
                        $.toast({
                            heading: 'Error',
                            text: 'Failed to delete file',
                            position: 'top-right',
                            loaderBg: '#ff6849',
                            icon: 'error',
                            hideAfter: 3000,
                            stack: 6
                        });
                    }
                });
            }
        });

        // Initialize on page load
        $(document).ready(function() {
            // Trigger leave type change to load initial balance
            $('.leave_type_id').trigger('change');
        });

    });
</script>
@endsection
