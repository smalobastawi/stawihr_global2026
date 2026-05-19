@extends('admin.master')

@section('title', 'On Leave Today')

@section('content')

<div class="container-fluid">
    <div class="row bg-title">
        <div class="col-lg-7 col-md-7 col-sm-7 col-xs-12">
            <ol class="breadcrumb">
                <li class="active breadcrumbColor"><a href="#"><i class="fa fa-home"></i>
                        @lang('dashboard.dashboard')</a></li>
                <li>@yield('title')</li>
            </ol>

        </div>
       
    </div>

    <div class="row">
        <div class="col-sm-12">
            <div class="panel panel-info">
                <div class="panel-heading"><i class="mdi mdi-table fa-fw"></i> @yield('title')</div>
                <div class="panel-wrapper collapse in" aria-expanded="true">
                    <div class="panel-body">
                        @if(session()->has('success'))
                        <div class="alert alert-success alert-dismissable">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                            <i class="cr-icon glyphicon glyphicon-ok"></i>&nbsp;<strong>{{ session()->get('success')
                                }}</strong>
                        </div>
                        @endif
                        @if(session()->has('error'))
                        <div class="alert alert-danger alert-dismissable">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                            <i class="glyphicon glyphicon-remove"></i>&nbsp;<strong>{{ session()->get('error')
                                }}</strong>
                        </div>
                        @endif

                        <div id="searchBox">
                            <form id="" action="{{ route('leave.report.onLeaveToday') }}" method="GET">
                                <input type="hidden" name="filtering" value="filtering">
                                <div class="form-group">
                                    <div class="col-sm-3">
                                        <label class="control-label  " for="from_date">From Date<span
                                                class="validateRq">*</span>:</label>
                                        <input type="text" class="form-control dateField" required
                                            value="{{$start_date}}" placeholder="@lang('common.date')" name="from_date"
                                            id='from_date'>
                                    </div>
                                    <div class="col-sm-3">
                                        <label class="control-label" for="to_date">To Date<span
                                                class="validateRq">*</span>:</label>
                                        <input type="text" class="form-control dateField" required value="{{$end_date}}"
                                            placeholder="@lang('common.date')" name="to_date" id="to_date">
                                    </div>
                                    <div class="col-sm-2">
                                        <div class="form-group">
                                            <label for="location_id">@lang('employee.location')</label>
                                            <select name="location_id" class="form-control location_id  select2"
                                                id="location_id">
                                                <option value="">All Locations
                                                </option>
                                                @foreach ($locations as $location)
                                                <option value="{{ $location->location_id }}">
                                                    {{ $location->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-sm-2">
                                        <div class="form-group">
                                            <label for="department_id">@lang('employee.department')</label>
                                            <select name="department_id" class="form-control department_id  select2"
                                                id="department_id">
                                                <option value="">--- @lang('employee.select_department')---
                                                </option>
                                                @foreach ($departments as $department)
                                                <option value="{{ $department->department_id }}">
                                                    {{ $department->department_name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-sm-2">
                                        <label for="filter">Filter data</label>
                                        <input type="submit" id="filter" style="margin-top: 2px; width: 100px;"
                                            class="btn btn-info form-control" value="@lang('common.filter')">
                                            <a href="{{ route('leave.report.onLeaveToday') }}" class="btn btn-primary form-control"
                                            style="margin-top: 2px; width: 100px;">@lang('common.reset')</a>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="panel panel-default">
                                    <div class="panel-heading">Leave Status ({{ $start_date }} to {{ $end_date }}) - Pie Chart</div>
                                    <div class="panel-body">
                                        <canvas id="leavePieChart" height="250"></canvas>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="panel panel-default">
                                    <div class="panel-heading">Leave Status ({{ $start_date }} to {{ $end_date }}) - Bar Chart</div>
                                    <div class="panel-body">
                                        <canvas id="leaveBarChart" height="250"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="table-responsive1" id='table_reponse' style="width: 100%; overflow-x: scroll;">
                            @include('admin.leave.leaveApplication.all_applications_table',['results'=>$results])
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection


@section('page_scripts')
<script type="text/javascript">
    $(document).ready(function() {
        $('#filter_form select').select2();
        $('#filter_form').on('submit', function(e) {
            e.preventDefault();
            var formData = $(this).serialize();
            
            $.ajax({
                url: "",
                type: "GET",
                data: formData,
                success: function(response) {
                    $('#table_reponse').html(response.html);
                },
                error: function(xhr) {
                    console.error(xhr.responseText);
                }
            });
        });
    });
</script>

<script>
    $(document).ready(function() {
        // Calculate percentages
        const totalEmployees = {{ $onLeave + $present }};
        const onLeavePercent = Math.round(({{ $onLeave }} / totalEmployees) * 100) || 0;
        const presentPercent = Math.round(({{ $present }} / totalEmployees) * 100) || 0;

        // Pie Chart
        const pieCtx = document.getElementById('leavePieChart').getContext('2d');
        const leavePieChart = new Chart(pieCtx, {
            type: 'pie',
            data: {
                labels: [
                    `On Leave (${onLeavePercent}%)`, 
                    `Not On Leave (${presentPercent}%)`
                ],
                datasets: [{
                    data: [{{ $onLeave }}, {{ $present }}],
                    backgroundColor: ['#FF6384', '#36A2EB'],
                    hoverBackgroundColor: ['#FF6384', '#36A2EB']
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                tooltips: {
                    callbacks: {
                        label: function(tooltipItem, data) {
                            const dataset = data.datasets[tooltipItem.datasetIndex];
                            const total = dataset.data.reduce((sum, value) => sum + value, 0);
                            const currentValue = dataset.data[tooltipItem.index];
                            const percentage = Math.round((currentValue / total) * 100);
                            return `${data.labels[tooltipItem.index]}: ${percentage}%`;
                        }
                    }
                }
            }
        });

        // Bar Chart
        const barCtx = document.getElementById('leaveBarChart').getContext('2d');
        const leaveBarChart = new Chart(barCtx, {
            type: 'bar',
            data: {
                labels: ['On Leave', 'Not On Leave'],
                datasets: [{
                    label: 'Number of Employees',
                    data: [{{ $onLeave }}, {{ $present }}],
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.7)',
                        'rgba(54, 162, 235, 0.7)'
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
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        }
                    }
                },
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const total = {{ $onLeave + $present }};
                                const currentValue = context.raw;
                                const percentage = Math.round((currentValue / total) * 100);
                                return `${context.dataset.label}: ${currentValue} (${percentage}%)`;
                            }
                        }
                    }
                }
            }
        });
    });
</script>
@endsection