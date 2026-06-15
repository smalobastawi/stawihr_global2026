@extends('admin.master')
@section('content')
@section('title')
    @if (isset($editModeData))
        @lang('employee_deductions.edit_employee_deduction')
    @else
        @lang('employee_deductions.add_employee_deduction')
    @endif
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
        <div class="col-lg-9 col-md-8 col-sm-8 col-xs-12">
            <a href="{{ route('employee_deductions.index') }}"
                class="btn btn-success pull-right m-l-20 hidden-xs hidden-sm waves-effect waves-light"><i
                    class="fa fa-list-ul" aria-hidden="true"></i> @lang('employee_deductions.view_employee_deductions') </a>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-info">
                <div class="panel-heading"><i class="mdi mdi-clipboard-text fa-fw"></i>@yield('title')</div>
                <div class="panel-wrapper collapse in" aria-expanded="true">
                    <div class="panel-body">
                        @if (isset($editModeData))
                            <form action="{{ route('employee_deductions.update', $editModeData->id) }}" method="POST" enctype="multipart/form-data" id="employeeDeductionForm" class="form-horizontal">
                                @csrf
                                @method('PUT')
                        @else
                            <form action="{{ route('employee_deductions.store') }}" method="POST" enctype="multipart/form-data" id="employeeDeductionForm" class="form-horizontal">
                                @csrf
                        @endif

                        <div class="form-body">
                            <div class="row">
                                <div class="col-md-offset-1 col-md-10">
                                    @if ($errors->any())
                                        <div class="alert alert-danger alert-dismissible" role="alert">
                                            <button type="button" class="close" data-dismiss="alert"
                                                aria-label="Close"><span aria-hidden="true">×</span></button>
                                            @foreach ($errors->all() as $error)
                                                <strong>{!! $error !!}</strong><br>
                                            @endforeach
                                        </div>
                                    @endif
                                    @if (session()->has('success'))
                                        <div class="alert alert-success alert-dismissable">
                                            <button type="button" class="close" data-dismiss="alert"
                                                aria-hidden="true">×</button>
                                            <i
                                                class="cr-icon glyphicon glyphicon-ok"></i>&nbsp;<strong>{{ session()->get('success') }}</strong>
                                        </div>
                                    @endif
                                    @if (session()->has('error'))
                                        <div class="alert alert-danger alert-dismissable">
                                            <button type="button" class="close" data-dismiss="alert"
                                                aria-hidden="true">×</button>
                                            <i
                                                class="glyphicon glyphicon-remove"></i>&nbsp;<strong>{{ session()->get('error') }}</strong>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Basic Information -->
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label col-md-4" for="employee_id">@lang('employee_deductions.employee')<span
                                                class="validateRq">*</span></label>
                                        <div class="col-md-8">
                                            @if (isset($selectedEmployee))
                                                <select name="employee_id" class="form-control required employee_id" id="employee_id">
@foreach([$selectedEmployee->employee_id => $selectedEmployee->payroll_number . ' - ' . $selectedEmployee->fullName()] as $__key => $__value)
<option value="{{ $__key }}" {{ (string)$selectedEmployee->employee_id == (string)$__key ? 'selected' : '' }}>{{ $__value }}</option>
@endforeach
</select>
                                            @else
                                                <select name="employee_id" class="form-control required employee_id" id="employee_id">
@foreach(['' => __('common.select_employee')] +
                                                        $employees->mapWithKeys(function ($employee) {
                                                                return [
                                                                    $employee->employee_id => $employee->staff_no . ' - ' . $employee->fullName(),
                                                                ];
                                                            })->toArray() as $__key => $__value)
<option value="{{ $__key }}" {{ (string)Request::old('employee_id') == (string)$__key ? 'selected' : '' }}>{{ $__value }}</option>
@endforeach
</select>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label col-md-4" for="deduction_type_id">Employee
                                            Deduction Type<span class="validateRq">*</span></label>
                                        <div class="col-md-8">
                                            <select name="deduction_type_id" class="form-control required deduction_type_id" id="deduction_type_id">
