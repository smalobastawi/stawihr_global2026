<!DOCTYPE html>
<html lang="en">
<head>
    <title>P10 Return - {{ $dataExport['period']->name ?? '' }}</title>
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
    <h3>P10 Monthly PAYE Return - {{ $dataExport['period']->name ?? '' }}</h3>
    <p>
        Period: {{ $dataExport['period']->start_date->format('d M Y') ?? '' }}
        - {{ $dataExport['period']->end_date->format('d M Y') ?? '' }}
    </p>

    <table>
        <thead>
            <tr>
                <th>Employer's PIN</th>
                <th>PIN of Employee</th>
                <th>Name of Employee</th>
                <th>Resident Status</th>
                <th>Type of Employee</th>
                <th>Basic Salary</th>
                <th>Housing Allowance</th>
                <th>Transport Allowance</th>
                <th>Leave Pay</th>
                <th>Over Time Allowance</th>
                <th>Other Allowance</th>
                <th>Total Cash Pay</th>
                <th>Value of Car Benefit</th>
                <th>Other Non-Cash Benefits</th>
                <th>Total Gross Pay</th>
                <th>Social Health Insurance Fund (J)</th>
                <th>Affordable Housing Levy (N)</th>
                <th>Actual Pension Contribution (K)</th>
                <th>Post Retirement Medical</th>
                <th>Amount of Insurance Relief (Ksh) (S)</th>
                <th>PAYE Tax</th>
            </tr>
        </thead>
        <tbody>
            @foreach($dataExport['p10Rows'] as $row)
                <tr>
                    <td>{{ $row['Employer PIN'] ?? '' }}</td>
                    <td>{{ $row['PIN of Employee'] }}</td>
                    <td>{{ $row['Name of Employee'] }}</td>
                    <td>{{ $row['Resident Status'] }}</td>
                    <td>{{ $row['Type of Employee'] }}</td>
                    <td>{{ $row['Basic Salary'] }}</td>
                    <td>{{ $row['Housing Allowance'] }}</td>
                    <td>{{ $row['Transport Allowance'] }}</td>
                    <td>{{ $row['Leave Pay'] ?? 0 }}</td>
                    <td>{{ $row['Over Time Allowance'] }}</td>
                    <td>{{ $row['Other Allowance'] }}</td>
                    <td>{{ $row['Total Cash Pay'] ?? $row['Basic Salary'] + $row['Housing Allowance'] + $row['Transport Allowance'] + $row['Over Time Allowance'] + $row['Other Allowance'] }}</td>
                    <td>{{ $row['Value of Car Benefit'] ?? 0 }}</td>
                    <td>{{ $row['Other Non-Cash Benefits'] ?? 0 }}</td>
                    <td>{{ $row['Total Gross Pay'] ?? $row['Gross Salary'] }}</td>
                    <td>{{ $row['Social Health Insurance Fund (J)'] }}</td>
                    <td>{{ $row['Affordable Housing Levy (N)'] }}</td>
                    <td>{{ $row['Actual Pension Contribution (K)'] }}</td>
                    <td>{{ $row['Post Retirement Medical'] ?? 0 }}</td>
                    <td>{{ $row['Amount of Insurance Relief (Ksh) (S)'] }}</td>
                    <td>{{ $row['PAYE Tax'] }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th colspan="5" style="text-align:right">Totals:</th>
                <th>{{ $dataExport['p10Rows']->sum('Basic Salary') }}</th>
                <th>{{ $dataExport['p10Rows']->sum('Housing Allowance') }}</th>
                <th>{{ $dataExport['p10Rows']->sum('Transport Allowance') }}</th>
                <th>{{ $dataExport['p10Rows']->sum('Leave Pay') }}</th>
                <th>{{ $dataExport['p10Rows']->sum('Over Time Allowance') }}</th>
                <th>{{ $dataExport['p10Rows']->sum('Other Allowance') }}</th>
                <th>{{ $dataExport['p10Rows']->sum('Total Cash Pay') }}</th>
                <th>{{ $dataExport['p10Rows']->sum('Value of Car Benefit') }}</th>
                <th>{{ $dataExport['p10Rows']->sum('Other Non-Cash Benefits') }}</th>
                <th>{{ $dataExport['p10Rows']->sum('Total Gross Pay') }}</th>
                <th>{{ $dataExport['p10Rows']->sum('Social Health Insurance Fund (J)') }}</th>
                <th>{{ $dataExport['p10Rows']->sum('Affordable Housing Levy (N)') }}</th>
                <th>{{ $dataExport['p10Rows']->sum('Actual Pension Contribution (K)') }}</th>
                <th>{{ $dataExport['p10Rows']->sum('Post Retirement Medical') }}</th>
                <th>{{ $dataExport['p10Rows']->sum('Amount of Insurance Relief (Ksh) (S)') }}</th>
                <th>{{ $dataExport['summary']['total_paye'] }}</th>
            </tr>
        </tfoot>
    </table>
</body>
</html>
