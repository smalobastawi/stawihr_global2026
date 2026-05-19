@extends('admin.master')

@section('title', __('payroll.edit_payroll_period'))

@section('content')
<div class="row bg-title">
    <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
        <h4 class="page-title">@lang('payroll.edit_payroll_period')</h4>
    </div>
    <div class="col-lg-9 col-sm-8 col-md-8 col-xs-12">
        <ol class="breadcrumb">
            <li class="active breadcrumbColor">
                <a href="{{ url('dashboard') }}"><i class="fa fa-home"></i> @lang('dashboard.dashboard')</a>
            </li>
            <li><a href="{{ route('payroll.dashboard') }}">@lang('payroll.payroll')</a></li>
            <li><a href="{{ route('payroll.settings.periods.index') }}">@lang('payroll.periods')</a></li>
            <li><a href="{{ route('payroll.settings.periods.show', $period) }}">{{ $period->name }}</a></li>
            <li>@lang('common.edit')</li>
        </ol>
    </div>
</div>

<div class="row">
    <div class="col-sm-12">
         @include('admin.partials.alert')
        <div class="white-box">
            <h3 class="box-title m-b-0">@lang('payroll.edit_payroll_period')</h3>
            <p class="text-muted m-b-30">@lang('payroll.modify_period_details')</p>

            @if($period->status !== 'open')
                <div class="alert alert-warning">
                    <i class="fa fa-warning"></i> @lang('payroll.only_open_periods_editable')
                </div>
            @endif

            <form method="POST" action="{{ route('payroll.settings.periods.update', $period) }}" class="form-horizontal">
                @csrf
                @method('PUT')
                
                <div class="form-group">
                    <label class="col-md-2 control-label">@lang('payroll.period_name') <span class="text-danger">*</span></label>
                    <div class="col-md-10">
                        <input type="text" name="name" class="form-control" 
                               value="{{ old('name', $period->name) }}" 
                               placeholder="@lang('payroll.period_name_placeholder')" required
                               {{ $period->status !== 'open' ? 'readonly' : '' }}>
                        @error('name')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-2 control-label">@lang('payroll.period_type') <span class="text-danger">*</span></label>
                    <div class="col-md-10">
                        <select name="period_type" class="form-control" required 
                                {{ $period->status !== 'open' ? 'disabled' : '' }}>
                            <option value="">@lang('payroll.select_period_type')</option>
                            <option value="monthly" {{ old('period_type', $period->period_type) == 'monthly' ? 'selected' : '' }}>@lang('payroll.monthly')</option>
                            <option value="weekly" {{ old('period_type', $period->period_type) == 'weekly' ? 'selected' : '' }}>@lang('payroll.weekly')</option>
                            <option value="bi-weekly" {{ old('period_type', $period->period_type) == 'bi-weekly' ? 'selected' : '' }}>@lang('payroll.bi_weekly')</option>
                        </select>
                        @error('period_type')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <!-- Date Fields in Two Columns -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="col-md-4 control-label">@lang('payroll.start_date') <span class="text-danger">*</span></label>
                            <div class="col-md-8">
                                <input type="date" name="start_date" class="form-control" 
                                       value="{{ old('start_date', $period->start_date->format('Y-m-d')) }}" required
                                       {{ $period->status !== 'open' ? 'readonly' : '' }}>
                                @error('start_date')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="col-md-4 control-label">@lang('payroll.end_date') <span class="text-danger">*</span></label>
                            <div class="col-md-8">
                                <input type="date" name="end_date" class="form-control" 
                                       value="{{ old('end_date', $period->end_date->format('Y-m-d')) }}" required
                                       {{ $period->status !== 'open' ? 'readonly' : '' }}>
                                @error('end_date')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                                <small class="text-muted">@lang('payroll.end_date_hint')</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Input Period Fields in Two Columns -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="col-md-4 control-label">@lang('payroll.input_period_start') <span class="text-danger">*</span></label>
                            <div class="col-md-8">
                                <input type="date" name="input_period_start" class="form-control" 
                                       value="{{ old('input_period_start', $period->input_period_start ? \Carbon\Carbon::parse($period->input_period_start)->format('Y-m-d') : '') }}" 
                                       required
                                       {{ $period->status !== 'open' ? 'readonly' : '' }}>
                                @error('input_period_start')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                                <small class="text-muted">@lang('payroll.input_period_start_hint')</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="col-md-4 control-label">@lang('payroll.input_period_end') <span class="text-danger">*</span></label>
                            <div class="col-md-8">
                                <input type="date" name="input_period_end" class="form-control" 
                                       value="{{ old('input_period_end', $period->input_period_end ? \Carbon\Carbon::parse($period->input_period_end)->format('Y-m-d') : '') }}" 
                                       required
                                       {{ $period->status !== 'open' ? 'readonly' : '' }}>
                                @error('input_period_end')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                                <small class="text-muted">@lang('payroll.input_period_end_hint')</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Pay Date -->
                <div class="form-group">
                    <label class="col-md-2 control-label">@lang('payroll.pay_date') <span class="text-danger">*</span></label>
                    <div class="col-md-10">
                        <input type="date" name="pay_date" class="form-control" 
                               value="{{ old('pay_date', $period->pay_date->format('Y-m-d')) }}" required
                               {{ $period->status !== 'open' ? 'readonly' : '' }}>
                        @error('pay_date')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                        <small class="text-muted">@lang('payroll.pay_date_hint')</small>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-2 control-label">@lang('payroll.current_status')</label>
                    <div class="col-md-10">
                        <p class="form-control-static">
                            @if($period->status === 'open')
                                <span class="label label-success">@lang('payroll.open')</span>
                            @elseif($period->status === 'closed')
                                <span class="label label-danger">@lang('payroll.closed')</span>
                            @else
                                <span class="label label-warning">{{ ucfirst($period->status) }}</span>
                            @endif
                            
                            @if($period->is_current)
                                <span class="label label-primary">@lang('payroll.current')</span>
                            @endif
                        </p>
                        <small class="text-muted">@lang('payroll.status_change_hint')</small>
                    </div>
                </div>

                @if($period->payrollRecords->count() > 0)
                <div class="form-group">
                    <label class="col-md-2 control-label">@lang('payroll.payroll_records')</label>
                    <div class="col-md-10">
                        <p class="form-control-static">
                            <span class="badge">{{ $period->payrollRecords->count() }}</span> @lang('payroll.records_exist')
                        </p>
                        <small class="text-warning">
                            <i class="fa fa-warning"></i> @lang('payroll.records_warning')
                        </small>
                    </div>
                </div>
                @endif

                <div class="form-group">
                    <div class="col-md-10 col-md-offset-2">
                        @if($period->status === 'open')
                            <button type="submit" class="btn btn-success">
                                <i class="fa fa-save"></i> @lang('payroll.update_period')
                            </button>
                        @endif
                        <a href="{{ route('payroll.settings.periods.show', $period) }}" class="btn btn-info">
                            <i class="fa fa-eye"></i> @lang('common.view_details')
                        </a>
                        <a href="{{ route('payroll.settings.periods.index') }}" class="btn btn-default">
                            <i class="fa fa-arrow-left"></i> @lang('common.back_to_list')
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
$(document).ready(function() {
    // Auto-calculate dates based on period type and start date
    $('select[name="period_type"], input[name="start_date"]').on('change', function() {
        var periodType = $('select[name="period_type"]').val();
        var startDate = new Date($('input[name="start_date"]').val());
        
        if (periodType && startDate && !$('select[name="period_type"]').prop('disabled')) {
            var endDate = new Date(startDate);
            var inputPeriodStart = new Date(startDate);
            var inputPeriodEnd = new Date(startDate);
            var payDate = new Date(startDate);
            
            if (periodType === 'monthly') {
                // End of month
                endDate = new Date(startDate.getFullYear(), startDate.getMonth() + 1, 0);
                // Input period typically starts a few days before the actual period
                inputPeriodStart.setDate(startDate.getDate() - 3); // 3 days before period start
                inputPeriodEnd = new Date(endDate);
                payDate = new Date(endDate);
                payDate.setDate(payDate.getDate() + 5); // 5 days after month end
            } else if (periodType === 'weekly') {
                // End of week (Sunday)
                endDate.setDate(startDate.getDate() + (6 - startDate.getDay()));
                // Input period starts a couple days before the work week
                inputPeriodStart.setDate(startDate.getDate() - 2); // 2 days before week start
                inputPeriodEnd = new Date(endDate);
                payDate = new Date(endDate);
                payDate.setDate(payDate.getDate() + 2); // 2 days after week end
            } else if (periodType === 'bi-weekly') {
                // 14 days later
                endDate.setDate(startDate.getDate() + 13);
                // Input period starts a few days before the work period
                inputPeriodStart.setDate(startDate.getDate() - 3); // 3 days before period start
                inputPeriodEnd = new Date(endDate);
                payDate = new Date(endDate);
                payDate.setDate(payDate.getDate() + 2); // 2 days after period end
            }
            
            $('input[name="end_date"]').val(endDate.toISOString().split('T')[0]);
            $('input[name="input_period_start"]').val(inputPeriodStart.toISOString().split('T')[0]);
            $('input[name="input_period_end"]').val(inputPeriodEnd.toISOString().split('T')[0]);
            $('input[name="pay_date"]').val(payDate.toISOString().split('T')[0]);
        }
    });
});
</script>
@endsection