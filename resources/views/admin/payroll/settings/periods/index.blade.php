@extends('admin.master')

@section('title', __('payroll.payroll_periods'))

@section('content')
    <div class="row bg-title">
        <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
            <h4 class="page-title">@lang('payroll.payroll_periods')</h4>
        </div>
        <div class="col-lg-9 col-sm-8 col-md-8 col-xs-12">
            <ol class="breadcrumb">
                <li class="active breadcrumbColor">
                    <a href="{{ url('dashboard') }}"><i class="fa fa-home"></i> @lang('dashboard.dashboard')</a>
                </li>
                <li><a href="{{ route('payroll.dashboard') }}">@lang('payroll.payroll')</a></li>
                <li>@lang('payroll.periods')</li>
            </ol>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12">
            @include('admin.partials.alert')
            <div class="white-box">
                <div class="row">
                    <div class="col-md-6">
                        <h3 class="box-title m-b-0">@lang('payroll.payroll_periods_management')</h3>
                        <p class="text-muted m-b-30">@lang('payroll.manage_payroll_periods_status')</p>
                    </div>
                    <div class="col-md-6 text-right">
                        <a href="{{ route('payroll.settings.periods.create') }}" class="btn btn-success btn-outline">
                            <i class="fa fa-plus"></i> @lang('payroll.create_period')
                        </a>

                        <button type="button" class="btn btn-info btn-outline" data-toggle="modal"
                            data-target="#generatePeriodsModal">
                            <i class="fa fa-calendar"></i> @lang('payroll.generate_multiple')
                        </button>
                        <a href="{{ route('payroll.process.form') }}" class="btn btn-success">
                            <i class="fa fa-cash"></i> @lang('payroll.process_payroll')
                        </a>
                        <a href="{{ route('payroll.dashboard') }}" class="btn btn-success">
                            <i class="fa fa-home"></i> Payroll Dashboard
                        </a>
                    </div>
                </div>

                @if ($currentPeriod)
                    <div class="alert alert-info">
                        <strong>@lang('payroll.current_period'):</strong> {{ $currentPeriod->name }}
                        ({{ $currentPeriod->start_date->format('M d, Y') }} -
                        {{ $currentPeriod->end_date->format('M d, Y') }})
                    </div>
                @endif

                <div class="table-responsive">
                    <table id="myTable" class="table table-striped">
                        <thead>
                            <tr>
                                <th>@lang('payroll.period_name')</th>
                                <th>@lang('payroll.type')</th>
                                <th>@lang('payroll.start_date')</th>
                                <th>@lang('payroll.end_date')</th>
                                <th>@lang('payroll.input_period_start')</th>
                                <th>@lang('payroll.input_period_end')</th>
                                <th>@lang('payroll.pay_date')</th>
                                <th>@lang('payroll.status')</th>
                                <th>@lang('payroll.records')</th>
                                <th>@lang('payroll.actions')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if ($periods->count() > 0)
                                @forelse($periods as $period)
                                    <tr>
                                        <td>
                                            <strong>{{ $period->name }}</strong>
                                            @if ($period->is_current)
                                                <span class="label label-primary">@lang('payroll.current')</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="label label-primary">{{ ucfirst($period->period_type) }}</span>
                                        </td>
                                        <td>{{ $period->start_date->format('M d, Y') }}</td>
                                        <td>{{ $period->end_date->format('M d, Y') }}</td>
                                        <td>{{ $period->input_period_start->format('M d, Y') }}</td>
                                        <td>{{ $period->input_period_end->format('M d, Y') }}</td>
                                        <td>{{ $period->pay_date->format('M d, Y') }}</td>
                                        <td>
                                            @if ($period->status === 'open')
                                                <span class="label label-success">@lang('payroll.open')</span>
                                            @elseif($period->status === 'closed')
                                                <span class="label label-danger">@lang('payroll.closed')</span>
                                            @else
                                                <span class="label label-warning">{{ ucfirst($period->status) }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge">{{ $period->payroll_records_count }}</span>
                                        </td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="{{ route('payroll.settings.periods.show', $period) }}"
                                                    class="btn btn-info btn-xs" title="@lang('payroll.view')">
                                                    <i class="fa fa-eye"></i> @lang('payroll.view')
                                                </a>

                                                @if ($period->status === 'open')
                                                    <a href="{{ route('payroll.settings.periods.edit', $period) }}"
                                                        class="btn btn-warning btn-xs" title="@lang('payroll.edit')">
                                                        <i class="fa fa-edit"></i> @lang('payroll.edit')
                                                    </a>

                                                    @if (!$period->is_current)
                                                        <a href="{{ route('payroll.settings.periods.set-current', $period) }}"
                                                            class="btn btn-primary btn-xs" title="@lang('payroll.set_as_current')"
                                                            onclick="return confirm('@lang('payroll.set_this_period_as_current')')">
                                                            <i class="fa fa-check"></i> @lang('payroll.set_current')
                                                        </a>
                                                    @endif

                                                    <a href="{{ route('payroll.settings.periods.close', $period) }}"
                                                        class="btn btn-danger btn-xs" title="@lang('payroll.close_period')"
                                                        onclick="return confirm('@lang('payroll.close_this_payroll_period')')">
                                                        <i class="fa fa-lock"></i> @lang('payroll.close')
                                                    </a>
                                                @endif

                                                @if ($period->status === 'closed')
                                                    <a href="{{ route('payroll.settings.periods.reopen', $period) }}"
                                                        class="btn btn-success btn-xs" title="@lang('payroll.reopen')"
                                                        onclick="return confirm('@lang('payroll.reopen_this_payroll_period')')">
                                                        <i class="fa fa-unlock"></i> @lang('payroll.reopen')
                                                    </a>
                                                @endif

                                                @if ($period->payroll_records_count == 0 && !$period->is_current)
                                                    <form method="POST"
                                                        action="{{ route('payroll.settings.periods.delete', $period) }}"
                                                        style="display: inline-block;"
                                                        onsubmit="return confirm('@lang('payroll.delete_this_period_permanently')')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger btn-xs"
                                                            title="@lang('payroll.delete')">
                                                            <i class="fa fa-trash"></i> @lang('payroll.delete')
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center">
                                            <p class="text-muted">@lang('payroll.no_payroll_periods_found')</p>
                                            <a href="{{ route('payroll.settings.periods.create') }}"
                                                class="btn btn-success">
                                                <i class="fa fa-plus"></i> @lang('payroll.create_first_period')
                                            </a>
                                        </td>
                                    </tr>
                                @endforelse
                            @endif
                        </tbody>
                    </table>
                </div>


            </div>
        </div>
    </div>

    <!-- Generate Multiple Periods Modal -->
    <div class="modal fade" id="generatePeriodsModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form method="POST" action="{{ route('payroll.settings.periods.generate-periods') }}">
                    @csrf
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">@lang('payroll.generate_multiple_periods')</h4>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>@lang('payroll.period_type')</label>
                            <select name="period_type" class="form-control" required>
                                <option value="monthly">@lang('payroll.monthly')</option>
                                <option value="weekly">@lang('payroll.weekly')</option>
                                <option value="bi-weekly">@lang('payroll.bi_weekly')</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>@lang('payroll.start_date')</label>
                            <input type="date" name="start_date" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>@lang('payroll.number_of_periods')</label>
                            <input type="number" name="number_of_periods" class="form-control" min="1"
                                max="12" value="6" required>
                            <small class="text-muted">@lang('payroll.maximum_12_periods_generated')</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">@lang('payroll.cancel')</button>
                        <button type="submit" class="btn btn-success">@lang('payroll.generate_periods')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        $(document).ready(function() {
            // Set default start date to next month
            var nextMonth = new Date();
            nextMonth.setMonth(nextMonth.getMonth() + 1);
            nextMonth.setDate(1);
            $('input[name="start_date"]').val(nextMonth.toISOString().split('T')[0]);
        });
    </script>
@endsection
