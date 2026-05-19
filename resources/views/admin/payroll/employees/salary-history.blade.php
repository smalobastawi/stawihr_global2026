@extends('admin.master')
@section('content')
@section('title')
    @lang('payroll.salary_history')
@endsection

<div class="container-fluid">
    <div class="row bg-title">
        <div class="">
            <ol class="breadcrumb">
                <li class="active breadcrumbColor"><a href="{{ url('dashboard') }}"><i class="fa fa-home"></i>
                        @lang('dashboard.dashboard')</a></li>
                <li><a href="{{ route('payroll.employees.index') }}">@lang('payroll.employee_payroll')</a></li>
                <li>@yield('title')</li>
            </ol>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12">
            <div class="panel panel-info">
                <div class="panel-heading">
                    <i class="mdi mdi-history fa-fw"></i>
                    @lang('payroll.salary_history_for') {{ $employee->first_name }} {{ $employee->last_name }}
                    <span class="pull-right">
                        <a href="{{ route('payroll.employees.edit', $employee->employeePayroll->id) }}"
                            class="btn btn-primary btn-sm">
                            <i class="fa fa-edit"></i> @lang('payroll.edit_payroll')
                        </a>
                    </span>
                </div>
                <div class="panel-wrapper collapse in" aria-expanded="true">
                    <div class="panel-body">
                        @if ($salaryHistory->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered">
                                    <thead>
                                        <tr>
                                            <th>@lang('payroll.effective_date')</th>
                                            <th>@lang('payroll.previous_salary')</th>
                                            <th>@lang('payroll.new_salary')</th>
                                            <th>@lang('payroll.change_amount')</th>
                                            <th>@lang('payroll.change_percentage')</th>
                                            <th>@lang('payroll.change_type')</th>
                                            <th>@lang('payroll.change_reason')</th>
                                            <th>@lang('common.changed_by')</th>
                                            <th>@lang('common.date')</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($salaryHistory as $history)
                                            <tr>
                                                <td>{{ $history->effective_date->format('M d, Y') }}</td>
                                                <td>KES {{ number_format($history->previous_salary, 2) }}</td>
                                                <td>KES {{ number_format($history->new_salary, 2) }}</td>
                                                <td
                                                    class="{{ $history->salary_change_amount >= 0 ? 'text-success' : 'text-danger' }}">
                                                    {{ $history->salary_change_amount >= 0 ? '+' : '' }}KES
                                                    {{ number_format(abs($history->salary_change_amount), 2) }}
                                                </td>
                                                <td
                                                    class="{{ $history->salary_change_percentage >= 0 ? 'text-success' : 'text-danger' }}">
                                                    {{ $history->salary_change_percentage >= 0 ? '+' : '' }}{{ number_format($history->salary_change_percentage, 2) }}%
                                                </td>
                                                <td>
                                                    <span class="label label-info">
                                                        {{ ucfirst(str_replace('_', ' ', $history->change_type)) }}
                                                    </span>
                                                </td>
                                                <td>{{ $history->change_reason }}</td>
                                                <td>{{ $history->changedBy->name ?? 'System' }}</td>
                                                <td>{{ $history->created_at->format('M d, Y H:i') }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="alert alert-info">
                                <i class="fa fa-info-circle"></i>
                                @lang('payroll.no_salary_history_found')
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
