@extends('admin.master')

@section('title')
    StawiHR - Salary History
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row bg-title">
            <div class="col-lg-8 col-md-8 col-sm-8">
                <ol class="breadcrumb">
                    <li class="active breadcrumbColor">
                        <a href="{{ url('dashboard') }}"><i class="fa fa-home"></i> @lang('dashboard.dashboard')</a>
                    </li>
                    <li><a href="{{ route('payroll.dashboard') }}">Payroll</a></li>
                    <li>Salary History</li>
                </ol>
            </div>
            <div class="col-lg-4 col-sm-4 col-md-4">
                <a href="{{ route('payroll.salary.history.export', request()->all()) }}"
                    class="btn btn-success pull-right m-l-20 waves-effect waves-light">
                    <i class="fa fa-download"></i> Export to Excel
                </a>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="row">
            <div class="col-lg-3 col-sm-6 col-xs-12">
                <div class="white-box analytics-info">
                    <h3 class="box-title">Total Changes</h3>
                    <ul class="list-inline two-part">
                        <li><i class="fa fa-exchange text-info"></i></li>
                        <li class="text-right"><span class="counter text-info">{{ $stats['total_changes'] }}</span></li>
                    </ul>
                </div>
            </div>
            <div class="col-lg-3 col-sm-6 col-xs-12">
                <div class="white-box analytics-info">
                    <h3 class="box-title">This Year</h3>
                    <ul class="list-inline two-part">
                        <li><i class="fa fa-calendar text-success"></i></li>
                        <li class="text-right"><span class="counter text-success">{{ $stats['changes_this_year'] }}</span>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="col-lg-3 col-sm-6 col-xs-12">
                <div class="white-box analytics-info">
                    <h3 class="box-title">This Month</h3>
                    <ul class="list-inline two-part">
                        <li><i class="fa fa-calendar-check-o text-purple"></i></li>
                        <li class="text-right"><span class="counter text-purple">{{ $stats['changes_this_month'] }}</span>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="col-lg-3 col-sm-6 col-xs-12">
                <div class="white-box analytics-info">
                    <h3 class="box-title">Avg Increase</h3>
                    <ul class="list-inline two-part">
                        <li><i class="fa fa-money text-danger"></i></li>
                        <li class="text-right">
                            <span class="text-danger">KES
                                {{ number_format($stats['average_increase'] ?? 0, 2) }}</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading">Filter Salary History</div>
                    <div class="panel-body">
                        <form method="GET" action="{{ route('payroll.salary.history.index') }}">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="employee_id">Employee</label>
                                        <select name="employee_id" id="employee_id" class="form-control select2">
                                            <option value="">All Employees</option>
                                            @foreach ($employees as $employee)
                                                <option value="{{ $employee->employee_id }}"
                                                    {{ request('employee_id') == $employee->employee_id ? 'selected' : '' }}>
                                                    {{ $employee->first_name }} {{ $employee->last_name }}
                                                    ({{ $employee->employee_id }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="change_type">Change Type</label>
                                        <select name="change_type" id="change_type" class="form-control">
                                            <option value="">All Types</option>
                                            @foreach ($changeTypes as $key => $label)
                                                <option value="{{ $key }}"
                                                    {{ request('change_type') == $key ? 'selected' : '' }}>
                                                    {{ $label }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="date_from">From Date</label>
                                        <input type="date" name="date_from" id="date_from" class="form-control"
                                            value="{{ request('date_from') }}">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="date_to">To Date</label>
                                        <input type="date" name="date_to" id="date_to" class="form-control"
                                            value="{{ request('date_to') }}">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="search">Search</label>
                                        <input type="text" name="search" id="search" class="form-control"
                                            placeholder="Name or ID" value="{{ request('search') }}">
                                    </div>
                                </div>
                                <div class="col-md-1">
                                    <div class="form-group">
                                        <label>&nbsp;</label>
                                        <button type="submit" class="btn btn-info form-control">
                                            <i class="fa fa-search"></i> Filter
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Salary History Table -->
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-info">
                    <div class="panel-heading">
                        <i class="fa fa-history fa-fw"></i> Salary Change History
                        <span class="badge badge-primary">{{ $salaryHistory->total() }} records</span>
                    </div>
                    <div class="panel-wrapper collapse in" aria-expanded="true">
                        <div class="panel-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr class="success">
                                            <th>Employee</th>
                                            <th>Effective Date</th>
                                            <th>Previous Salary</th>
                                            <th>New Salary</th>
                                            <th>Change Amount</th>
                                            <th>Change %</th>
                                            <th>Change Type</th>
                                            <th>Reason</th>
                                            <th>Changed By</th>
                                            <th>Date Changed</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($salaryHistory as $history)
                                            <tr>
                                                <td>
                                                    <strong>{{ $history->employee->first_name }}
                                                        {{ $history->employee->last_name }}</strong>
                                                    <br>
                                                    <small class="text-muted">
                                                        {{ $history->employee->employee_id }} |
                                                        {{ $history->employee->department->department_name ?? 'N/A' }}
                                                    </small>
                                                </td>
                                                <td>
                                                    {{ \Carbon\Carbon::parse($history->effective_date)->format('d M Y') }}
                                                </td>
                                                <td class="text-right">
                                                    <strong>KES {{ number_format($history->previous_salary, 2) }}</strong>
                                                </td>
                                                <td class="text-right">
                                                    <strong>KES {{ number_format($history->new_salary, 2) }}</strong>
                                                </td>
                                                <td class="text-right">
                                                    @php
                                                        $changeAmount = $history->salary_change_amount;
                                                        $changeClass =
                                                            $changeAmount > 0
                                                                ? 'text-success'
                                                                : ($changeAmount < 0
                                                                    ? 'text-danger'
                                                                    : 'text-muted');
                                                    @endphp
                                                    <strong class="{{ $changeClass }}">
                                                        {{ $changeAmount > 0 ? '+' : '' }}KES
                                                        {{ number_format($changeAmount, 2) }}
                                                    </strong>
                                                </td>
                                                <td class="text-center">
                                                    @php
                                                        $percentageClass =
                                                            $history->salary_change_percentage > 0
                                                                ? 'text-success'
                                                                : ($history->salary_change_percentage < 0
                                                                    ? 'text-danger'
                                                                    : 'text-muted');
                                                    @endphp
                                                    <span class="badge {{ $percentageClass }}">
                                                        {{ $history->salary_change_percentage > 0 ? '+' : '' }}{{ number_format($history->salary_change_percentage, 1) }}%
                                                    </span>
                                                </td>
                                                <td>
                                                    <span
                                                        class="label 
                                                    @if ($history->change_type == 'promotion') label-success
                                                    @elseif($history->change_type == 'increment') label-info
                                                    @elseif($history->change_type == 'adjustment') label-warning
                                                    @elseif($history->change_type == 'demotion') label-danger
                                                    @else label-default @endif">
                                                        {{ ucfirst($history->change_type) }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <small>{{ Str::limit($history->change_reason, 50) }}</small>
                                                </td>
                                                <td>
                                                    <small>{{ $history->changedBy->name ?? 'System' }}</small>
                                                </td>
                                                <td>
                                                    <small>{{ \Carbon\Carbon::parse($history->created_at)->format('d M Y H:i') }}</small>
                                                </td>
                                                <td>
                                                    <a href="{{ route('payroll.employees.salary-history', $history->employee_id) }}"
                                                        class="btn btn-xs btn-info" title="View Employee History"
                                                        style="color: white">
                                                        <i class="fa fa-user">View</i>
                                                    </a>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="11" class="text-center">
                                                    <div class="alert alert-info">
                                                        <i class="fa fa-info-circle"></i> No salary change records found.
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            <!-- Pagination -->
                            <div class="text-center">
                                {{ $salaryHistory->appends(request()->query())->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('page_scripts')
    <script>
        $(document).ready(function() {
            // Auto-submit form when filters change
            $('#employee_id, #change_type').change(function() {
                $(this).closest('form').submit();
            });
        });
    </script>
@endsection
