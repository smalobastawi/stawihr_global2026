@extends('admin.master')

@section('title')
    StawiHR - Payroll Reports Dashboard
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row bg-title">
            <div class="col-lg-8 col-md-8 col-sm-8">
                <ol class="breadcrumb">
                    <li class="active breadcrumbColor"><a href="{{ url('dashboard') }}"><i class="fa fa-home"></i>
                            @lang('dashboard.dashboard')</a></li>
                    <li>Payroll Reports Dashboard</li>
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
            <!-- Statutory Breakdown Pie Chart -->
            <div class="col-md-6">
                <div class="panel panel-info">
                    <div class="panel-heading">
                        <i class="fa fa-pie-chart fa-fw"></i> Statutory Deductions Breakdown
                    </div>
                    <div class="panel-wrapper collapse in" aria-expanded="true">
                        <div class="panel-body">
                            <canvas id="statutoryPieChart" height="300"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Monthly Trends Bar Chart -->
            <div class="col-md-6">
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        <i class="fa fa-bar-chart fa-fw"></i> Monthly Statutory Trends (Last 6 Months)
                    </div>
                    <div class="panel-wrapper collapse in" aria-expanded="true">
                        <div class="panel-body">
                            <canvas id="monthlyTrendsChart" height="300"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Department Breakdown Bubble Chart & Report Links -->
        <div class="row">
            <!-- Department Breakdown Bubble Chart -->
            <!-- Department Breakdown Bubble Chart -->
            <div class="col-md-8">
                <div class="panel panel-success">
                    <div class="panel-heading">
                        <i class="fa fa-globe fa-fw"></i> Department Payroll Distribution (Bubble Chart)
                    </div>
                    <div class="panel-wrapper collapse in" aria-expanded="true">
                        <div class="panel-body">
                            <div class="chart-container" style="height: 400px; position: relative;">
                                <canvas id="departmentBubbleChart"></canvas>
                            </div>
                            <div class="text-center">
                                <small class="text-muted">
                                    X-axis: Gross Salary (K), Y-axis: Net Salary (K), Bubble Size: Employee Count
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Report Download Links -->
            <div class="col-md-4">
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        <i class="fa fa-download fa-fw"></i> Download Reports
                    </div>
                    <div class="panel-wrapper collapse in" aria-expanded="true">
                        <div class="panel-body">
                            <div class="list-group">


                                <a href="{{ route('shifReportsIndex') }}" class="list-group-item">
                                    <i class="fa fa-file-excel-o text-primary"></i>
                                    <strong>SHIF Report</strong>
                                    <span class="pull-right">
                                        <i class="fa fa-eye"></i> <i class="fa fa-download"></i>
                                    </span>
                                    <div class="clearfix"></div>
                                    <small class="text-muted">Monthly SHIF contributions report</small>
                                </a>

                                <a href="{{ route('nssfReportsIndex') }}" class="list-group-item">
                                    <i class="fa fa-file-excel-o text-warning"></i>
                                    <strong>NSSF Report</strong>
                                    <span class="pull-right">
                                        <i class="fa fa-eye"></i> <i class="fa fa-download"></i>
                                    </span>
                                    <div class="clearfix"></div>
                                    <small class="text-muted">Monthly NSSF contributions report</small>
                                </a>

                                <a href="{{ route('ahlReportIndex') }}" class="list-group-item">
                                    <i class="fa fa-file-excel-o text-primary"></i>
                                    <strong>Housing Levy Report</strong>
                                    <span class="pull-right">
                                        <i class="fa fa-eye"></i> <i class="fa fa-download"></i>
                                    </span>
                                    <div class="clearfix"></div>
                                    <small class="text-muted">Monthly Housing Levy report</small>
                                </a>

                                <a href="{{ route('reports.paye') }}" class="list-group-item">
                                    <i class="fa fa-file-pdf-o text-danger"></i>
                                    <strong>PAYE/P9 Report</strong>
                                    <span class="pull-right">
                                        <i class="fa fa-eye"></i> <i class="fa fa-download"></i>
                                    </span>
                                    <div class="clearfix"></div>
                                    <small class="text-muted">Generate P9 Forms</small>
                                </a>


                                <a href="{{ route('payroll.reports.deductions') }}" class="list-group-item">
                                    <i class="fa fa-file-excel-o text-info"></i>
                                    <strong>Deductions Report</strong>
                                    <span class="pull-right">
                                        <i class="fa fa-eye"></i> <i class="fa fa-download"></i>
                                    </span>
                                    <div class="clearfix"></div>
                                    <small class="text-muted">View and export non-statutory deductions</small>
                                </a>

                              

                                <a href="{{ route('payroll.reports.inputs') }}" class="list-group-item">
                                    <i class="fa fa-file-excel-o text-info"></i>
                                    <strong>Payroll Inputs Report</strong>
                                    <span class="pull-right">
                                        <i class="fa fa-eye"></i> <i class="fa fa-download"></i>
                                    </span>
                                    <div class="clearfix"></div>
                                    <small class="text-muted">View and export all payroll inputs before processing</small>
                                </a>

                                @if (isset($periods) && $periods->count() > 0)
                                    <div class="list-group-item">
                                        <form method="POST"
                                            action="{{ route('reports.rawpaysumm', $currentPeriod->id) }}"
                                            style="margin: 0;">
                                            @csrf

                                            <input type="hidden" name="period_id" id="selectedMonthInput"
                                                value="">
                                            <button type="submit" class="btn btn-link"
                                                style="padding: 0; text-align: left; width: 100%;">
                                                <i class="fa fa-file-excel-o text-primary"></i>
                                                <strong>Payroll Summary Export</strong>
                                                <span class="pull-right">
                                                    <i class="fa fa-download"></i>
                                                </span>
                                                <div class="clearfix"></div>
                                                <small class="text-muted">Complete payroll export (Excel)</small>
                                            </button>
                                        </form>
                                    </div>
                                @endif
                                <a href="{{ route('payroll.settings.periods.bank-upload-report', $currentPeriod->id) }}"
                                    class="list-group-item">
                                    <i class="fa fa-file-pdf-o text-primary"></i>
                                    <strong>KCB Bank Upload File</strong>
                                    <span class="pull-right">
                                        <i class="fa fa-eye"></i> <i class="fa fa-download"></i>
                                    </span>
                                    <div class="clearfix"></div>
                                    <small class="text-muted">Generate KCB Bank Upload File</small>
                                </a>
                                <a href="{{ route('payroll.reports.variance') }}" class="list-group-item">
                                    <i class="fa fa-file-pdf-o text-primary"></i>
                                    <strong>Variance Report</strong>
                                    <span class="pull-right">
                                        <i class="fa fa-eye"></i> <i class="fa fa-download"></i>
                                    </span>
                                    <div class="clearfix"></div>
                                    <small class="text-muted">Go to Variance Report</small>
                                </a>
                            </div>

                            <div style="margin-top: 15px;">
                                <h5><strong>Quick Actions</strong></h5>
                                <div class="btn-group-vertical">
                                    <a style="color: #ffff;" href="{{ route('payroll.process.form') }}"
                                        class="btn btn-success">
                                        <i class="fa fa-cogs"></i> Process Payroll
                                    </a>
                                    <a style="color: #ffff;" href="{{ route('payroll.index') }}"
                                        class="btn btn-primary">
                                        <i class="fa fa-list"></i> Payroll Records
                                    </a>
                                    <a style="color: #ffff;" href="{{ route('payroll.dashboard') }}"
                                        class="btn btn-info">
                                        <i class="fa fa-dashboard"></i> Payroll Dashboard
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Detailed Statutory Breakdown -->
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <i class="fa fa-table fa-fw"></i> Current Period Statutory Breakdown
                    </div>
                    <div class="panel-wrapper collapse in" aria-expanded="true">
                        <div class="panel-body">
                            <div class="row text-center">
                                <div class="col-md-2">
                                    <div class="stat-box">
                                        <h4 class="text-primary" id="payeAmount">KES
                                            {{ number_format($chartData['report_summary']['total_paye'] ?? 0) }}</h4>
                                        <p class="text-muted">PAYE Tax</p>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="stat-box">
                                        <h4 class="text-warning" id="nssfAmount">KES
                                            {{ number_format($chartData['report_summary']['total_nssf'] ?? 0) }}</h4>
                                        <p class="text-muted">NSSF</p>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="stat-box">
                                        <h4 class="text-primary" id="shifAmount">KES
                                            {{ number_format($chartData['report_summary']['total_shif'] ?? 0) }}</h4>
                                        <p class="text-muted">SHIF</p>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="stat-box">
                                        <h4 class="text-success" id="housingAmount">KES
                                            {{ number_format($chartData['report_summary']['total_housing_levy'] ?? 0) }}
                                        </h4>
                                        <p class="text-muted">Housing Levy</p>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="stat-box">
                                        <h4 class="text-danger" id="totalStatutory">KES
                                            {{ number_format(($chartData['report_summary']['total_paye'] ?? 0) + ($chartData['report_summary']['total_nssf'] ?? 0) + ($chartData['report_summary']['total_shif'] ?? 0) + ($chartData['report_summary']['total_housing_levy'] ?? 0)) }}
                                        </h4>
                                        <p class="text-muted">Total Statutory</p>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="stat-box">
                                        @php
                                            $totalGross = $chartData['report_summary']['total_gross'] ?? 1;
                                            $totalStatutory =
                                                ($chartData['report_summary']['total_paye'] ?? 0) +
                                                ($chartData['report_summary']['total_nssf'] ?? 0) +
                                                ($chartData['report_summary']['total_shif'] ?? 0) +
                                                ($chartData['report_summary']['total_housing_levy'] ?? 0);
                                            $percentage = $totalGross > 0 ? ($totalStatutory / $totalGross) * 100 : 0;
                                        @endphp
                                        <h4 class="text-primary" id="deductionPercentage">
                                            {{ number_format($percentage, 1) }}%</h4>
                                        <p class="text-muted">Of Gross Salary</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        /* Enhanced styling for the dashboard */
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

        .stat-box {
            padding: 15px;
            margin: 5px 0;
        }

        .stat-box h4 {
            font-weight: bold;
            margin: 0;
        }

        .list-group-item:hover {
            background-color: #f5f5f5;
        }

        .panel {
            box-shadow: 0 1px 1px rgba(0, 0, 0, 0.05);
        }

        .chart-container {
            position: relative;
            height: 300px;
            margin: 10px 0;
        }

        #monthFilter {
            max-width: 250px;
        }

        .loading-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(255, 255, 255, 0.8);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1000;
        }
    </style>
