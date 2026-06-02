<!DOCTYPE html>
<html lang="en">
<head>
    <title>PAYE Report - {{ $period->name }}</title>
    <meta charset="utf-8">
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; margin: 20px; }
        h2, h3 { margin: 0 0 10px 0; }
        .summary { margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        table, td, th { border: 1px solid #333; }
        td, th { padding: 6px; text-align: left; }
        th { background: #eee; }
        .text-right { text-align: right; }
        @media print {
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="no-print" style="margin-bottom: 15px;">
        <button onclick="window.print()">Print / Save as PDF</button>
    </div>

    @include('admin.partials.company_report_header')

    <h2>PAYE Report</h2>
    <div class="summary">
        <p><strong>Period:</strong> {{ $period->name }}</p>
        <p><strong>Date Range:</strong> {{ $period->start_date->format('d M Y') }} - {{ $period->end_date->format('d M Y') }}</p>
        <p><strong>Employees:</strong> {{ $totals['employees'] }} |
           <strong>Total Gross:</strong> KES {{ number_format($totals['total_gross'], 2) }} |
           <strong>Total PAYE:</strong> KES {{ number_format($totals['total_paye'], 2) }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Employee Code</th>
                <th>Employee Name</th>
                <th>KRA PIN</th>
                <th>Basic Salary</th>
                <th>Gross Salary</th>
                <th>NSSF</th>
                <th>SHIF</th>
                <th>Housing Levy</th>
                <th>Pension</th>
                <th>PAYE Tax</th>
                <th>Net Salary</th>
            </tr>
        </thead>
        <tbody>
            @foreach($records as $index => $record)
                @php
                    $employee = $record->employee;
                    $employeePayroll = $record->employeePayroll;
                    $kraPin = $employeePayroll->kra_pin ?? $employee->KRA_Pin ?? '';
                    $employeeName = trim(($employee->first_name ?? '') . ' ' . ($employee->last_name ?? ''));
                @endphp
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $employee->staff_no ?? $employeePayroll->payroll_number ?? '' }}</td>
                    <td>{{ $employeeName }}</td>
                    <td>{{ $kraPin }}</td>
                    <td class="text-right">{{ number_format($record->basic_salary ?? 0, 2) }}</td>
                    <td class="text-right">{{ number_format($record->gross_salary ?? 0, 2) }}</td>
                    <td class="text-right">{{ number_format($record->nssf_contribution ?? 0, 2) }}</td>
                    <td class="text-right">{{ number_format($record->shif_contribution ?? 0, 2) }}</td>
                    <td class="text-right">{{ number_format($record->housing_levy ?? 0, 2) }}</td>
                    <td class="text-right">{{ number_format($record->pension_contribution ?? 0, 2) }}</td>
                    <td class="text-right">{{ number_format($record->paye_tax ?? 0, 2) }}</td>
                    <td class="text-right">{{ number_format($record->net_salary ?? 0, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
        @if($records->isNotEmpty())
            <tfoot>
                <tr>
                    <th colspan="4" class="text-right">Totals:</th>
                    <th class="text-right">{{ number_format($records->sum('basic_salary'), 2) }}</th>
                    <th class="text-right">{{ number_format($totals['total_gross'], 2) }}</th>
                    <th class="text-right">{{ number_format($records->sum('nssf_contribution'), 2) }}</th>
                    <th class="text-right">{{ number_format($records->sum('shif_contribution'), 2) }}</th>
                    <th class="text-right">{{ number_format($records->sum('housing_levy'), 2) }}</th>
                    <th class="text-right">{{ number_format($records->sum('pension_contribution'), 2) }}</th>
                    <th class="text-right">{{ number_format($totals['total_paye'], 2) }}</th>
                    <th class="text-right">{{ number_format($records->sum('net_salary'), 2) }}</th>
                </tr>
            </tfoot>
        @endif
    </table>
</body>
</html>
