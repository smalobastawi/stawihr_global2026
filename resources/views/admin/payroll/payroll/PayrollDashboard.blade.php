@extends('admin.master')

@section('title')
    StawiHR - Payroll Dashboard
@endsection

@section('content')
<div class="container-fluid">
    <div class="row bg-title">
        <div class="col-lg-8 col-md-8 col-sm-8">
            <ol class="breadcrumb">
                <li class="active breadcrumbColor"><a href="{{ url('dashboard') }}"><i class="fa fa-home"></i> @lang('dashboard.dashboard')</a></li>
                <li>@yield('title')</li>
            </ol>
        </div>
    </div>

    <!-- Summary Cards Row -->
    <div class="row">
        <!-- Current Month Payroll -->
        <div class="col-lg-3 col-xs-6">
            <div class="small-box bg-blue">
                <div class="inner">
                    <h3>KES 2.4M</h3>
                    <p>Current Month Payroll</p>
                    <small>August 2025</small>
                </div>
                <div class="icon">
                    <i class="fa fa-money"></i>
                </div>
            </div>
        </div>

        <!-- Previous Month Comparison -->
        <div class="col-lg-3 col-xs-6">
            <div class="small-box bg-green">
                <div class="inner">
                    <h3>+8.5%</h3>
                    <p>vs Previous Month</p>
                    <small>July: KES 2.2M</small>
                </div>
                <div class="icon">
                    <i class="fa fa-arrow-up"></i>
                </div>
            </div>
        </div>

        <!-- Upcoming Month Estimate -->
        <div class="col-lg-3 col-xs-6">
            <div class="small-box bg-orange">
                <div class="inner">
                    <h3>KES 2.6M</h3>
                    <p>Upcoming Month Estimate</p>
                    <small>September 2025</small>
                </div>
                <div class="icon">
                    <i class="fa fa-calendar"></i>
                </div>
            </div>
        </div>

        <!-- Annual Estimate -->
        <div class="col-lg-3 col-xs-6">
            <div class="small-box bg-purple">
                <div class="inner">
                    <h3>KES 28.8M</h3>
                    <p>Annual Estimate</p>
                    <small>2025 Projection</small>
                </div>
                <div class="icon">
                    <i class="fa fa-line-chart"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row">
        <!-- Current Year Payroll Trends Chart -->
        <div class="col-md-8">
            <div class="panel panel-info">
                <div class="panel-heading">
                    <i class="fa fa-line-chart fa-fw"></i> Current Year Payroll Trends (2025)
                </div>
                <div class="panel-wrapper collapse in" aria-expanded="true">
                    <div class="panel-body">
                        <canvas id="payrollTrendsChart" height="100"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Current vs Previous Month Comparison -->
        <div class="col-md-4">
            <div class="panel panel-success">
                <div class="panel-heading">
                    <i class="fa fa-bar-chart fa-fw"></i> Monthly Comparison
                </div>
                <div class="panel-wrapper collapse in" aria-expanded="true">
                    <div class="panel-body">
                        <canvas id="monthlyComparisonChart" height="200"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Additional Statistics Row -->
    <div class="row">
        <!-- Payroll Breakdown -->
        <div class="col-md-6">
            <div class="panel panel-warning">
                <div class="panel-heading">
                    <i class="fa fa-pie-chart fa-fw"></i> Current Month Payroll Breakdown
                </div>
                <div class="panel-wrapper collapse in" aria-expanded="true">
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="stat-item">
                                    <h4>KES 1.8M</h4>
                                    <p class="text-muted">Basic Salaries (75%)</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="stat-item">
                                    <h4>KES 360K</h4>
                                    <p class="text-muted">Allowances (15%)</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="stat-item">
                                    <h4>KES 240K</h4>
                                    <p class="text-muted">Overtime (10%)</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="stat-item">
                                    <h4>KES 480K</h4>
                                    <p class="text-muted">Total Deductions (20%)</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Employee Statistics -->
        <div class="col-md-6">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <i class="fa fa-users fa-fw"></i> Employee Statistics
                </div>
                <div class="panel-wrapper collapse in" aria-expanded="true">
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="stat-item">
                                    <h4>145</h4>
                                    <p class="text-muted">Total Employees</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="stat-item">
                                    <h4>142</h4>
                                    <p class="text-muted">Paid This Month</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="stat-item">
                                    <h4>3</h4>
                                    <p class="text-muted">Pending Payments</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="stat-item">
                                    <h4>KES 16,552</h4>
                                    <p class="text-muted">Average Salary</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <i class="fa fa-clock-o fa-fw"></i> Recent Payroll Activity
                </div>
                <div class="panel-wrapper collapse in" aria-expanded="true">
                    <div class="panel-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Activity</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Aug 01, 2025</td>
                                        <td>Monthly Payroll Generated</td>
                                        <td>KES 2,400,000</td>
                                        <td><span class="label label-success">Completed</span></td>
                                    </tr>
                                    <tr>
                                        <td>Jul 31, 2025</td>
                                        <td>NSSF Report Generated</td>
                                        <td>KES 180,000</td>
                                        <td><span class="label label-success">Completed</span></td>
                                    </tr>
                                    <tr>
                                        <td>Jul 31, 2025</td>
                                        <td>SHIF Report Generated</td>
                                        <td>KES 120,000</td>
                                        <td><span class="label label-success">Completed</span></td>
                                    </tr>
                                    <tr>
                                        <td>Jul 30, 2025</td>
                                        <td>Overtime Calculations</td>
                                        <td>KES 240,000</td>
                                        <td><span class="label label-warning">Pending</span></td>
                                    </tr>
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
    padding: 15px 0;
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
    box-shadow: 0 1px 1px rgba(0,0,0,0.1);
}
.small-box > .inner {
    padding: 10px;
}
.small-box > .small-box-footer {
    position: relative;
    text-align: center;
    padding: 3px 0;
    color: #fff;
    color: rgba(255,255,255,0.8);
    display: block;
    z-index: 10;
    background: rgba(0,0,0,0.1);
    text-decoration: none;
}
.small-box h3 {
    font-size: 38px;
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
    color: rgba(255,255,255,0.8);
}
.small-box .icon {
    -webkit-transition: all .3s linear;
    -o-transition: all .3s linear;
    transition: all .3s linear;
    position: absolute;
    top: -10px;
    right: 10px;
    z-index: 0;
    font-size: 90px;
    color: rgba(0,0,0,0.15);
}
.bg-blue { background-color: #3c8dbc !important; }
.bg-green { background-color: #00a65a !important; }
.bg-orange { background-color: #ff851b !important; }
.bg-purple { background-color: #605ca8 !important; }
</style>
@endsection

@section('page_scripts')

<script>
$(document).ready(function() {
    // Current Year Payroll Trends Chart
    const trendsCtx = document.getElementById('payrollTrendsChart').getContext('2d');
    const payrollTrendsChart = new Chart(trendsCtx, {
        type: 'line',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
            datasets: [{
                label: 'Payroll Amount (KES)',
                data: [2100000, 2050000, 2200000, 2150000, 2300000, 2250000, 2200000, 2400000, 2600000, 2500000, 2700000, 2800000],
                borderColor: 'rgb(75, 192, 192)',
                backgroundColor: 'rgba(75, 192, 192, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            plugins: {
                title: {
                    display: true,
                    text: '2025 Monthly Payroll Trends'
                },
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: false,
                    ticks: {
                        callback: function(value, index, values) {
                            return 'KES ' + (value / 1000000).toFixed(1) + 'M';
                        }
                    }
                }
            }
        }
    });

    // Monthly Comparison Chart
    const comparisonCtx = document.getElementById('monthlyComparisonChart').getContext('2d');
    const monthlyComparisonChart = new Chart(comparisonCtx, {
        type: 'bar',
        data: {
            labels: ['July 2025', 'August 2025'],
            datasets: [{
                label: 'Payroll Amount',
                data: [2200000, 2400000],
                backgroundColor: [
                    'rgba(255, 99, 132, 0.8)',
                    'rgba(54, 162, 235, 0.8)'
                ],
                borderColor: [
                    'rgba(255, 99, 132, 1)',
                    'rgba(54, 162, 235, 1)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                title: {
                    display: true,
                    text: 'Current vs Previous Month'
                },
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: false,
                    ticks: {
                        callback: function(value, index, values) {
                            return 'KES ' + (value / 1000000).toFixed(1) + 'M';
                        }
                    }
                }
            }
        }
    });
});
</script>
@endsection