@endsection

@section('page_scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        let statutoryChart, trendsChart, bubbleChart;

        $(document).ready(function() {
            initializeCharts();

            // Handle month filter change
            $('#monthFilter').change(function() {
                const selectedMonth = $(this).val();
                updateDashboardData(selectedMonth);
            });
        });

        function initializeCharts() {
            const chartData = @json($chartData);

            // Initialize Statutory Pie Chart
            initStatutoryPieChart(chartData.statutory_breakdown);

            // Initialize Monthly Trends Chart
            initMonthlyTrendsChart(chartData.monthly_trends);

            // Initialize Department Bubble Chart
            initDepartmentBubbleChart(chartData.department_breakdown);
        }

        function initStatutoryPieChart(data) {
            const ctx = document.getElementById('statutoryPieChart').getContext('2d');

            statutoryChart = new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: data.labels || [],
                    datasets: [{
                        data: data.data || [],
                        backgroundColor: data.colors || ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0',
                            '#9966FF'
                        ],
                        borderWidth: 2,
                        borderColor: '#fff'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                padding: 20,
                                usePointStyle: true
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const label = context.label || '';
                                    const value = context.parsed || 0;
                                    return label + ': KES ' + value.toLocaleString();
                                }
                            }
                        }
                    }
                }
            });
        }

        function initMonthlyTrendsChart(data) {
            const ctx = document.getElementById('monthlyTrendsChart').getContext('2d');

            trendsChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: data.months || [],
                    datasets: data.datasets || []
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top'
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return context.dataset.label + ': KES ' + context.parsed.y.toLocaleString();
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            stacked: true
                        },
                        y: {
                            stacked: true,
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return 'KES ' + value.toLocaleString();
                                }
                            }
                        }
                    }
                }
            });
        }

        function initDepartmentBubbleChart(data) {
            const ctx = document.getElementById('departmentBubbleChart').getContext('2d');

            bubbleChart = new Chart(ctx, {
                type: 'bubble',
                data: {
                    datasets: data || []
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const dataset = context.dataset;
                                    const dataPoint = dataset.data[context.dataIndex];
                                    return [
                                        dataset.label,
                                        `Employees: ${dataset.employee_count || 0}`,
                                        `Gross: KES ${(dataset.total_gross || 0).toLocaleString()}`,
                                        `Net: KES ${(dataset.total_net || 0).toLocaleString()}`
                                    ];
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Gross Salary (Thousands)'
                            },
                            ticks: {
                                callback: function(value) {
                                    return 'K' + value.toLocaleString();
                                }
                            }
                        },
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Net Salary (Thousands)'
                            },
                            ticks: {
                                callback: function(value) {
                                    return 'K' + value.toLocaleString();
                                }
                            }
                        }
                    }
                }
            });
        }

        function updateDashboardData(month) {
            // Show loading state
            showLoadingState();
            document.getElementById('selectedMonthInput').value = month;

            // Fetch updated data via AJAX
            $.get('{{ route('payrollReportsChartsData') }}', {
                    month: month
                })
                .done(function(data) {
                    updateSummaryCards(data.report_summary);
                    updateCharts(data);
                    hideLoadingState();
                })
                .fail(function() {
                    alert('Error loading data. Please try again.');
                    hideLoadingState();
                });
        }

        function updateSummaryCards(summary) {
            $('#totalEmployees').text(Number(summary.total_employees || 0).toLocaleString());
            $('#totalGross').text('KES ' + Number(summary.total_gross || 0).toLocaleString());
            $('#totalNet').text('KES ' + Number(summary.total_net || 0).toLocaleString());
            $('#totalDeductions').text('KES ' + Number(summary.total_deductions || 0).toLocaleString());

            // Update detailed breakdown
            $('#payeAmount').text('KES ' + Number(summary.total_paye || 0).toLocaleString());
            $('#nssfAmount').text('KES ' + Number(summary.total_nssf || 0).toLocaleString());
            $('#shifAmount').text('KES ' + Number(summary.total_shif || 0).toLocaleString());
            $('#housingAmount').text('KES ' + Number(summary.total_housing_levy || 0).toLocaleString());

            const totalStatutory = (summary.total_paye || 0) + (summary.total_nssf || 0) + (summary.total_shif || 0) + (
                summary.total_housing_levy || 0);
            $('#totalStatutory').text('KES ' + totalStatutory.toLocaleString());

            const percentage = summary.total_gross > 0 ? (totalStatutory / summary.total_gross) * 100 : 0;
            $('#deductionPercentage').text(percentage.toFixed(1) + '%');
        }

        function updateCharts(data) {
            // Update Statutory Pie Chart
            if (statutoryChart) {
                statutoryChart.data.labels = data.statutory_breakdown.labels || [];
                statutoryChart.data.datasets[0].data = data.statutory_breakdown.data || [];
                statutoryChart.update();
            }

            // Update Monthly Trends Chart
            if (trendsChart) {
                trendsChart.data.labels = data.monthly_trends.months || [];
                trendsChart.data.datasets = data.monthly_trends.datasets || [];
                trendsChart.update();
            }

            // Update Department Bubble Chart
            if (bubbleChart) {
                bubbleChart.data.datasets = data.department_breakdown || [];
                bubbleChart.update();
            }
        }

        function showLoadingState() {
            $('.panel-body').append('<div class="loading-overlay"><i class="fa fa-spinner fa-spin fa-2x"></i></div>');
        }

        function hideLoadingState() {
            $('.loading-overlay').remove();
        }

        // Utility function to format numbers
        function formatNumber(num) {
            return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        }
        $(document).ready(function() {
            // Set the selected period ID in the hidden input
            const selectedPeriod = $('#monthFilter').val();
            $('#selectedMonthInput').val(selectedPeriod);

            // Auto-submit the form
            $('form[action*="reports.rawpaysumm"]').submit();
        });
    </script>
@endsection
