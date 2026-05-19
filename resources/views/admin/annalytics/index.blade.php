@extends('admin.master')

@section('title')
    Hr Annalytics
@endsection
@section('content')
    <div class="container-fluid">
        <div class="row bg-title">
            <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
                <ol class="breadcrumb">
                    <li class="active breadcrumbColor"><a href="{{ url('dashboard') }}"><i class="fa fa-home"></i>
                            @lang('dashboard.dashboard')</a></li>
                    <li>@yield('title')</li>
                </ol>
            </div>

        </div>

        <div class="row">
            <div class="col-sm-12">
                <div class="panel panel-info">
                    <div class="panel-heading"><i class="mdi mdi-table fa-fw"></i> @yield('title')</div>
                    <div class="panel-wrappe collapse in" aria-expanded="true">

                        <div class="container mt-5 table-responsive">
                            <div class="row  g-4 d-flex">
                                <!-- Chart Card 1 -->
                                <div class="col-md-5">
                                    <div class="card shadow">
                                        <div class="card-body text-center">
                                            <h5 class="card-title panel-heading">Gender Distribution</h5>
                                            <canvas id="genderPieChart"></canvas>
                                        </div>
                                    </div>
                                </div>

                                <!-- Chart Card 2 -->
                                <div class="col-md-5">
                                    <div class="card shadow">
                                        <div class="card-body text-center">
                                            <h5 class="card-title panel-heading">Age(When Joining) Vs Years of Service</h5>
                                            <canvas id="ageVsServiceChart"></canvas>
                                        </div>
                                    </div>
                                </div>

                                <!-- Chart Card Ethnicity -->
                                <div class="col-md-6">
                                    <div class="card shadow">
                                        <div class="card-body text-center">
                                            <h5 class="card-title panel-heading">Ethnicity Distribution</h5>
                                            <canvas id="ethnicityBarChart"></canvas>
                                        </div>
                                    </div>
                                </div>

                                <!-- Chart Card Nationality -->
                                <div class="col-md-6">
                                    <div class="card shadow">
                                        <div class="card-body text-center">
                                            <h5 class="card-title panel-heading">Nationality Distribution</h5>
                                            <canvas id="nationalityBarChart"></canvas>
                                        </div>
                                    </div>
                                </div>

                                <!-- Chart Card 3 -->
                                <div class="col-md-6">
                                    <div class="card shadow">
                                        <div class="card-body text-center">
                                            <h5 class="card-title panel-heading">Department Distribution</h5>
                                            <canvas id="departmentBarChart"></canvas>
                                        </div>
                                    </div>
                                </div>

                                <!-- Chart Card 4 -->
                                <div class="col-md-6">
                                    <div class="card shadow">
                                        <div class="card-body text-center">
                                            <h5 class="card-title panel-heading">Location Distribution</h5>
                                            <canvas id="branchBarChart"></canvas>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row g-4 d-flex mt-4">
                                <!-- Leave Balance Chart -->
                                <div class="col-md-6">
                                    <div class="card shadow">
                                        <div class="card-body text-center">
                                            <h5 class="card-title panel-heading">Leave Balance by Type</h5>
                                            <canvas id="leaveBalanceChart"></canvas>
                                        </div>
                                    </div>
                                </div>

                                <!-- Leave Taken Chart -->
                                <div class="col-md-6">
                                    <div class="card shadow">
                                        <div class="card-body text-center">
                                            <h5 class="card-title panel-heading">Leave Taken This Year</h5>
                                            <canvas id="leaveTakenChart"></canvas>
                                        </div>
                                    </div>
                                </div>




                            </div>
                            <div class="row g-4 d-flex mt-4">
                                <!-- Hires Chart -->
                                <div class="col-md-6">
                                    <div class="card shadow">
                                        <div class="card-body text-center">
                                            <h5 class="card-title panel-heading">Employee Hires This Year</h5>
                                            <canvas id="hiresChart"></canvas>
                                        </div>
                                    </div>
                                </div>

                                <!-- Exits Chart -->
                                <div class="col-md-6">
                                    <div class="card shadow">
                                        <div class="card-body text-center">
                                            <h5 class="card-title panel-heading">Employee Exits This Year</h5>
                                            <canvas id="exitsChart"></canvas>
                                        </div>
                                    </div>
                                </div>

                                <!-- Turnover Rate Chart -->
                                <div class="col-md-6">
                                    <div class="card shadow">
                                        <div class="card-body text-center">
                                            <h5 class="card-title panel-heading">Turnover Rate by Department</h5>
                                            <canvas id="turnoverRateChart"></canvas>
                                        </div>
                                    </div>
                                </div>

                                <!-- Retention Trends Chart -->
                                <div class="col-md-6">
                                    <div class="card shadow">
                                        <div class="card-body text-center">
                                            <h5 class="card-title panel-heading">Retention Trends</h5>
                                            <canvas id="retentionTrendsChart"></canvas>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('page_scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            var ctx = document.getElementById('genderPieChart').getContext('2d');

            // Define a function to generate random colors
            function generateColors(count) {
                const colors = [];
                for (let i = 0; i < count; i++) {
                    const r = Math.floor(Math.random() * 255);
                    const g = Math.floor(Math.random() * 255);
                    const b = Math.floor(Math.random() * 255);
                    colors.push(`rgba(${r}, ${g}, ${b}, 0.6)`); // Background color
                }
                return colors;
            }

            // Generate colors dynamically based on data length
            const backgroundColors = generateColors(@json($genderData['labels']).length);
            const borderColors = backgroundColors.map(color => color.replace('0.6',
                '1')); // Adjust alpha for border

            var genderChart = new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: @json($genderData['labels']), // Dynamic labels
                    datasets: [{
                        data: @json($genderData['values']), // Dynamic values
                        backgroundColor: backgroundColors,
                        borderColor: borderColors,
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'top'
                        },
                        tooltip: {
                            callbacks: {
                                label: function(tooltipItem) {
                                    let value = tooltipItem.raw;
                                    let total = tooltipItem.chart.data.datasets[0].data.reduce((a, b) =>
                                        a + b, 0);
                                    let percentage = ((value / total) * 100).toFixed(2);
                                    return `${tooltipItem.label}: ${value} (${percentage}%)`;
                                }
                            }
                        }
                    }
                }
            });
        });
    </script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const ctx = document.getElementById('ageVsServiceChart').getContext('2d');

            // Data from the controller
            const ageRanges = @json($ageVsServiceData['ageRanges']);
            const serviceYears = @json($ageVsServiceData['serviceYears']);

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: ageRanges, // X-axis (Age Ranges)
                    datasets: [{
                        label: 'Average Years of Service',
                        data: serviceYears, // Y-axis (Average Years of Service)
                        fill: false,
                        borderColor: 'rgba(75, 192, 192, 1)',
                        backgroundColor: 'rgba(75, 192, 192, 0.2)',
                        tension: 0.1
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'top'
                        },
                        tooltip: {
                            callbacks: {
                                label: function(tooltipItem) {
                                    return `Avg. Years of Service: ${tooltipItem.raw}`;
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            title: {
                                display: true,
                                text: 'Age Range'
                            }
                        },
                        y: {
                            title: {
                                display: true,
                                text: 'Average Years of Service'
                            },
                            beginAtZero: true
                        }
                    }
                }
            });
        });
    </script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const ctx = document.getElementById('departmentBarChart').getContext('2d');

            // Data from the controller
            const departmentLabels = @json($departmentData->keys()->toArray());
            const departmentValues = @json($departmentData->values()->toArray());

            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: departmentLabels,
                    datasets: [{
                        label: 'Number of Employees',
                        data: departmentValues,
                        backgroundColor: 'rgba(54, 162, 235, 0.6)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'top'
                        },
                        tooltip: {
                            callbacks: {
                                label: function(tooltipItem) {
                                    return `Employees: ${tooltipItem.raw}`;
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            title: {
                                display: true,
                                text: 'Departments'
                            }
                        },
                        y: {
                            title: {
                                display: true,
                                text: 'Number of Employees'
                            },
                            beginAtZero: true
                        }
                    }
                }
            });
        });
    </script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const ctx = document.getElementById('branchBarChart').getContext('2d');

            // Data from the controller
            const branchLabels = @json($branchData->keys()->toArray());
            const branchValues = @json($branchData->values()->toArray());

            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: branchLabels,
                    datasets: [{
                        label: 'Number of Employees',
                        data: branchValues,
                        backgroundColor: 'rgba(255, 99, 132, 0.6)',
                        borderColor: 'rgba(255, 99, 132, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'top'
                        },
                        tooltip: {
                            callbacks: {
                                label: function(tooltipItem) {
                                    return `Employees: ${tooltipItem.raw}`;
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            title: {
                                display: true,
                                text: 'Locations'
                            }
                        },
                        y: {
                            title: {
                                display: true,
                                text: 'Number of Employees'
                            },
                            beginAtZero: true
                        }
                    }
                }
            });
        });
    </script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const ctx = document.getElementById('leaveBalanceChart').getContext('2d');

            // Data from the controller
            const leaveBalanceLabels = @json($leaveBalanceData['labels']);
            const leaveBalanceValues = @json($leaveBalanceData['values']);

            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: leaveBalanceLabels,
                    datasets: [{
                        label: 'Total Leave Balance',
                        data: leaveBalanceValues,
                        backgroundColor: 'rgba(153, 102, 255, 0.6)',
                        borderColor: 'rgba(153, 102, 255, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'top'
                        },
                        tooltip: {
                            callbacks: {
                                label: function(tooltipItem) {
                                    return `Balance: ${tooltipItem.raw} days`;
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            title: {
                                display: true,
                                text: 'Leave Types'
                            }
                        },
                        y: {
                            title: {
                                display: true,
                                text: 'Days'
                            },
                            beginAtZero: true
                        }
                    }
                }
            });
        });
    </script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const ctx = document.getElementById('leaveTakenChart').getContext('2d');

            // Data from the controller
            const leaveTakenLabels = @json($leaveTakenData['labels']);
            const leaveTakenValues = @json($leaveTakenData['taken']);
            const balanceValues = @json($leaveTakenData['balance']);

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: leaveTakenLabels,
                    datasets: [{
                        label: 'Leave Taken',
                        data: leaveTakenValues,
                        fill: false,
                        borderColor: 'rgba(255, 159, 64, 1)',
                        backgroundColor: 'rgba(255, 159, 64, 0.2)',
                        tension: 0.1
                    }, {
                        label: 'Balance Trend',
                        data: balanceValues,
                        fill: false,
                        borderColor: 'rgba(54, 162, 235, 1)',
                        backgroundColor: 'rgba(54, 162, 235, 0.2)',
                        tension: 0.1
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'top'
                        },
                        tooltip: {
                            callbacks: {
                                label: function(tooltipItem) {
                                    return `${tooltipItem.dataset.label}: ${tooltipItem.raw} days`;
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            title: {
                                display: true,
                                text: 'Month'
                            }
                        },
                        y: {
                            title: {
                                display: true,
                                text: 'Days'
                            },
                            beginAtZero: true
                        }
                    }
                }
            });
        });
    </script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const ctx = document.getElementById('hiresChart').getContext('2d');

            // Data from the controller
            const hiresLabels = @json($hiresData['labels']);
            const hiresValues = @json($hiresData['values']);

            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: hiresLabels,
                    datasets: [{
                        label: 'Hires',
                        data: hiresValues,
                        backgroundColor: 'rgba(40, 167, 69, 0.6)',
                        borderColor: 'rgba(40, 167, 69, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'top'
                        },
                        tooltip: {
                            callbacks: {
                                label: function(tooltipItem) {
                                    return `Hires: ${tooltipItem.raw}`;
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            title: {
                                display: true,
                                text: 'Month'
                            }
                        },
                        y: {
                            title: {
                                display: true,
                                text: 'Number of Hires'
                            },
                            beginAtZero: true
                        }
                    }
                }
            });
        });
    </script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const ctx = document.getElementById('exitsChart').getContext('2d');

            // Data from the controller
            const exitsLabels = @json($exitsData['labels']);
            const exitsValues = @json($exitsData['values']);

            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: exitsLabels,
                    datasets: [{
                        label: 'Exits',
                        data: exitsValues,
                        backgroundColor: 'rgba(220, 53, 69, 0.6)',
                        borderColor: 'rgba(220, 53, 69, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'top'
                        },
                        tooltip: {
                            callbacks: {
                                label: function(tooltipItem) {
                                    return `Exits: ${tooltipItem.raw}`;
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            title: {
                                display: true,
                                text: 'Month'
                            }
                        },
                        y: {
                            title: {
                                display: true,
                                text: 'Number of Exits'
                            },
                            beginAtZero: true
                        }
                    }
                }
            });
        });
    </script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const ctx = document.getElementById('turnoverRateChart').getContext('2d');

            // Data from the controller
            const turnoverLabels = @json($turnoverRateData['labels']);
            const turnoverValues = @json($turnoverRateData['values']);

            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: turnoverLabels,
                    datasets: [{
                        label: 'Turnover Rate (%)',
                        data: turnoverValues,
                        backgroundColor: 'rgba(255, 193, 7, 0.6)',
                        borderColor: 'rgba(255, 193, 7, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'top'
                        },
                        tooltip: {
                            callbacks: {
                                label: function(tooltipItem) {
                                    return `Turnover Rate: ${tooltipItem.raw}%`;
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            title: {
                                display: true,
                                text: 'Department'
                            }
                        },
                        y: {
                            title: {
                                display: true,
                                text: 'Turnover Rate (%)'
                            },
                            beginAtZero: true
                        }
                    }
                }
            });
        });
    </script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const ctx = document.getElementById('retentionTrendsChart').getContext('2d');

            // Data from the controller
            const retentionLabels = @json($retentionTrendsData['labels']);
            const retentionValues = @json($retentionTrendsData['values']);

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: retentionLabels,
                    datasets: [{
                        label: 'Retained Employees',
                        data: retentionValues,
                        fill: false,
                        borderColor: 'rgba(23, 162, 184, 1)',
                        backgroundColor: 'rgba(23, 162, 184, 0.2)',
                        tension: 0.1
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'top'
                        },
                        tooltip: {
                            callbacks: {
                                label: function(tooltipItem) {
                                    return `Retained: ${tooltipItem.raw}`;
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            title: {
                                display: true,
                                text: 'Year'
                            }
                        },
                        y: {
                            title: {
                                display: true,
                                text: 'Number of Retained Employees'
                            },
                            beginAtZero: true
                        }
                    }
                }
            });
        });
    </script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const ctx = document.getElementById('ethnicityBarChart').getContext('2d');

            // Data from the controller
            const ethnicityLabels = @json($ethnicityData['labels']);
            const ethnicityValues = @json($ethnicityData['values']);

            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: ethnicityLabels,
                    datasets: [{
                        label: 'Number of Employees',
                        data: ethnicityValues,
                        backgroundColor: 'rgba(75, 0, 130, 0.6)',
                        borderColor: 'rgba(75, 0, 130, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'top'
                        },
                        tooltip: {
                            callbacks: {
                                label: function(tooltipItem) {
                                    return `Employees: ${tooltipItem.raw}`;
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            title: {
                                display: true,
                                text: 'Ethnicity'
                            }
                        },
                        y: {
                            title: {
                                display: true,
                                text: 'Number of Employees'
                            },
                            beginAtZero: true
                        }
                    }
                }
            });
        });
    </script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const ctx = document.getElementById('nationalityBarChart').getContext('2d');

            // Data from the controller
            const nationalityLabels = @json($nationalityData['labels']);
            const nationalityValues = @json($nationalityData['values']);

            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: nationalityLabels,
                    datasets: [{
                        label: 'Number of Employees',
                        data: nationalityValues,
                        backgroundColor: 'rgba(255, 20, 147, 0.6)',
                        borderColor: 'rgba(255, 20, 147, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'top'
                        },
                        tooltip: {
                            callbacks: {
                                label: function(tooltipItem) {
                                    return `Employees: ${tooltipItem.raw}`;
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            title: {
                                display: true,
                                text: 'Nationality'
                            }
                        },
                        y: {
                            title: {
                                display: true,
                                text: 'Number of Employees'
                            },
                            beginAtZero: true
                        }
                    }
                }
            });
        });
    </script>
@endsection
