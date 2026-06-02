<!DOCTYPE html>
<html lang="en">
<head>
    <title>PAYE Report Export</title>
    <meta charset="utf-8">
</head>
<style>
    table {
        margin: 0 0 20px 0;
        width: 100%;
        border-collapse: collapse;
    }

    table, td, th {
        border: 1px solid black;
    }

    td, th {
        padding: 5px;
    }

    th {
        background-color: #f0f0f0;
        font-weight: bold;
    }
</style>
<body>
    @include('admin.partials.company_report_header')
    <h3>PAYE Report - {{ $dataExport['period']->name ?? '' }}</h3>
    <p>
        Period: {{ $dataExport['period']->start_date->format('d M Y') ?? '' }}
        - {{ $dataExport['period']->end_date->format('d M Y') ?? '' }}
    </p>

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
            @foreach($dataExport['records'] as $index => $record)
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
                    <td>{{ $record->basic_salary ?? 0 }}</td>
                    <td>{{ $record->gross_salary ?? 0 }}</td>
                    <td>{{ $record->nssf_contribution ?? 0 }}</td>
                    <td>{{ $record->shif_contribution ?? 0 }}</td>
                    <td>{{ $record->housing_levy ?? 0 }}</td>
                    <td>{{ $record->pension_contribution ?? 0 }}</td>
                    <td>{{ $record->paye_tax ?? 0 }}</td>
                    <td>{{ $record->net_salary ?? 0 }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th colspan="4" style="text-align:right">Totals:</th>
                <th>{{ $dataExport['records']->sum('basic_salary') }}</th>
                <th>{{ $dataExport['totals']['total_gross'] }}</th>
                <th>{{ $dataExport['records']->sum('nssf_contribution') }}</th>
                <th>{{ $dataExport['records']->sum('shif_contribution') }}</th>
                <th>{{ $dataExport['records']->sum('housing_levy') }}</th>
                <th>{{ $dataExport['records']->sum('pension_contribution') }}</th>
                <th>{{ $dataExport['totals']['total_paye'] }}</th>
                <th>{{ $dataExport['records']->sum('net_salary') }}</th>
            </tr>
        </tfoot>
    </table>
</body>
</html>
