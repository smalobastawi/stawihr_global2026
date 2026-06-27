<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Personal Development Plan - {{ $plan->plan_title }}</title>
    <style>
        * { box-sizing: border-box; }
        body {
            font-family: DejaVu Sans, Arial, sans-serif;
            font-size: 10pt;
            color: #222;
            line-height: 1.45;
        }
        .print-head {
            text-align: center;
            margin-bottom: 18px;
        }
        h1 {
            font-size: 16pt;
            margin: 0 0 4px;
            text-align: center;
        }
        .subtitle {
            text-align: center;
            color: #555;
            margin-bottom: 18px;
        }
        .meta-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 16px;
        }
        .meta-table td {
            padding: 4px 8px 4px 0;
            vertical-align: top;
        }
        .meta-label {
            font-weight: bold;
            width: 140px;
        }
        .section-title {
            font-size: 12pt;
            margin: 18px 0 8px;
            border-bottom: 1px solid #333;
            padding-bottom: 4px;
        }
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 16px;
        }
        table.data-table th,
        table.data-table td {
            border: 1px solid #333;
            padding: 6px 8px;
            vertical-align: top;
        }
        table.data-table th {
            background: #f0f0f0;
            text-align: left;
        }
        .signature-block {
            margin-top: 22px;
            page-break-inside: avoid;
        }
        .signature-block h3 {
            font-size: 11pt;
            margin: 0 0 10px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .signature-line {
            margin: 0 0 8px;
        }
        .signature-line strong {
            display: inline-block;
            width: 95px;
        }
        .signature-value {
            display: inline-block;
            min-width: 280px;
            border-bottom: 1px solid #333;
            padding-bottom: 2px;
            min-height: 16px;
        }
        .comments-box {
            border: 1px solid #333;
            min-height: 48px;
            padding: 8px;
            margin-top: 4px;
            white-space: pre-wrap;
        }
        .text-block {
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="print-head">
        @if($printHead)
            {!! $printHead->description !!}
        @endif
    </div>

    <h1>Personal Development Plan</h1>
    <div class="subtitle">{{ $plan->plan_title }} ({{ $plan->plan_year }})</div>

    <table class="meta-table">
        <tr>
            <td class="meta-label">Employee</td>
            <td>{{ $plan->employee?->full_name ?? 'N/A' }}</td>
            <td class="meta-label">Department</td>
            <td>{{ $plan->department?->department_name ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td class="meta-label">Designation</td>
            <td>{{ $plan->designation?->designation_name ?? 'N/A' }}</td>
            <td class="meta-label">Supervisor</td>
            <td>{{ $plan->supervisor?->full_name ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td class="meta-label">Plan Period</td>
            <td>{{ $plan->start_date?->format('d M Y') }} – {{ $plan->end_date?->format('d M Y') }}</td>
            <td class="meta-label">Review Frequency</td>
            <td>{{ ucfirst(str_replace('_', '-', $plan->review_frequency)) }}</td>
        </tr>
        <tr>
            <td class="meta-label">Status</td>
            <td>{{ ucfirst($plan->status) }}</td>
            <td class="meta-label">Overall Progress</td>
            <td>{{ $plan->averageProgress() }}%</td>
        </tr>
    </table>

    @if($plan->development_focus)
        <div class="text-block"><strong>Development Focus:</strong> {{ $plan->development_focus }}</div>
    @endif

    @if($plan->career_aspirations)
        <div class="text-block"><strong>Career Aspirations:</strong> {{ $plan->career_aspirations }}</div>
    @endif

    <div class="section-title">Development Goals</div>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 28%;">Goal</th>
                <th style="width: 32%;">SMART Objective</th>
                <th style="width: 14%;">Competency</th>
                <th style="width: 10%;">Status</th>
                <th style="width: 8%;">Progress</th>
                <th style="width: 8%;">Target</th>
            </tr>
        </thead>
        <tbody>
            @forelse($plan->goals as $goal)
                <tr>
                    <td>{{ $goal->goal_title }}</td>
                    <td>{{ $goal->smart_objective }}</td>
                    <td>{{ $goal->competency_area ?? '—' }}</td>
                    <td>{{ ucfirst(str_replace('_', ' ', $goal->status)) }}</td>
                    <td>{{ $goal->overall_progress }}%</td>
                    <td>{{ $goal->target_completion_date?->format('d M Y') ?? '—' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6">No goals recorded on this plan.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="section-title">Sign-off</div>

    <div class="signature-block">
        <h3>Employee</h3>
        <p class="signature-line"><strong>Signature:</strong> <span class="signature-value">{{ $signatures['employee']['signature'] }}</span></p>
        <p class="signature-line"><strong>Date:</strong> <span class="signature-value">{{ $signatures['employee']['date'] }}</span></p>
        <p class="signature-line"><strong>Comments:</strong></p>
        <div class="comments-box">{{ $signatures['employee']['comments'] }}</div>
    </div>

    <div class="signature-block">
        <h3>Supervisor</h3>
        <p class="signature-line"><strong>Signature:</strong> <span class="signature-value">{{ $signatures['supervisor']['signature'] }}</span></p>
        <p class="signature-line"><strong>Date:</strong> <span class="signature-value">{{ $signatures['supervisor']['date'] }}</span></p>
        <p class="signature-line"><strong>Comments:</strong></p>
        <div class="comments-box">{{ $signatures['supervisor']['comments'] }}</div>
    </div>

    <div class="signature-block">
        <h3>HR</h3>
        <p class="signature-line"><strong>Signature:</strong> <span class="signature-value">{{ $signatures['hr']['signature'] }}</span></p>
        <p class="signature-line"><strong>Date:</strong> <span class="signature-value">{{ $signatures['hr']['date'] }}</span></p>
        <p class="signature-line"><strong>Comments:</strong></p>
        <div class="comments-box">{{ $signatures['hr']['comments'] }}</div>
    </div>
</body>
</html>
