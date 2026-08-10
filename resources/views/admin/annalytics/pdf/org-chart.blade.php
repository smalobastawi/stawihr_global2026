<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>{{ $definition['title'] }}</title>
    <style>
        body {
            font-family: DejaVu Sans, Arial, sans-serif;
            font-size: 10pt;
            color: #222;
            line-height: 1.4;
        }
        h1 {
            font-size: 16pt;
            margin: 0 0 4px;
            text-align: center;
        }
        .subtitle {
            text-align: center;
            color: #555;
            margin-bottom: 14px;
        }
        .summary-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 16px;
        }
        .summary-table td {
            border: 1px solid #ccc;
            padding: 6px 8px;
            width: 25%;
        }
        .summary-label {
            font-size: 8pt;
            color: #555;
            text-transform: uppercase;
        }
        .summary-value {
            font-size: 13pt;
            font-weight: bold;
            color: #0f766e;
        }
        .section-title {
            font-size: 12pt;
            font-weight: bold;
            margin: 16px 0 8px;
            border-bottom: 1px solid #0f766e;
            padding-bottom: 3px;
            color: #0f766e;
        }
        .node-table {
            border-collapse: collapse;
            margin: 0 0 6px 0;
            width: auto;
        }
        .node-cell {
            border: 1px solid #0f766e;
            border-radius: 4px;
            padding: 6px 10px;
            background: #f0fdfa;
            min-width: 180px;
        }
        .muted { color: #555; font-size: 9pt; }
        .tree-block { margin-bottom: 18px; page-break-inside: avoid; }
        .footer-note {
            margin-top: 18px;
            font-size: 8pt;
            color: #777;
            border-top: 1px solid #ddd;
            padding-top: 6px;
        }
    </style>
</head>
<body>
    @if(!empty($printHead) && !empty($printHead->description))
        <div style="text-align: center; margin-bottom: 10px;">
            {!! $printHead->description !!}
        </div>
    @else
        @include('admin.partials.company_report_header')
    @endif

    <h1>{{ $definition['title'] }}</h1>
    <div class="subtitle">
        Generated {{ $generated_at->format('d M Y H:i') }}
        @if(!empty($filters['department_id']))
            @php
                $departmentName = optional($departments->firstWhere('department_id', $filters['department_id']))->department_name;
            @endphp
            · Department: {{ $departmentName ?? 'Selected' }}
        @else
            · All Departments
        @endif
    </div>

    <table class="summary-table">
        <tr>
            @foreach($summary as $item)
                <td>
                    <div class="summary-label">{{ $item['label'] }}</div>
                    <div class="summary-value">{{ $item['value'] }}</div>
                </td>
            @endforeach
        </tr>
    </table>

    <div class="section-title">Reporting Hierarchy</div>

    @forelse($trees as $index => $tree)
        <div class="tree-block">
            <div class="muted" style="margin-bottom: 4px;">Branch {{ $index + 1 }}</div>
            @include('admin.annalytics.partials.org-chart-pdf-node', ['node' => $tree, 'level' => 0])
        </div>
    @empty
        <p>No active employees found for the selected filters.</p>
    @endforelse

    @if(!empty($unassigned))
        <div class="section-title">Unlinked Employees</div>
        @foreach($unassigned as $node)
            @include('admin.annalytics.partials.org-chart-pdf-node', ['node' => $node, 'level' => 0])
        @endforeach
    @endif

    <div class="footer-note">
        Hierarchy is derived from active employee supervisor assignments in StawiHR.
    </div>
</body>
</html>
