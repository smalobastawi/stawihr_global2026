@extends('admin.master')

@section('title')
    {{ $definition['title'] }}
@endsection

@section('content')
<div class="container-fluid">
    <div class="row bg-title">
        <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
            <ol class="breadcrumb">
                <li class="active breadcrumbColor">
                    <a href="{{ url('dashboard') }}"><i class="fa fa-home"></i> @lang('dashboard.dashboard')</a>
                </li>
                <li><a href="{{ route('reports.annalytics.view') }}">Reports</a></li>
                <li>@yield('title')</li>
            </ol>
        </div>
        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12 text-right" style="padding-top: 10px;">
            <a href="{{ route('reports.annalytics.view') }}" class="btn btn-default btn-sm">
                <i class="fa fa-arrow-left"></i> Back to Reports
            </a>
            <a href="{{ route('reports.annalytics.export', array_merge(['report' => $report], array_filter(['year' => $filters['year'] ?? null, 'leave_type_id' => $filters['leave_type_id'] ?? null]))) }}" class="btn btn-success btn-sm">
                <i class="fa fa-download"></i> Export
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="white-box" style="margin-bottom: 10px;">
                <form method="GET" class="form-inline">
                    <label for="year" style="margin-right: 8px;">Year</label>
                    <select name="year" id="year" class="form-control" style="width: 120px; margin-right: 10px;">
                        @for($y = date('Y'); $y >= date('Y') - 5; $y--)
                            <option value="{{ $y }}" {{ $filters['year'] == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>

                    @if($report === 'leave' && !empty($leaveTypes))
                        <label for="leave_type_id" style="margin-right: 8px; margin-left: 10px;">Leave Type</label>
                        <select name="leave_type_id" id="leave_type_id" class="form-control" style="width: 220px; margin-right: 10px;">
                            @foreach($leaveTypes as $leaveType)
                                <option value="{{ $leaveType->leave_type_id }}" {{ ($filters['leave_type_id'] ?? 1) == $leaveType->leave_type_id ? 'selected' : '' }}>
                                    {{ $leaveType->leave_type_name }}
                                </option>
                            @endforeach
                        </select>
                    @endif

                    <button type="submit" class="btn btn-info">Apply</button>
                </form>

                @if($report === 'leave' && !empty($selectedLeaveType))
                    <p style="margin: 12px 0 0; color: #6b7280;">
                        Showing data for <strong>{{ $selectedLeaveType->leave_type_name }}</strong>
                        @if($selectedLeaveType->leave_type_id == 1)
                            (default annual leave)
                        @endif
                    </p>
                @endif
            </div>
        </div>
    </div>

    @include('admin.annalytics.partials.summary-bubbles', ['summary' => $summary])

    <div class="row">
        @foreach($charts as $chart)
            @include('admin.annalytics.partials.chart-card', ['chart' => $chart, 'report' => $report, 'filters' => $filters])
        @endforeach
    </div>
</div>
@endsection

@section('page_scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const charts = @json($charts);

    charts.forEach(function (chart) {
        const canvas = document.getElementById('chart-' + chart.slug);
        if (!canvas) return;

        new Chart(canvas.getContext('2d'), {
            type: chart.config.type,
            data: {
                labels: chart.config.labels,
                datasets: chart.config.datasets
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: { position: 'top' }
                },
                scales: chart.config.type === 'pie'
                    ? {}
                    : {
                        y: { beginAtZero: true }
                    }
            }
        });
    });
});
</script>
@endsection