@foreach($payrollDeductionTypes->pluck('name', 'id')->toArray() as $__key => $__value)
<option value="{{ $__key }}" {{ (string) old('deduction_type_id', $editModeData->deduction_type_id ?? '') == (string) $__key ? 'selected' : '' }}>{{ $__value }}</option>
@endforeach
</select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label col-md-4"
                                            for="deduction_category">@lang('employee_deductions.deduction_category')<span
                                                class="validateRq">*</span></label>
                                        <div class="col-md-8">
                                            <select name="deduction_category" class="form-control required deduction_category" id="deduction_category">
@foreach(['' => __('common.select_category')] + $deductionCategories as $__key => $__value)
<option value="{{ $__key }}" {{ (string)Request::old('deduction_category') == (string)$__key ? 'selected' : '' }}>{{ $__value }}</option>
@endforeach
</select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Calculation Details -->

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label col-md-4" for="frequency">@lang('employee_deductions.frequency')<span
                                            class="validateRq">*</span></label>
                                    <div class="col-md-8">
                                        <select name="frequency" class="form-control required frequency" id="frequency">
@foreach(['' => __('common.select_frequency')] + $frequencies as $__key => $__value)
<option value="{{ $__key }}" {{ (string)Request::old('frequency') == (string)$__key ? 'selected' : '' }}>{{ $__value }}</option>
@endforeach
</select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Amount Fields (Dynamic based on calculation type) -->
                        <div class="row" id="amount_section" style="display: none;">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label col-md-4" for="amount">@lang('employee_deductions.amount')<span
                                            class="validateRq amount_required">*</span></label>
                                    <div class="col-md-8">
                                        <input type="number" name="amount" value="{{ Request::old('amount') }}">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row" id="percentage_section" style="display: none;">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label col-md-4" for="percentage">@lang('employee_deductions.percentage')<span
                                            class="validateRq percentage_required">*</span></label>
                                    <div class="col-md-8">
                                        <input type="number" name="percentage" value="{{ Request::old('percentage') }}">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row" id="rate_section" style="display: none;">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label col-md-4">Daily Rate<span
                                            class="validateRq rate_required">*</span></label>
                                    <div class="col-md-8">
                                        <input type="number" name="rate" value="{{ Request::old('rate') }}" class="form-control rate" id="rate" placeholder="Daily Rate" min="0" step="0.01" readonly="readonly">
                                        <small class="help-block text-muted" id="rate_calculation_note">
                                            Daily rate calculated automatically from basic salary
                                        </small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label col-md-4">Units (Days)<span
                                            class="validateRq">*</span></label>
                                    <div class="col-md-8">
                                        <input type="number" name="units" value="{{ Request::old('units') }}" class="form-control units" id="units" placeholder="Number of days" min="0" step="0.5">
                                        <small class="help-block text-muted">
                                            Enter number of days for deduction calculation
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Working days info display (optional) -->
                        <div class="row" id="working_days_info" style="display: none;">
                            <div class="col-md-12">
                                <div class="alert alert-info">
                                    <i class="fa fa-info-circle"></i>
                                    <strong>Calculation Info:</strong>
                                    Basic Salary: <span id="basic_salary_display">0.00</span> /
                                    Working Days: <span id="working_days_display">0</span> =
                                    Daily Rate: <span id="daily_rate_display">0.00</span>
                                    <br>
                                    Total Deduction: <span id="total_deduction_display">0.00</span>
                                    (Daily Rate × Units/Days)
                                </div>
                            </div>
                        </div>

                        <!-- Limits disabled per client request -->
                        {{-- <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label col-md-4" for="limit_per_month">@lang('employee_deductions.limit_per_month')</label>
                                        <div class="col-md-8">
                                            <input type="number" name="limit_per_month" value="{{ Request::old('limit_per_month') }}">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label col-md-4" for="limit_per_year">@lang('employee_deductions.limit_per_year')</label>
                                        <div class="col-md-8">
                                            <input type="number" name="limit_per_year" value="{{ Request::old('limit_per_year') }}">
                                        </div>
                                    </div>
                                </div>
                            </div> --}}

                        <!-- Effective Period -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label col-md-4" for="effective_from">@lang('employee_deductions.effective_from')<span
                                            class="validateRq">*</span></label>
                                    <div class="col-md-8">
                                        <input type="date" name="effective_from" value="{{ Request::old('effective_from') }}" class="form-control required effective_from" id="effective_from">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label col-md-4"
                                        for="effective_to">@lang('employee_deductions.effective_to')</label>
                                    <div class="col-md-8">
                                        <input type="date" name="effective_to" value="{{ Request::old('effective_to') }}" class="form-control effective_to" id="effective_to">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Financial Year and Payroll Month -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label col-md-4" for="payroll_year">Financial Year<span
                                            class="validateRq">*</span></label>
                                    <div class="col-md-8">
                                        <select name="payroll_year" class="form-control required payroll_year" id="payroll_year">
                                            <option value="">{{ __('common.select_financial_year') }}</option>
                                            @foreach($financialYears as $financialYear)
                                                @php
                                                    $yearValue = \Carbon\Carbon::parse($financialYear->start_date)->year;
                                                    $selectedYear = isset($editModeData) ? (string)$editModeData->payroll_year : (isset($activeFinancialYear) ? (string)\Carbon\Carbon::parse($activeFinancialYear->start_date)->year : '');
                                                @endphp
                                                <option value="{{ $yearValue }}" {{ $selectedYear == (string)$yearValue ? 'selected' : '' }}>{{ $financialYear->name }} ({{ $financialYear->formatted_date_range }})</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label col-md-4" for="payroll_month">@lang('employee_deductions.payroll_month')<span
                                            class="validateRq">*</span></label>
                                    <div class="col-md-8">
                                        <select name="payroll_month" class="form-control required payroll_month" id="payroll_month">
                                            <option value="">{{ __('common.select_month') }}</option>
                                            @foreach(range(1,12) as $__m)
                                                @php
                                                    $selectedMonth = isset($editModeData) ? (string)$editModeData->payroll_month : (string)date('n');
                                                @endphp
                                                <option value="{{ $__m }}" {{ $selectedMonth == (string)$__m ? 'selected' : '' }}>{{ date('F', mktime(0,0,0,$__m,1)) }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label col-md-4" for="status">@lang('common.status')<span
                                            class="validateRq">*</span></label>
                                    <div class="col-md-8">
                                        <select name="status" class="form-control required status" id="status">
                                            <option value="">{{ __('common.select_status') }}</option>
                                            @foreach(\App\Lib\Enumerations\GeneralStatus::getArray() as $__key => $__value)
                                                @php
                                                    $selectedStatus = isset($editModeData) ? (string)$editModeData->status : (string)Request::old('status');
                                                @endphp
                                                <option value="{{ $__key }}" {{ $selectedStatus == (string)$__key ? 'selected' : '' }}>{{ $__value }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <div class="col-md-offset-2 col-md-10">

                                        <div>
                                            <label for="is_recurring">
                                                @lang('employee_deductions.is_recurring')
                                                <input type="checkbox" name="is_recurring" value="1" id="is_recurring" @if((isset($editModeData) ? $editModeData->is_recurring : true)) checked @endif>
                                                <input type="hidden" name="is_recurring" value="{{ 0 }}">
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Description -->
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="control-label col-md-2" for="description">@lang('employee_deductions.description')</label>
                                    <div class="col-md-10">
                                        <textarea name="description">{{ Request::old('description') }}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-actions">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-offset-2 col-md-10">
                                        @if (isset($editModeData))
                                            <button type="submit" class="btn btn-info btn_style"><i
                                                    class="fa fa-pencil"></i> @lang('common.update')</button>
                                        @else
                                            <button type="submit" class="btn btn-info btn_style"><i
                                                    class="fa fa-check"></i> @lang('common.save')</button>
                                        @endif
                                        <a href="{{ route('employee_deductions.index') }}" class="btn btn-default"><i
                                                class="fa fa-times"></i> @lang('common.cancel')</a>
                                    </div>
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
</div>
@endsection

@section('page_scripts')
<script>
    jQuery(function() {
        $("#employeeDeductionForm").validate();

        // Handle deduction type selection
        $(document).on("change", ".deduction_type_id", function() {
            var deductionTypeId = $(this).val();

            // Hide all sections first
            $('#amount_section, #percentage_section, #rate_section').hide();
            $('.amount, .percentage, .rate').removeClass('required');

            if (deductionTypeId) {
                $.ajax({
                    url: '{{ route('deduction_types.details', ['id' => ':id']) }}'.replace(
                        ':id', deductionTypeId),
                    type: 'GET',
                    success: function(data) {
                        if (data.default_calculation_type == 'fixed_amount') {
                            $('#amount_section').show();
                            $('.amount').addClass('required');
                        } else if (data.default_calculation_type == 'percentage_of_basic') {
                            $('#percentage_section').show();
                            $('.percentage').addClass('required');
                        } else if (data.default_calculation_type == 'daily_rate') {
                            $('#rate_section').show();
                            $('.rate').addClass('required');

                            // Auto-calculate daily rate when daily rate type is selected
                            calculateDailyRate();
                        }
                    }
                });
            }
        });

        // Calculate daily rate when employee or payroll month/year changes
        $(document).on('change', '#employee_id, #payroll_year, #payroll_month', function() {
            if ($('#rate_section').is(':visible')) {
                calculateDailyRate();
            }
        });

        // Initialize form based on edit mode
        @if (isset($editModeData))
            $('.deduction_type_id').trigger('change');
        @endif

        const today = new Date();
        const firstDayOfMonth = new Date(today.getFullYear(), today.getMonth(), 1);
        const firstDayFormatted = firstDayOfMonth.toISOString().split('T')[0];

        // Ensure effective_to is after effective_from
        $('#effective_from').on('change', function() {
            $('#effective_to').attr('min', $(this).val());
        });

        function calculateDailyRate() {
            var employeeId = $('#employee_id').val();
            var payrollYear = $('#payroll_year').val();
            var payrollMonth = $('#payroll_month').val();

            if (!employeeId || !payrollYear || !payrollMonth) {
                $('#rate').val('');
                $('#working_days').val('');
                $('#rate_calculation_note').text('Please select employee, year, and month first');
                return;
            }

            // Show loading state
            $('#rate').val('Calculating...');
            $('#working_days').val('Calculating...');
            $('#rate_calculation_note').text('Calculating daily rate...');

            $.ajax({
                url: '{{ route('employee_deductions.calculate_daily_rate') }}',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    employee_id: employeeId,
                    payroll_year: payrollYear,
                    payroll_month: payrollMonth
                },
                success: function(response) {
                    if (response.success) {
                        $('#rate').val(response.daily_rate.toFixed(2));
                        $('#working_days').val(response.working_days);
                        $('#rate_calculation_note').html(
                            'Daily rate calculated: ' + response.basic_salary.toFixed(2) +
                            ' / ' + response.working_days + ' working days = ' +
                            response.daily_rate.toFixed(2)
                        );
                    } else {
                        $('#rate').val('');
                        $('#working_days').val('');
                        $('#rate_calculation_note').text('Error: ' + response.message);
                    }
                },
                error: function(xhr) {
                    $('#rate').val('');
                    $('#working_days').val('');
                    $('#rate_calculation_note').text('Error calculating daily rate');
                    console.error('Error calculating daily rate:', xhr.responseText);
                }
            });
        }
    });

    $('.employee_id').select2({
        placeholder: 'Search employee',
    });
</script>
@endsection

