@extends('admin.master')

@section('title')
    StawiHR - Payroll Dashboard
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row bg-title">
            <div class="col-lg-8 col-md-8 col-sm-8">
                <ol class="breadcrumb">
                    <li class="active breadcrumbColor"><a href="{{ url('dashboard') }}"><i class="fa fa-home"></i>
                            @lang('dashboard.dashboard')</a></li>
                    <li>Payroll Dashboard</li>
                </ol>
            </div>
            <div class="col-lg-4 col-sm-4 col-md-4 col-xs-12">
                @include('admin.partials.alert')
                <div class="form-group pull-right" style="margin-top: 10px;">
                    <select id="monthFilter" class="form-control" style="width: 200px;">
                        @foreach ($periods as $period)
                            <option value="{{ $period->id }}" @if ($period->id == $currentPeriod->id) selected @endif>
                                {{ $period->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <!-- Summary Statistics Cards -->
        <div class="row" id="summaryCards">
            <div class="col-lg-3 col-xs-6">
                <div class="small-box bg-blue">
                    <div class="inner">
                        <h3 id="totalEmployees">{{ number_format($chartData['report_summary']['total_employees'] ?? 0) }}
                        </h3>
                        <p>Total Employees</p>
                        <small>{{ $chartData['report_summary']['period_name'] ?? 'Current Period' }}</small>
                    </div>
                    <div class="icon">
                        <i class="fa fa-users"></i>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-xs-6">
                <div class="small-box bg-green">
                    <div class="inner">
                        <h3 id="totalGross">KES {{ number_format($chartData['report_summary']['total_gross'] ?? 0) }}</h3>
                        <p>Total Gross Salary</p>
                        <small>All employees combined</small>
                    </div>
                    <div class="icon">
                        <i class="fa fa-money"></i>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-xs-6">
                <div class="small-box bg-orange">
                    <div class="inner">
                        <h3 id="totalNet">KES {{ number_format($chartData['report_summary']['total_net'] ?? 0) }}</h3>
                        <p>Total Net Salary</p>
                        <small>After all deductions</small>
                    </div>
                    <div class="icon">
                        <i class="fa fa-calculator"></i>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-xs-6">
                <div class="small-box bg-red">
                    <div class="inner">
                        <h3 id="totalDeductions">KES
                            {{ number_format($chartData['report_summary']['total_deductions'] ?? 0) }}</h3>
                        <p>Total Deductions</p>
                        <small>All statutory & non-statutory</small>
                    </div>
                    <div class="icon">
                        <i class="fa fa-minus-circle"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Row -->
        <div class="row">
            <!-- Payroll Trends Chart -->
            <div class="col-md-8">
                <div class="panel panel-info">
                    <div class="panel-heading">
                        <i class="fa fa-line-chart fa-fw"></i> Payroll Trends (Last 12 Months)
                    </div>
                    <div class="panel-wrapper collapse in" aria-expanded="true">
                        <div class="panel-body">
                            <canvas id="payrollTrendsChart" height="100"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Statutory Deductions Breakdown -->
            <div class="col-md-4">
                <div class="panel panel-success">
                    <div class="panel-heading">
                        <i class="fa fa-pie-chart fa-fw"></i> Current Month Breakdown
                    </div>
                    <div class="panel-wrapper collapse in" aria-expanded="true">
                        <div class="panel-body">
                            <div class="stat-item">
                                <h4>KES {{ number_format($stats['total_gross_salary'] ?? 0) }}</h4>
                                <p class="text-muted">Gross Salary</p>
                            </div>
                            <div class="stat-item">
                                <h4>KES {{ number_format($stats['total_deductions'] ?? 0) }}</h4>
                                <p class="text-muted">Total Deductions</p>
                            </div>
                            <div class="stat-item">
                                <h4>KES {{ number_format($stats['total_net_salary'] ?? 0) }}</h4>
                                <p class="text-muted">Net Salary</p>
                            </div>
                            <div class="progress">
                                @php
                                    $gross = $stats['total_gross_salary'] ?? 1;
                                    $deductionPercentage =
                                        $gross > 0 ? (($stats['total_deductions'] ?? 0) / $gross) * 100 : 0;
                                    $netPercentage = 100 - $deductionPercentage;
                                @endphp
                                <div class="progress-bar progress-bar-success" style="width: {{ $netPercentage }}%">
                                    Net: {{ number_format($netPercentage, 1) }}%
                                </div>
                                <div class="progress-bar progress-bar-warning" style="width: {{ $deductionPercentage }}%">
                                    Deductions: {{ number_format($deductionPercentage, 1) }}%
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statutory Compliance Row -->
        <div class="row">
            <!-- PAYE Summary -->
            <div class="col-md-3">
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        <i class="fa fa-university fa-fw"></i> PAYE Tax
                    </div>
                    <div class="panel-wrapper collapse in" aria-expanded="true">
                        <div class="panel-body text-center">
                            <h3>KES {{ number_format($stats['total_paye'] ?? 0) }}</h3>
                            <p class="text-muted">Current Month</p>
                            <a href="{{ route('reports.paye') }}" class="btn btn-primary btn-sm" style="color: white">
                                <i class="fa fa-file-text"></i> Generate P9
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- NSSF Summary -->
            <div class="col-md-3">
                <div class="panel panel-warning">
                    <div class="panel-heading">
                        <i class="fa fa-shield fa-fw"></i> NSSF
                    </div>
                    <div class="panel-wrapper collapse in" aria-expanded="true">
                        <div class="panel-body text-center">
                            <h3>KES {{ number_format($stats['total_nssf'] ?? 0) }}</h3>
                            <p class="text-muted">Current Month</p>
                            <a href="{{ route('nssfReportsIndex') }}" class="btn btn-warning btn-sm" style="color: white">
                                <i class="fa fa-file-text"></i> Generate Report
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- SHIF Summary -->
            <div class="col-md-3">
                <div class="panel panel-info">
                    <div class="panel-heading">
                        <i class="fa fa-heartbeat fa-fw"></i> SHIF
                    </div>
                    <div class="panel-wrapper collapse in" aria-expanded="true">
                        <div class="panel-body text-center">
                            <h3>KES {{ number_format($stats['total_shif'] ?? 0) }}</h3>
                            <p class="text-muted">Current Month</p>
                            <a href="{{ route('shifReportsIndex') }}" class="btn btn-info btn-sm" style="color: white">
                                <i class="fa fa-file-text"></i> Generate Report
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Housing Levy Summary -->
            <div class="col-md-3">
                <div class="panel panel-success">
                    <div class="panel-heading">
                        <i class="fa fa-home fa-fw"></i> Housing Levy
                    </div>
                    <div class="panel-wrapper collapse in" aria-expanded="true">
                        <div class="panel-body text-center">
                            <h3>KES {{ number_format($stats['total_housing_levy'] ?? 0) }}</h3>
                            <p class="text-muted">Current Month</p>
                            <a href="{{ route('ahlReportIndex') }}" class="btn btn-success btn-sm" style="color: white">
                                <i class="fa fa-file-text"></i> Generate Report
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activities -->
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <i class="fa fa-clock-o fa-fw"></i> Recent Payroll Activities
                    </div>
                    <div class="panel-wrapper collapse in" aria-expanded="true">
                        <div class="panel-body">
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Employee</th>
                                            <th>Period</th>
                                            <th>Gross Salary</th>
                                            <th>Net Salary</th>
                                            <th>Status</th>
                                            <th>Last Updated</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($recentActivities as $activity)
                                            <tr>
                                                <td>
                                                    {{ $activity->employeePayroll->employee->fullName() ?? 'N/A' }}

                                                </td>
                                                <td>{{ $activity->payrollPeriod->name ?? 'N/A' }}</td>
                                                <td>KES {{ number_format($activity->gross_salary) }}</td>
                                                <td>KES {{ number_format($activity->net_salary) }}</td>
                                                <td>
                                                    @switch($activity->payroll_record_status)
                                                        @case(\PayrollStatus::DRAFT)
                                                            <span class="label label-default">Draft</span>
                                                        @break

                                                        @case(\PayrollStatus::CALCULATED)
                                                            <span class="label label-info">Calculated</span>
                                                        @break

                                                        @case(\PayrollStatus::APPROVED)
                                                            <span class="label label-warning">Approved</span>
                                                        @break

                                                        @case(\PayrollStatus::PAID)
                                                            <span class="label label-success">Paid</span>
                                                        @break

                                                        @default
                                                            <span
                                                                class="label label-default">{{ ucfirst($activity->payroll_record_status) }}</span>
                                                    @endswitch
                                                </td>
                                                <td>{{ $activity->updated_at->format('M d, Y H:i') }}</td>
                                                <td>
                                                    <a href="{{ route('payroll.show', $activity->id) }}"
                                                        class="btn btn-xs btn-primary">
                                                        <i class="fa fa-eye"></i> View
                                                    </a>
                                                    @if ($activity->status === 'approved')
                                                        <a href="{{ route('payroll.payslip', $activity->id) }}"
                                                            class="btn btn-xs btn-success" target="_blank">
                                                            <i class="fa fa-print"></i> Payslip
                                                        </a>
                                                    @endif
                                                </td>
                                            </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="7" class="text-center">No recent activities found</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <style>
            .stat-item {
                text-align: center;
                padding: 10px 0;
            }

            .stat-item h4 {
                margin: 0;
                font-weight: bold;
                color: #333;
            }

            .small-box {
                border-radius: 2px;
                position: relative;
                display: block;
                margin-bottom: 20px;
                box-shadow: 0 1px 1px rgba(0, 0, 0, 0.1);
            }

            .small-box>.inner {
                padding: 10px;
            }

            .small-box>.small-box-footer {
                position: relative;
                text-align: center;
                padding: 3px 0;
                color: #fff;
                color: rgba(255, 255, 255, 0.8);
                display: block;
                z-index: 10;
                background: rgba(0, 0, 0, 0.1);
                text-decoration: none;
            }

            .small-box h3 {
                font-size: 28px;
                font-weight: bold;
                margin: 0 0 10px 0;
                white-space: nowrap;
                padding: 0;
                color: #fff;
            }

            .small-box p {
                font-size: 15px;
                color: #fff;
                margin: 0;
            }

            .small-box small {
                font-size: 12px;
                color: rgba(255, 255, 255, 0.8);
            }

            .small-box .icon {
                position: absolute;
                top: -10px;
                right: 10px;
                z-index: 0;
                font-size: 70px;
                color: rgba(0, 0, 0, 0.15);
            }

            .bg-blue {
                background-color: #3c8dbc !important;
            }

            .bg-green {
                background-color: #00a65a !important;
            }

            .bg-orange {
                background-color: #ff851b !important;
            }

            .bg-red {
                background-color: #dd4b39 !important;
            }
        </style>
    @endsection

    @section('page_scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            $(document).ready(function() {
                // Payroll Trends Chart
                const trendsCtx = document.getElementById('payrollTrendsChart').getContext('2d');
                const payrollTrendsChart = new Chart(trendsCtx, {
                    type: 'line',
                    data: {
                        labels: @json($monthlyTrends['months']),
                        datasets: [{
                                label: 'Gross Salary',
                                data: @json($monthlyTrends['gross_salaries']),
                                borderColor: 'rgb(75, 192, 192)',
                                backgroundColor: 'rgba(75, 192, 192, 0.1)',
                                tension: 0.4,
                                fill: false
                            },
                            {
                                label: 'Net Salary',
                                data: @json($monthlyTrends['net_salaries']),
                                borderColor: 'rgb(54, 162, 235)',
                                backgroundColor: 'rgba(54, 162, 235, 0.1)',
                                tension: 0.4,
                                fill: false
                            },
                            {
                                label: 'Deductions',
                                data: @json($monthlyTrends['deductions']),
                                borderColor: 'rgb(255, 99, 132)',
                                backgroundColor: 'rgba(255, 99, 132, 0.1)',
                                tension: 0.4,
                                fill: false
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            title: {
                                display: true,
                                text: 'Monthly Payroll Trends'
                            },
                            legend: {
                                display: true,
                                position: 'top'
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    callback: function(value, index, values) {
                                        return 'KES ' + value.toLocaleString();
                                    }
                                }
                            }
                        }
                    }
                });
            });
        </script>
    @endsection
