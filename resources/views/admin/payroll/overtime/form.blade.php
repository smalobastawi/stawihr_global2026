@extends('admin.master')
@section('content')
@section('title')
    @if (isset($overtime))
        Edit Overtime Record
    @else
        Add Overtime Record
    @endif
@endsection

<style>
    .departmentName {
        position: relative;
    }

    #department_id-error {
        position: absolute;
        top: 66px;
        left: 0;
        width: 100%;
        height: 100%;
    }
</style>

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
                        @include('admin.partials.alert')
                       

                        <div class="row">
                            <div id="searchBox">
                                @if (isset($overtime))
                                    <form action="{{ route('payroll.overtime.update', $overtime->id) }}" method="POST" id="overtimeForm">
                                        @csrf
                                        @method('PUT')
                                @else
                                    <form action="{{ route('payroll.overtime.store') }}" method="POST" id="overtimeForm">
                                        @csrf
                                @endif

                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group departmentName">
                                            <label class="control-label" for="employee_id">@lang('employee.employee')<span class="validateRq">*</span></label>
                                            <select class="form-control employee_id select2 required" required name="employee_id" id="employee_id">
                                                <option value="">---- @lang('common.please_select')----</option>
                                                @foreach ($employees as $value)
                                                    <option value="{{ $value->employee_id }}" @if (isset($overtime) && $overtime->employee_id == $value->employee_id) selected 
                                                    @elseif(old('employee_id') == $value->employee_id) selected @endif>
                                                        {{ $value->fullName() }}  ({{ $value->payroll_number }})
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="control-label" for="month_year">Month/Year<span class="validateRq">*</span></label>
                                            <input type="text" class="form-control required monthField" name="month_year" id="monthField" value="@if (isset($overtime)) {{ $overtime->month_year }}@else{{ old('month_year') }} @endif" required>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <!-- Weekend Hours and Days -->
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="control-label" for="weekend_hours_totals">Weekend Hours Total</label>
                                            <input type="number" class="form-control" name="weekend_hours_totals" id="weekend_hours_totals"  min="0" value="@if (isset($overtime)) {{ $overtime->weekend_hours_totals }}@else{{ old('weekend_hours_totals') }} @endif">
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="control-label" for="weekend_days_totals">Weekend Days Total</label>
                                            <input type="number" class="form-control" name="weekend_days_totals" id="weekend_days_totals" min="0" value="@if (isset($overtime)) {{ $overtime->weekend_days_totals }}@else{{ old('weekend_days_totals') }} @endif">
                                        </div>
                                    </div>
                                </div>

                                <div class="row">

                                    <!-- Public Holiday Hours and Days -->
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="control-label" for="public_holiday_hours_totals">Public Holiday Hours Total</label>
                                            <input type="number" class="form-control" name="public_holiday_hours_totals" id="public_holiday_hours_totals"  min="0" value="@if (isset($overtime)) {{ $overtime->public_holiday_hours_totals }}@else{{ old('public_holiday_hours_totals') }} @endif">
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="control-label" for="public_holiday_days_totals">Public Holiday Days Total</label>
                                            <input type="number" class="form-control" name="public_holiday_days_totals" id="public_holiday_days_totals" min="0" value="@if (isset($overtime)) {{ $overtime->public_holiday_days_totals }}@else{{ old('public_holiday_days_totals') }} @endif">
                                        </div>
                                    </div>
                                </div>

                                <div class="row">

                                    <!-- Weekday Hours and Days -->
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="control-label" for="weekday_hours_total">Weekday Hours Total</label>
                                            <input type="number" class="form-control" name="weekday_hours_total" id="weekday_hours_total"  min="0" value="@if (isset($overtime)) {{ $overtime->weekday_hours_total }}@else{{ old('weekday_hours_total') }} @endif">
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="control-label" for="weekday_days_total">Weekday Days Total</label>
                                            <input type="number" class="form-control" name="weekday_days_total" id="weekday_days_total" min="0" value="@if (isset($overtime)) {{ $overtime->weekday_days_total }}@else{{ old('weekday_days_total') }} @endif">
                                        </div>
                                    </div>

                                </div>

                                <!-- Payroll Period and Month -->
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="control-label" for="payroll_period_id">Payroll Period ID</label>

                                        <select name="payroll_period_id" id="payroll_period_id" class="form-control select2 required" required>
                                            <option value="">@lang('common.select_option')</option>
                                            @foreach ($payrollPeriods as $period)
                                                <option value="{{ $period->id }}" @if (isset($editModeData) && $editModeData->payroll_period_id == $period->id) selected @endif @if (old('payroll_period_id') == $period->id) selected @endif>
                                                    {{ $period->name }} ({{ $period->start_date->format('M d, Y') }} - {{ $period->end_date->format('M d, Y') }})
                                                </option>
                                            @endforeach
                                        </select>

                                    </div>
                                </div>
                                
                                
                                
                                

                                <div class="col-md-12">
                                    <div class="form-actions">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <button type="submit" class="btn btn-info btn_style"><i class="fa fa-check"></i>
                                                    @if (isset($overtime))
                                                        @lang('common.update')
                                                    @else
                                                        @lang('common.save')
                                                    @endif
                                                </button>
                                                <a href="{{ route('payroll.overtime.index') }}" class="btn btn-danger btn_style"><i class="fa fa-times"></i> @lang('common.cancel')</a>
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
    </div>
</div>

<script>
    jQuery(function() {
        $("#employeeOvertime").validate();

        // Initialize select2
        $('.employee_id').select2({
            placeholder: 'Search employee',
        });
          $('#payroll_period_id').select2({
            placeholder: 'Search employee',
        });
    });
</script>
<script>
    jQuery(function() {
        $("#salaryAdvanceForm").validate();

        // Initialize month picker for existing month field
        $('.monthField').datepicker({
            format: "mm-yyyy",
            viewMode: "months",
            minViewMode: "months",
            autoclose: true
        });

        // Initialize month picker for payroll month field
        $('.payrollMonth').datepicker({
            format: "mm-yyyy",
            viewMode: "months",
            minViewMode: "months",
            autoclose: true
        });
    });
  
</script>
@endsection

