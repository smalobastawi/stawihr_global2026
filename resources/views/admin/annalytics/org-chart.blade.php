@extends('admin.master')

@section('title')
    {{ $definition['title'] }}
@endsection

@section('content')
<style>
    .org-toolbar { margin-bottom: 16px; }
    .org-summary {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
        margin-bottom: 20px;
    }
    .org-summary-item {
        background: #f0fdfa;
        border: 1px solid #99f6e4;
        border-radius: 10px;
        padding: 12px 16px;
        min-width: 160px;
    }
    .org-summary-item .label { color: #0f766e; font-size: 12px; font-weight: 600; text-transform: uppercase; }
    .org-summary-item .value { color: #134e4a; font-size: 22px; font-weight: 700; margin-top: 4px; }
    .org-chart-wrap {
        overflow-x: auto;
        padding: 24px 8px 40px;
        background:
            radial-gradient(circle at top, rgba(15, 118, 110, 0.06), transparent 45%),
            #fff;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
    }
    .org-tree, .org-tree ul {
        padding-top: 20px;
        position: relative;
        display: flex;
        justify-content: center;
        list-style: none;
        margin: 0;
    }
    .org-tree ul { padding-top: 28px; }
    .org-tree li {
        position: relative;
        padding: 28px 10px 0;
        text-align: center;
        list-style: none;
    }
    .org-tree li::before,
    .org-tree li::after {
        content: '';
        position: absolute;
        top: 0;
        right: 50%;
        border-top: 2px solid #99f6e4;
        width: 50%;
        height: 28px;
    }
    .org-tree li::after {
        right: auto;
        left: 50%;
        border-left: 2px solid #99f6e4;
    }
    .org-tree li:only-child::before,
    .org-tree li:only-child::after { display: none; }
    .org-tree li:only-child { padding-top: 0; }
    .org-tree li:first-child::before,
    .org-tree li:last-child::after { border: 0 none; }
    .org-tree li:last-child::before {
        border-right: 2px solid #99f6e4;
        border-radius: 0 6px 0 0;
    }
    .org-tree li:first-child::after {
        border-radius: 6px 0 0 0;
    }
    .org-tree > li::before,
    .org-tree > li::after { border: 0; }
    .org-node {
        display: inline-block;
        min-width: 180px;
        max-width: 220px;
        padding: 12px 14px;
        background: #fff;
        border: 2px solid #0f766e;
        border-radius: 12px;
        box-shadow: 0 8px 18px rgba(15, 118, 110, 0.08);
        text-align: center;
        vertical-align: top;
    }
    .org-node-name { font-weight: 700; color: #134e4a; font-size: 14px; line-height: 1.3; }
    .org-node-role { color: #0f766e; font-size: 12px; margin-top: 4px; font-weight: 600; }
    .org-node-meta { color: #6b7280; font-size: 11px; margin-top: 6px; line-height: 1.35; }
    .org-node-reports { color: #0d9488; font-size: 11px; margin-top: 8px; font-weight: 600; }
    .org-section-title {
        font-size: 16px;
        font-weight: 700;
        color: #111827;
        margin: 28px 0 12px;
    }
    .unassigned-grid {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
    }
    .unassigned-grid .org-node { border-color: #d1d5db; box-shadow: none; }
    .print-only { display: none; }

    @media print {
        .bg-title,
        .org-toolbar,
        .no-print,
        .sidebar,
        .navbar,
        .footer,
        .left-sidebar,
        #side-menu,
        .navbar-header {
            display: none !important;
        }
        .print-only { display: block !important; }
        body, .container-fluid, .white-box, .org-chart-wrap {
            background: #fff !important;
            box-shadow: none !important;
            border: none !important;
        }
        .org-chart-wrap {
            overflow: visible;
            padding: 0;
        }
        .org-node {
            box-shadow: none;
            break-inside: avoid;
        }
        @page { size: landscape; margin: 12mm; }
    }
</style>

<div class="container-fluid">
    <div class="row bg-title no-print">
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
            <button type="button" class="btn btn-info btn-sm" onclick="window.print()">
                <i class="fa fa-print"></i> Print
            </button>
            <a href="{{ route('reports.annalytics.export', array_filter(['report' => 'org-chart', 'department_id' => $filters['department_id'] ?? null])) }}"
               class="btn btn-success btn-sm">
                <i class="fa fa-file-pdf-o"></i> Download PDF
            </a>
        </div>
    </div>

    <div class="print-only" style="margin-bottom: 16px;">
        @include('admin.partials.company_report_header')
        <h2 style="margin: 0 0 4px;">{{ $definition['title'] }}</h2>
        <p style="margin: 0; color: #6b7280;">
            Generated {{ $generated_at->format('d M Y H:i') }}
            @if(!empty($filters['department_id']))
                · Filtered by department
            @endif
        </p>
    </div>

    <div class="white-box org-toolbar no-print">
        <form method="GET" class="form-inline">
            <label for="department_id" style="margin-right: 8px;">Department</label>
            <select name="department_id" id="department_id" class="form-control" style="width: 260px; margin-right: 10px;">
                <option value="">All Departments</option>
                @foreach($departments as $department)
                    <option value="{{ $department->department_id }}"
                        {{ ($filters['department_id'] ?? null) == $department->department_id ? 'selected' : '' }}>
                        {{ $department->department_name }}
                    </option>
                @endforeach
            </select>
            <button type="submit" class="btn btn-info">Apply</button>
            @if(!empty($filters['department_id']))
                <a href="{{ route('reports.annalytics.show', 'org-chart') }}" class="btn btn-default" style="margin-left: 6px;">Clear</a>
            @endif
        </form>
        <p style="margin: 12px 0 0; color: #6b7280;">
            Built from active employee supervisor relationships. Use Print for a printable page or Download PDF for a landscape PDF.
        </p>
    </div>

    <div class="org-summary">
        @foreach($summary as $item)
            <div class="org-summary-item">
                <div class="label">{{ $item['label'] }}</div>
                <div class="value">{{ $item['value'] }}</div>
            </div>
        @endforeach
    </div>

    <div class="org-chart-wrap">
        @forelse($trees as $tree)
            <div style="margin-bottom: 40px;">
                <ul class="org-tree">
                    @include('admin.annalytics.partials.org-chart-node', ['node' => $tree])
                </ul>
            </div>
        @empty
            <div class="text-center" style="padding: 40px; color: #6b7280;">
                No active employees found for the selected filters.
            </div>
        @endforelse
    </div>

    @if(!empty($unassigned))
        <div class="org-section-title">Unlinked Employees</div>
        <p class="no-print" style="color: #6b7280;">These employees could not be placed cleanly in the hierarchy (for example due to circular supervisor links).</p>
        <div class="unassigned-grid">
            @foreach($unassigned as $node)
                <div class="org-node">
                    <div class="org-node-name">{{ $node['name'] }}</div>
                    <div class="org-node-role">{{ $node['designation'] }}</div>
                    <div class="org-node-meta">{{ $node['department'] }}</div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
