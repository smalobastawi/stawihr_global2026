@extends('admin.master')

@section('title', __('payroll.create_period'))

@section('content')
    <div class="row bg-title">
        <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
            <h4 class="page-title">@lang('payroll.create_period')</h4>
        </div>
        <div class="col-lg-9 col-sm-8 col-md-8 col-xs-12">
            <ol class="breadcrumb">
                <li class="active breadcrumbColor">
                    <a href="{{ url('dashboard') }}"><i class="fa fa-home"></i> @lang('dashboard.dashboard')</a>
                </li>
                <li><a href="{{ route('payroll.dashboard') }}">@lang('payroll.payroll')</a></li>
                <li><a href="{{ route('payroll.settings.periods.index') }}">@lang('payroll.periods')</a></li>
                <li>@lang('payroll.create')</li>
            </ol>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12">
            @include('admin.partials.alert')
            <div class="white-box">
                <h3 class="box-title m-b-0">@lang('payroll.create_new_period')</h3>
                <p class="text-muted m-b-30">@lang('payroll.define_new_period')</p>

                <form method="POST" action="{{ route('payroll.settings.periods.store') }}" class="form-horizontal">
                    @csrf

                    <div class="form-group">
                        <label class="col-md-2 control-label">@lang('payroll.period_name') <span class="text-danger">*</span></label>
                        <div class="col-md-10">
                            <input type="text" name="name" class="form-control" value="{{ old('name') }}"
                                placeholder="@lang('payroll.period_name_placeholder')" required>
                            @error('name')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-md-2 control-label">@lang('payroll.period_type') <span class="text-danger">*</span></label>
                        <div class="col-md-10">
                            <select name="period_type" class="form-control" required>
                                <option value="">@lang('payroll.select_period_type')</option>
                                <option value="monthly" {{ old('period_type') == 'monthly' ? 'selected' : '' }}>
                                    @lang('payroll.monthly')</option>
                                <option value="weekly" {{ old('period_type') == 'weekly' ? 'selected' : '' }}>
                                    @lang('payroll.weekly')</option>
                                <option value="bi-weekly" {{ old('period_type') == 'bi-weekly' ? 'selected' : '' }}>
                                    @lang('payroll.bi_weekly')</option>
                            </select>
                            @error('period_type')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-md-2 control-label">@lang('payroll.start_date') <span class="text-danger">*</span></label>
                        <div class="col-md-10">
                            <input type="date" name="start_date" class="form-control"
                                value="{{ old('start_date', $suggestedStartDate->format('Y-m-d')) }}" required>
                            @error('start_date')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-md-2 control-label">@lang('payroll.end_date') <span class="text-danger">*</span></label>
                        <div class="col-md-10">
                            <input type="date" name="end_date" class="form-control" value="{{ old('end_date') }}"
                                required>
                            @error('end_date')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                            <small class="text-muted">@lang('payroll.end_date_hint')</small>
                        </div>
                    </div>

                    <!-- Add these new fields for payroll input period -->
                    <div class="form-group">
                        <label class="col-md-2 control-label">@lang('payroll.input_period_start') <span class="text-danger">*</span></label>
                        <div class="col-md-10">
                            <input type="date" name="input_period_start" class="form-control"
                                value="{{ old('input_period_start', $suggestedStartDate->format('d-m-Y')) }}" required>
                            @error('input_period_start')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                            <small class="text-muted">@lang('payroll.input_period_start_hint')</small>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-md-2 control-label">@lang('payroll.input_period_end') <span class="text-danger">*</span></label>
                        <div class="col-md-10">
                            <input type="date" name="input_period_end" class="form-control"
                                value="{{ old('input_period_end') }}" required>
                            @error('input_period_end')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                            <small class="text-muted">@lang('payroll.input_period_end_hint')</small>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-md-2 control-label">@lang('payroll.pay_date') <span class="text-danger">*</span></label>
                        <div class="col-md-10">
                            <input type="date" name="pay_date" class="form-control" value="{{ old('pay_date') }}"
                                required>
                            @error('pay_date')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                            <small class="text-muted">@lang('payroll.pay_date_hint')</small>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-10 col-md-offset-2">
                            <div class="">
                                <label>
                                    <input type="checkbox" name="is_current" value="1"
                                        {{ old('is_current') ? 'checked' : '' }}>
                                    @lang('payroll.set_as_current')
                                </label>
                            </div>
                            <small class="text-muted">@lang('payroll.current_period_hint')</small>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-10 col-md-offset-2">
                            <button type="submit" class="btn btn-success">
                                <i class="fa fa-save"></i> @lang('payroll.create_period')
                            </button>
                            <a href="{{ route('payroll.settings.periods.index') }}" class="btn btn-default">
                                <i class="fa fa-times"></i> @lang('payroll.cancel')
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

                if (periodType && startDate) {
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

            // Auto-generate period name
            $('select[name="period_type"], input[name="start_date"]').on('change', function() {
                var periodType = $('select[name="period_type"]').val();
                var startDate = new Date($('input[name="start_date"]').val());

                if (periodType && startDate && !$('input[name="name"]').val()) {
                    var name = '';
                    var months = ['January', 'February', 'March', 'April', 'May', 'June',
                        'July', 'August', 'September', 'October', 'November', 'December'
                    ];

                    if (periodType === 'monthly') {
                        name = months[startDate.getMonth()] + ' ' + startDate.getFullYear();
                    } else if (periodType === 'weekly') {
                        name = 'Week of ' + months[startDate.getMonth()] + ' ' + startDate.getDate() +
                            ', ' + startDate.getFullYear();
                    } else if (periodType === 'bi-weekly') {
                        var endDate = new Date(startDate);
                        endDate.setDate(startDate.getDate() + 13);
                        name = 'Bi-weekly ' + months[startDate.getMonth()] + ' ' + startDate.getDate() +
                            ' - ' + months[endDate.getMonth()] + ' ' + endDate.getDate() + ', ' + startDate
                            .getFullYear();
                    }

                    $('input[name="name"]').val(name);
                }
            });
        });
    </script>
@endsection
