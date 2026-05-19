@extends('admin.master')

@section('title', __('payroll.period_details'))

@section('content')
<div class="row bg-title">
    <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
        <h4 class="page-title">@lang('payroll.period_details')</h4>
    </div>
    <div class="col-lg-9 col-sm-8 col-md-8 col-xs-12">
        <ol class="breadcrumb">
            <li class="active breadcrumbColor">
                <a href="{{ url('dashboard') }}"><i class="fa fa-home"></i> @lang('dashboard.dashboard')</a>
            </li>
            <li><a href="{{ route('payroll.dashboard') }}">@lang('payroll.payroll')</a></li>
            <li><a href="{{ route('payroll.settings.periods.index') }}">@lang('payroll.periods')</a></li>
            <li>{{ $period->name }}</li>
        </ol>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="white-box">
            <div class="row">
                <div class="col-md-8">
                    <h3 class="box-title m-b-0">{{ $period->name }}</h3>
                    <p class="text-muted m-b-30">@lang('payroll.period_details_summary')</p>
                </div>
                <div class="col-md-4 text-right">
                    @if($period->status === 'open')
                        <a href="{{ route('payroll.settings.periods.edit', $period) }}" class="btn btn-warning">
                            <i class="fa fa-edit"></i> @lang('common.edit')
                        </a>
                    @endif
                    <a href="{{ route('payroll.settings.periods.index') }}" class="btn btn-default">
                        <i class="fa fa-arrow-left"></i> @lang('common.back')
                    </a>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <table class="table table-borderless">
                        <tr>
                            <td><strong>@lang('payroll.period_type'):</strong></td>
                            <td>
                                <span class="label label-default">{{ ucfirst($period->period_type) }}</span>
                            </td>
                        </tr>
                        <tr>
                            <td><strong>@lang('payroll.start_date'):</strong></td>
                            <td>{{ $period->start_date->format('F d, Y') }}</td>
                        </tr>
                        <tr>
                            <td><strong>@lang('payroll.end_date'):</strong></td>
                            <td>{{ $period->end_date->format('F d, Y') }}</td>
                        </tr>
                        <tr>
                            <td><strong>@lang('payroll.pay_date'):</strong></td>
                            <td>{{ $period->pay_date->format('F d, Y') }}</td>
                        </tr>
                        <tr>
                            <td><strong>@lang('common.status'):</strong></td>
                            <td>
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
                            </td>
                        </tr>
                        <tr>
                            <td><strong>@lang('payroll.duration'):</strong></td>
                            <td>{{ $period->start_date->diffInDays($period->end_date) + 1 }} @lang('payroll.days')</td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <table class="table table-borderless">
                        <tr>
                            <td><strong>@lang('common.created_by'):</strong></td>
                            <td>{{ $period->creator->name ?? 'System' }}</td>
                        </tr>
                        <tr>
                            <td><strong>@lang('common.created_at'):</strong></td>
                            <td>{{ $period->created_at->format('M d, Y H:i') }}</td>
                        </tr>
                        @if($period->updated_at != $period->created_at)
                        <tr>
                            <td><strong>@lang('common.last_updated'):</strong></td>
                            <td>{{ $period->updated_at->format('M d, Y H:i') }}</td>
                        </tr>
                        @endif
                        @if($period->updater)
                        <tr>
                            <td><strong>@lang('common.updated_by'):</strong></td>
                            <td>{{ $period->updater->name }}</td>
                        </tr>
                        @endif
                    </table>
                </div>
            </div>
        </div>

        @if($period->payrollRecords->count() > 0)
        <div class="white-box">
            <h4 class="box-title">@lang('payroll.payroll_records')</h4>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>@lang('employee.employee')</th>
                            <th>@lang('payroll.basic_salary')</th>
                            <th>@lang('payroll.gross_pay')</th>
                            <th>@lang('payroll.deductions')</th>
                            <th>@lang('payroll.net_pay')</th>
                            <th>@lang('common.status')</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($period->payrollRecords as $record)
                        <tr>
                            <td>
                                <strong>{{ $record->employeePayroll->employee->name }}</strong><br>
                                <small class="text-muted">{{ $record->employeePayroll->employee->employee_id }}</small>
                            </td>
                            <td>{{ number_format($record->basic_salary, 2) }}</td>
                            <td>{{ number_format($record->gross_salary, 2) }}</td>
                            <td>{{ number_format($record->total_deductions, 2) }}</td>
                            <td><strong>{{ number_format($record->net_salary, 2) }}</strong></td>
                            <td>
                                @if($record->status === 'paid')
                                    <span class="label label-success">@lang('payroll.paid')</span>
                                @elseif($record->status === 'approved')
                                    <span class="label label-info">@lang('payroll.approved')</span>
                                @else
                                    <span class="label label-warning">{{ ucfirst($record->status) }}</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif
    </div>

    <div class="col-md-4">
        <div class="white-box">
            <h4 class="box-title">@lang('payroll.period_summary')</h4>
            <div class="row">
                <div class="col-xs-6">
                    <div class="text-center">
                        <h2 class="text-info">{{ $summary['total_employees'] ?? 0 }}</h2>
                        <p class="text-muted">@lang('employee.employees')</p>
                    </div>
                </div>
                <div class="col-xs-6">
                    <div class="text-center">
                        <h2 class="text-success">{{ number_format($summary['total_gross_salary'] ?? 0, 0) }}</h2>
                        <p class="text-muted">@lang('payroll.gross_pay')</p>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-6">
                    <div class="text-center">
                        <h2 class="text-warning">{{ number_format($summary['total_deductions'] ?? 0, 0) }}</h2>
                        <p class="text-muted">@lang('payroll.deductions')</p>
                    </div>
                </div>
                <div class="col-xs-6">
                    <div class="text-center">
                        <h2 class="text-primary">{{ number_format($summary['total_net_salary'] ?? 0, 0) }}</h2>
                        <p class="text-muted">@lang('payroll.net_pay')</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="white-box">
            <h4 class="box-title">@lang('common.actions')</h4>
            <div class="list-group">
                @if($period->status === 'open')
                    @if(!$period->is_current)
                        <a href="{{ route('payroll.settings.periods.set-current', $period) }}" 
                           class="list-group-item"
                           onclick="return confirm('@lang('payroll.set_current_confirmation')')">
                            <i class="fa fa-check text-primary"></i> @lang('payroll.set_as_current')
                        </a>
                    @endif
                    
                    <a href="{{ route('payroll.settings.periods.close', $period) }}" 
                       class="list-group-item"
                       onclick="return confirm('@lang('payroll.close_period_confirmation')')">
                        <i class="fa fa-lock text-danger"></i> @lang('payroll.close_period')
                    </a>
                @endif
                
                @if($period->status === 'closed')
                    <a href="{{ route('payroll.settings.periods.reopen', $period) }}" 
                       class="list-group-item"
                       onclick="return confirm('@lang('payroll.reopen_period_confirmation')')">
                        <i class="fa fa-unlock text-success"></i> @lang('payroll.reopen_period')
                    </a>
                @endif
                
                @if($period->payrollRecords->count() == 0 && !$period->is_current)
                    <form method="POST" action="{{ route('payroll.settings.periods.delete', $period) }}" 
                          onsubmit="return confirm('@lang('payroll.delete_period_confirmation')')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="list-group-item text-danger" style="border: none; background: none; width: 100%; text-align: left;">
                            <i class="fa fa-trash"></i> @lang('common.delete')
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection