@extends('admin.master')

@section('title')
    @if (isset($editModeData))
        @lang('employee_earnings.edit_employee_earning')
    @else
        @lang('employee_earnings.add_employee_earning')
    @endif
@endsection

@section('content')
<div class="container-fluid">

    <div class="row bg-title">
        <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
            <ol class="breadcrumb">
                <li class="breadcrumbColor">
                    <a href="{{ url('dashboard') }}">
                        <i class="fa fa-home"></i> @lang('dashboard.dashboard')
                    </a>
                </li>
                <li class="active">@yield('title')</li>
            </ol>
        </div>

        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
            <a href="{{ route('employee_earnings.index') }}"
               class="btn btn-success pull-right m-l-20 hidden-xs hidden-sm waves-effect waves-light">
                <i class="fa fa-list-ul" aria-hidden="true"></i>
                @lang('employee_earnings.view_employee_earnings')
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-info">
                <div class="panel-heading">
                    <i class="mdi mdi-clipboard-text fa-fw"></i> @yield('title')
                </div>

                <div class="panel-wrapper collapse in" aria-expanded="true">
                    <div class="panel-body">

                        @if (isset($editModeData))
                            <form action="{{ route('employee_earnings.update', $editModeData->id) }}"
                                  method="POST"
                                  enctype="multipart/form-data"
                                  id="employeeEarningForm"
                                  class="form-horizontal">
                                @csrf
                                @method('PUT')
                        @else
                            <form action="{{ route('employee_earnings.store') }}"
                                  method="POST"
                                  enctype="multipart/form-data"
                                  id="employeeEarningForm"
                                  class="form-horizontal">
                                @csrf
                        @endif

                            <div class="form-body">

                                <div class="row">
                                    <div class="col-md-offset-1 col-md-10">
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
                                                <i class="cr-icon glyphicon glyphicon-ok"></i>
                                                &nbsp;<strong>{{ session()->get('success') }}</strong>
                                            </div>
                                        @endif

                                        @if (session()->has('error'))
                                            <div class="alert alert-danger alert-dismissable">
                                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                                                <i class="glyphicon glyphicon-remove"></i>
                                                &nbsp;<strong>{{ session()->get('error') }}</strong>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label col-md-4">
                                                @lang('employee_earnings.employee')
                                                <span class="validateRq">*</span>
                                            </label>

                                            <div class="col-md-8">
                                                <select name="employee_id" class="form-control required employee_id" id="employee_id">
                                                    @if (isset($selectedEmployee))
                                                        <option value="{{ $selectedEmployee->employee_id }}" selected>
                                                            {{ $selectedEmployee->payroll_number . ' - ' . $selectedEmployee->fullName() }}
                                                        </option>
                                                    @else
                                                        <option value="">@lang('common.select_employee')</option>
                                                        @foreach ($employees as $employee)
                                                            <option value="{{ $employee->employee_id }}"
                                                                {{ (string) old('employee_id', isset($editModeData) ? $editModeData->employee_id : '') === (string) $employee->employee_id ? 'selected' : '' }}>
                                                                {{ $employee->payroll_number . ' - ' . $employee->fullName() }}
                                                            </option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label col-md-4">
                                                @lang('employee_earnings.payroll_earning_type')
                                                <span class="validateRq">*</span>
                                            </label>

                                            <div class="col-md-8">
                                                <select name="payroll_earning_type_id"
                                                        class="form-control required payroll_earning_type_id"
                                                        id="payroll_earning_type_id">
                                                    <option value="">@lang('common.select_earning_type')</option>
                                                    @foreach ($payrollEarningTypes as $earningType)
                                                        <option value="{{ $earningType->id }}"
                                                            {{ (string) old('payroll_earning_type_id', isset($editModeData) ? $editModeData->payroll_earning_type_id : '') === (string) $earningType->id ? 'selected' : '' }}>
                                                            {{ $earningType->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label col-md-4">
                                                @lang('employee_earnings.earning_category')
                                                <span class="validateRq">*</span>
                                            </label>

                                            <div class="col-md-8">
                                                <select name="earning_category"
                                                        class="form-control required earning_category"
                                                        id="earning_category">
                                                    <option value="">@lang('common.select_category')</option>
                                                    @foreach ($earningCategories as $key => $value)
                                                        <option value="{{ $key }}"
                                                            {{ (string) old('earning_category', isset($editModeData) ? $editModeData->earning_category : '') === (string) $key ? 'selected' : '' }}>
                                                            {{ $value }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label col-md-4">
                                                @lang('employee_earnings.frequency')
                                                <span class="validateRq">*</span>
                                            </label>

                                            <div class="col-md-8">
                                                <select name="frequency"
                                                        class="form-control required frequency"
                                                        id="frequency">
                                                    <option value="">@lang('common.select_frequency')</option>
                                                    @foreach ($frequencies as $key => $value)
                                                        <option value="{{ $key }}"
                                                            {{ (string) old('frequency', isset($editModeData) ? $editModeData->frequency : '') === (string) $key ? 'selected' : '' }}>
                                                            {{ $value }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row" id="amount_section" style="display:none;">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label col-md-4">
                                                @lang('employee_earnings.amount')
                                                <span class="validateRq amount_required">*</span>
                                            </label>

                                            <div class="col-md-8">
                                                <input type="number"
                                                       name="amount"
                                                       class="form-control amount"
                                                       step="0.01"
                                                       value="{{ old('amount', isset($editModeData) ? $editModeData->amount : '') }}">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row" id="percentage_section" style="display:none;">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label col-md-4">
                                                @lang('employee_earnings.percentage')
                                                <span class="validateRq percentage_required">*</span>
                                            </label>

                                            <div class="col-md-8">
                                                <input type="number"
                                                       name="percentage"
                                                       class="form-control percentage"
                                                       step="0.01"
                                                       value="{{ old('percentage', isset($editModeData) ? $editModeData->percentage : '') }}">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row" id="rate_section" style="display:none;">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label col-md-4">
                                                @lang('employee_earnings.rate')
                                                <span class="validateRq rate_required">*</span>
                                            </label>

                                            <div class="col-md-8">
                                                <input type="number"
                                                       name="rate"
                                                       class="form-control rate"
                                                       step="0.01"
                                                       value="{{ old('rate', isset($editModeData) ? $editModeData->rate : '') }}">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label col-md-4">
                                                @lang('employee_earnings.units')
                                            </label>

                                            <div class="col-md-8">
                                                <input type="number"
                                                       name="units"
                                                       class="form-control units"
                                                       step="0.01"
                                                       value="{{ old('units', isset($editModeData) ? $editModeData->units : '') }}">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label col-md-4">
                                                @lang('employee_earnings.effective_from')
                                                <span class="validateRq">*</span>
                                            </label>

                                            <div class="col-md-8">
                                                @php
                                                    $defaultEffectiveFrom = old(
                                                        'effective_from',
                                                        isset($editModeData)
                                                            ? $editModeData->effective_from
                                                            : (isset($activeFinancialYear) ? $activeFinancialYear->start_date : '')
                                                    );
                                                @endphp

                                                <input type="date"
                                                       name="effective_from"
                                                       value="{{ $defaultEffectiveFrom }}"
                                                       class="form-control required effective_from"
                                                       id="effective_from">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label col-md-4">
                                                @lang('employee_earnings.effective_to')
                                            </label>

                                            <div class="col-md-8">
                                                <input type="date"
                                                       name="effective_to"
                                                       value="{{ old('effective_to', isset($editModeData) ? $editModeData->effective_to : '') }}"
                                                       class="form-control effective_to"
                                                       id="effective_to">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label col-md-4">
                                                Financial Year
                                                <span class="validateRq">*</span>
                                            </label>

                                            <div class="col-md-8">
                                                @php
                                                    $selectedFinancialYear = old(
                                                        'financial_year_id',
                                                        isset($editModeData)
                                                            ? $editModeData->financial_year_id
                                                            : (isset($activeFinancialYear) ? $activeFinancialYear->id : '')
                                                    );
                                                @endphp

                                                <select name="financial_year_id"
                                                        class="form-control required financial_year_id"
                                                        id="financial_year_id">
                                                    <option value="">@lang('common.select_financial_year')</option>
                                                    @foreach ($financialYears as $fy)
                                                        <option value="{{ $fy->id }}"
                                                            {{ (string) $selectedFinancialYear === (string) $fy->id ? 'selected' : '' }}>
                                                            {{ $fy->name . ' (' . $fy->formatted_date_range . ')' }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label col-md-4">
                                                @lang('employee_earnings.payroll_month')
                                                <span class="validateRq">*</span>
                                            </label>

                                            <div class="col-md-8">
                                                <select name="payroll_month"
                                                        class="form-control required payroll_month"
                                                        id="payroll_month"
                                                        data-selected="{{ old('payroll_month', isset($editModeData) ? $editModeData->payroll_month : '') }}">
                                                    <option value="">@lang('common.select_month')</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label col-md-4">
                                                @lang('common.status')
                                                <span class="validateRq">*</span>
                                            </label>

                                            <div class="col-md-8">
                                                <select name="status"
                                                        class="form-control required status"
                                                        id="status">
                                                    <option value="">@lang('common.select_status')</option>
                                                    @foreach (\App\Lib\Enumerations\GeneralStatus::getArray() as $key => $value)
                                                        <option value="{{ $key }}"
                                                            {{ (string) old('status', isset($editModeData) ? $editModeData->status : '') === (string) $key ? 'selected' : '' }}>
                                                            {{ $value }}
                                                        </option>
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
                                                <div class="checkbox checkbox-info">
                                                    <input type="hidden" name="is_recurring" value="0">
                                                    <input type="checkbox"
                                                           name="is_recurring"
                                                           value="1"
                                                           id="is_recurring"
                                                           {{ old('is_recurring', isset($editModeData) ? $editModeData->is_recurring : 1) ? 'checked' : '' }}>
                                                    <label for="is_recurring">
                                                        @lang('employee_earnings.is_recurring')
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label class="control-label col-md-2">
                                                @lang('employee_earnings.description')
                                            </label>

                                            <div class="col-md-10">
                                                <textarea name="description"
                                                          class="form-control"
                                                          rows="4">{{ old('description', isset($editModeData) ? $editModeData->description : '') }}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>

                            <div class="form-actions">
                                <div class="row">
                                    <div class="col-md-offset-2 col-md-10">
                                        @if (isset($editModeData))
                                            <button type="submit" class="btn btn-info btn_style">
                                                <i class="fa fa-pencil"></i> @lang('common.update')
                                            </button>
                                        @else
                                            <button type="submit" class="btn btn-info btn_style">
                                                <i class="fa fa-check"></i> @lang('common.save')
                                            </button>
                                        @endif

                                        <a href="{{ route('employee_earnings.index') }}" class="btn btn-default">
                                            <i class="fa fa-times"></i> @lang('common.cancel')
                                        </a>
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
    var financialYears = @json($financialYears);

    jQuery(function ($) {
        $("#employeeEarningForm").validate();

        $('.employee_id').select2({
            placeholder: 'Search employee',
            width: '100%'
        });

        $('#payroll_month').select2({
            placeholder: 'Select month',
            width: '100%'
        });

        $(document).on("change", ".payroll_earning_type_id", function () {
            var earningTypeId = $(this).val();

            $('#amount_section, #percentage_section, #rate_section').hide();
            $('.amount, .percentage, .rate').removeClass('required');

            if (earningTypeId) {
                $.ajax({
                    url: '{{ route("earning_types.details", ["id" => ":id"]) }}'.replace(':id', earningTypeId),
                    type: 'GET',
                    success: function (data) {
                        if (data.calculation_type === 'fixed_amount') {
                            $('#amount_section').show();
                            $('.amount').addClass('required');
                        } else if (
                            data.calculation_type === 'percentage_of_basic' ||
                            data.calculation_type === 'percentage_of_gross'
                        ) {
                            $('#percentage_section').show();
                            $('.percentage').addClass('required');
                        } else if (data.calculation_type === 'daily_rate') {
                            $('#rate_section').show();
                            $('.rate').addClass('required');
                        }
                    }
                });
            }
        });

        $(document).on("change", "#financial_year_id", function () {
            var financialYearId = $(this).val();
            var monthSelect = $('#payroll_month');
            var selectedMonth = monthSelect.data('selected');

            monthSelect.empty().append('<option value="">@lang("common.select_month")</option>');

            if (financialYearId) {
                var selectedFY = financialYears.find(function (fy) {
                    return fy.id == financialYearId;
                });

                if (selectedFY) {
                    var startDate = new Date(selectedFY.start_date);
                    var endDate = new Date(selectedFY.end_date);
                    var currentDate = new Date(startDate);
                    var seen = {};

                    while (currentDate <= endDate) {
                        var monthNum = currentDate.getMonth() + 1;
                        var year = currentDate.getFullYear();
                        var key = year + '-' + monthNum;

                        if (!seen[key]) {
                            seen[key] = true;

                            var monthName = currentDate.toLocaleString('default', {
                                month: 'long'
                            });

                            var isSelected = String(selectedMonth) === String(monthNum) ? 'selected' : '';

                            monthSelect.append(
                                '<option value="' + monthNum + '" ' + isSelected + '>' +
                                monthName + ' ' + year +
                                '</option>'
                            );
                        }

                        currentDate.setMonth(currentDate.getMonth() + 1);
                    }
                }
            }

            monthSelect.trigger('change.select2');
        });

        $('.payroll_earning_type_id').trigger('change');
        $('#financial_year_id').trigger('change');

        $('#effective_from').on('change', function () {
            $('#effective_to').attr('min', $(this).val());
        }).trigger('change');
    });
</script>
@endsection