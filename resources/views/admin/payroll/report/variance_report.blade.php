@extends('admin.master')

@section('title', __('payroll.variance_report'))

@section('content')
    <div class="row bg-title">
        <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
            <h4 class="page-title">@lang('payroll.process_payroll')</h4>
        </div>
        <div class="col-lg-9 col-sm-8 col-md-8 col-xs-12">
            <ol class="breadcrumb">
                <li class="active breadcrumbColor">
                    <a href="{{ url('dashboard') }}"><i class="fa fa-home"></i> @lang('dashboard.dashboard')</a>
                </li>
                <li><a href="{{ route('payroll.dashboard') }}">@lang('payroll.payroll')</a></li>
                <li>@lang('payroll.process')</li>
            </ol>
        </div>
    </div>


<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Payroll Variance Report</h4>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('payroll.reports.variance') }}">
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="payroll_period_id" class="form-label">Select Payroll Period</label>
                                <select class="form-control" id="payroll_period_id" name="payroll_period_id" required>
                                    <option value="">Select a payroll period</option>
                                    @foreach($payrollPeriods as $period)
                                        <option value="{{ $period->id }}"
                                                {{ request('payroll_period_id') == $period->id ? 'selected' : '' }}>
                                            {{ $period->name }} ({{ $period->start_date->format('M Y') }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="compare_period_id" class="form-label">Compare With Payroll Period (Optional)</label>
                                <select class="form-control" id="compare_period_id" name="compare_period_id">
                                    <option value="">Select a comparison period</option>
                                    @foreach($payrollPeriods as $period)
                                        <option value="{{ $period->id }}"
                                                {{ request('compare_period_id') == $period->id ? 'selected' : '' }}>
                                            {{ $period->name }} ({{ $period->start_date->format('M Y') }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            @include('admin.payroll.report.partials.company_filter')
                            <div class="col-md-4">
                                <label for="department_id" class="form-label">Department (Optional)</label>
                                <select class="form-control" id="department_id" name="department_id">
                                    <option value="">All Departments</option>
                                    @foreach($departments as $department)
                                        <option value="{{ $department->id }}"
                                                {{ request('department_id') == $department->id ? 'selected' : '' }}>
                                            {{ $department->department_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label>&nbsp;</label>
                                <div class="form-group">
                                    <button type="submit" name="action" value="Filter" class="btn btn-primary">
                                        <i class="fa fa-filter"></i> Filter
                                    </button>
                                    <button type="submit" name="action" value="Download" class="btn btn-success">
                                        <i class="fa fa-download"></i> Download Excel
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>

                    @if(isset($currentPeriod) && isset($previousPeriod) && ($currentPeriodData->isNotEmpty() || $previousPeriodData->isNotEmpty()))
                        <div class="alert alert-info">
                            <strong>Comparison:</strong>
                            {{ $currentPeriod->name }} vs {{ $previousPeriod->name }}
                            <br><strong>Company:</strong> {{ $selectedCompanyName ?? 'All Companies' }}
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <h5>Current Period Summary</h5>
                                <p><strong>Period:</strong> {{ $currentPeriod->name }}</p>
                                <p><strong>Employees:</strong> {{ $currentPeriodData->count() }}</p>
                                <p><strong>Total Gross:</strong> KES {{ number_format($currentPeriodData->sum('gross_salary'), 2) }}</p>
                            </div>
                            <div class="col-md-6">
                                <h5>Previous Period Summary</h5>
                                <p><strong>Period:</strong> {{ $previousPeriod->name }}</p>
                                <p><strong>Employees:</strong> {{ $previousPeriodData->count() }}</p>
                                <p><strong>Total Gross:</strong> KES {{ number_format($previousPeriodData->sum('gross_salary'), 2) }}</p>
                            </div>
                        </div>

                        <div class="mt-4">
                            <p class="text-muted">
                                Use the "Download Excel" button to export a detailed variance report with two sheets:
                                <br>
                                <strong>1. Variance by item totals</strong> - Summary of earnings variances by type
                                <br>
                                <strong>2. Variance by individual</strong> - Detailed individual employee variances
                            </p>
                        </div>
                    @else
                        <div class="alert alert-warning">
                            @if(!request()->filled('payroll_period_id'))
                                Please select a payroll period and department (optional) to view the variance report.
                            @elseif(isset($currentPeriod) && !$previousPeriod)
                                No previous payroll period found for comparison with {{ $currentPeriod->name }}.
                            @elseif(isset($currentPeriod) && $currentPeriodData->isEmpty() && $previousPeriodData->isEmpty())
                                No payroll data found for the selected periods.
                            @else
                                No data available for the selected month or no previous period for comparison.
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Auto-select current payroll period if no selection made
    @if(!request('payroll_period_id') && isset($currentPeriod))
        $('#payroll_period_id').val('{{ $currentPeriod->id }}');
    @endif
});
</script>
@endpush