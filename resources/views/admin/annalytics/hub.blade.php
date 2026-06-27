@extends('admin.master')

@section('title')
    Reports
@endsection

@section('content')
<style>
    .analytics-hub { padding: 10px 0 30px; }
    .analytics-hub h1 { font-size: 28px; font-weight: 700; color: #1f2937; margin: 0 0 24px; }
    .report-card {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        padding: 24px;
        height: 100%;
        display: flex;
        flex-direction: column;
        transition: box-shadow .2s ease, transform .2s ease;
    }
    .report-card:hover { box-shadow: 0 8px 24px rgba(15, 23, 42, 0.08); transform: translateY(-2px); }
    .report-card-icon {
        width: 44px;
        height: 44px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 16px;
    }
    .report-card-icon i { color: #fff; font-size: 22px; }
    .report-card h3 { font-size: 18px; font-weight: 700; color: #111827; margin: 0 0 4px; }
    .report-card .subtitle { color: #6b7280; font-size: 14px; margin-bottom: 12px; }
    .report-card .description { color: #4b5563; font-size: 14px; line-height: 1.5; flex: 1; margin-bottom: 20px; }
    .report-card-actions { display: flex; gap: 10px; margin-top: auto; }
    .report-card-actions .btn {
        flex: 1;
        border-radius: 8px;
        font-weight: 600;
        padding: 8px 12px;
    }
    .report-card-actions .btn-outline-primary {
        color: #2563eb;
        border-color: #dbeafe;
        background: #fff;
    }
    .report-card-actions .btn-outline-primary:hover {
        background: #eff6ff;
        border-color: #93c5fd;
        color: #1d4ed8;
    }
</style>

<div class="container-fluid analytics-hub">
    <div class="row bg-title">
        <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
            <ol class="breadcrumb">
                <li class="active breadcrumbColor">
                    <a href="{{ url('dashboard') }}"><i class="fa fa-home"></i> @lang('dashboard.dashboard')</a>
                </li>
                <li>@yield('title')</li>
            </ol>
        </div>
    </div>

    <h1>Reports</h1>

    @if(empty($reports))
        <div class="white-box">
            <p style="margin: 0; color: #6b7280;">
                You do not have permission to view any analytics reports. Contact your administrator to request access to the relevant report modules.
            </p>
        </div>
    @else
    <div class="row">
        @foreach($reports as $slug => $report)
            <div class="col-lg-4 col-md-6" style="margin-bottom: 24px;">
                <div class="report-card">
                    <div class="report-card-icon" style="background: {{ $report['color'] }};">
                        <i class="mdi {{ $report['icon'] }}"></i>
                    </div>
                    <h3>{{ $report['title'] }}</h3>
                    <div class="subtitle">{{ $report['subtitle'] }}</div>
                    <div class="description">{{ $report['description'] }}</div>
                    <div class="report-card-actions">
                        <a href="{{ route('reports.annalytics.show', $slug) }}" class="btn btn-outline-primary">
                            <i class="fa fa-eye"></i> View
                        </a>
                        <a href="{{ route('reports.annalytics.export', $slug) }}" class="btn btn-outline-primary">
                            <i class="fa fa-download"></i> Export
                        </a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
    @endif
</div>
@endsection
