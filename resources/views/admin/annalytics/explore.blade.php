@extends('admin.master')

@section('title')
    Explore: {{ $chartDefinition['title'] }}
@endsection

@section('content')
<style>
    .granularity-tabs > li > a {
        border-radius: 8px 8px 0 0;
        font-weight: 600;
        color: #4b5563;
    }
    .granularity-tabs > li.active > a,
    .granularity-tabs > li.active > a:hover,
    .granularity-tabs > li.active > a:focus {
        background: #2563eb;
        color: #fff;
        border-color: #2563eb;
    }
    .compare-filter {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 20px;
    }
    .explore-chart-card {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        padding: 24px;
    }
</style>

<div class="container-fluid">
    <div class="row bg-title">
        <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
            <ol class="breadcrumb">
                <li class="active breadcrumbColor">
                    <a href="{{ url('dashboard') }}"><i class="fa fa-home"></i> @lang('dashboard.dashboard')</a>
                </li>
                <li><a href="{{ route('reports.annalytics.view') }}">Reports</a></li>
                <li><a href="{{ route('reports.annalytics.show', $report) }}">{{ $definition['title'] }}</a></li>
                <li>Explore</li>
            </ol>
        </div>
        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12 text-right" style="padding-top: 10px;">
            <a href="{{ route('reports.annalytics.show', array_merge(['report' => $report], array_filter(['year' => $filters['year'] ?? null, 'leave_type_id' => $filters['leave_type_id'] ?? null]))) }}" class="btn btn-default btn-sm">
                <i class="fa fa-arrow-left"></i> Back to Report
            </a>
        </div>
    </div>

    <div class="compare-filter">
        <h4 style="margin-top: 0; font-weight: 700;">Period Comparison</h4>
        <p style="color: #6b7280; margin-bottom: 16px;">Compare the same metric across different years or periods.</p>
        <form method="GET" class="form-inline">
            <div class="form-group" style="margin-right: 16px;">
                <label for="year" style="margin-right: 8px;">Primary Year</label>
                <select name="year" id="year" class="form-control" style="width: 120px;">
                    @for($y = date('Y'); $y >= date('Y') - 5; $y--)
                        <option value="{{ $y }}" {{ $filters['year'] == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </div>
            <div class="form-group" style="margin-right: 16px;">
                <label for="compare_year" style="margin-right: 8px;">Compare With</label>
                <select name="compare_year" id="compare_year" class="form-control" style="width: 140px;">
                    <option value="">None</option>
                    @for($y = date('Y'); $y >= date('Y') - 5; $y--)
                        <option value="{{ $y }}" {{ ($filters['compare_year'] ?? null) == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </div>
            @if($report === 'leave' && !empty($leaveTypes))
                <div class="form-group" style="margin-right: 16px;">
                    <label for="leave_type_id" style="margin-right: 8px;">Leave Type</label>
                    <select name="leave_type_id" id="leave_type_id" class="form-control" style="width: 220px;">
                        @foreach($leaveTypes as $leaveType)
                            <option value="{{ $leaveType->leave_type_id }}" {{ ($filters['leave_type_id'] ?? 1) == $leaveType->leave_type_id ? 'selected' : '' }}>
                                {{ $leaveType->leave_type_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            @endif
            <button type="submit" class="btn btn-info">Compare</button>
        </form>
        @if($report === 'leave' && !empty($selectedLeaveType))
            <p style="margin: 12px 0 0; color: #6b7280;">
                Leave type: <strong>{{ $selectedLeaveType->leave_type_name }}</strong>
            </p>
        @endif
    </div>

    <div class="explore-chart-card">
        <h3 style="margin-top: 0; font-weight: 700;">{{ $chartDefinition['title'] }}</h3>
        <p style="color: #6b7280;">View this chart by quarterly, bi-annually, or annual breakdown.</p>

        <ul class="nav nav-tabs granularity-tabs" role="tablist">
            <li class="active">
                <a data-toggle="tab" href="#quarterly-tab">Quarterly</a>
            </li>
            <li>
                <a data-toggle="tab" href="#biannually-tab">Bi-annually</a>
            </li>
            <li>
                <a data-toggle="tab" href="#annually-tab">Annually</a>
            </li>
        </ul>

        <div class="tab-content" style="padding-top: 20px;">
            @foreach(['quarterly' => 'Quarterly', 'biannually' => 'Bi-annually', 'annually' => 'Annually'] as $key => $label)
                <div class="tab-pane fade {{ $loop->first ? 'in active' : '' }}" id="{{ $key }}-tab">
                    <canvas id="explore-chart-{{ $key }}" height="120"></canvas>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection

@section('page_scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const granularities = @json($granularities);
    const chartInstances = {};

    Object.keys(granularities).forEach(function (key) {
        const canvas = document.getElementById('explore-chart-' + key);
        if (!canvas) return;

        const config = granularities[key];
        chartInstances[key] = new Chart(canvas.getContext('2d'), {
            type: config.type,
            data: {
                labels: config.labels,
                datasets: config.datasets
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { position: 'top' },
                    title: {
                        display: true,
                        text: key.charAt(0).toUpperCase() + key.slice(1) + ' view'
                    }
                },
                scales: config.type === 'pie'
                    ? {}
                    : {
                        y: { beginAtZero: true }
                    }
            }
        });
    });

    $('a[data-toggle="tab"]').on('shown.bs.tab', function () {
        Object.values(chartInstances).forEach(function (chart) {
            chart.resize();
        });
    });
});
</script>
@endsection
