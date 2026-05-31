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
    <h3>P10 Monthly PAYE Return - {{ $dataExport['period']->name ?? '' }}</h3>
    <p>
        Period: {{ $dataExport['period']->start_date->format('d M Y') ?? '' }}
        - {{ $dataExport['period']->end_date->format('d M Y') ?? '' }}
    </p>

    <table>
        <thead>
            <tr>
                <th>PIN of Employee</th>
                <th>Name of Employee</th>
                <th>Resident Status</th>
                <th>Type of Employee</th>
                <th>Basic Salary</th>
                <th>Housing Allowance</th>
                <th>Transport Allowance</th>
                <th>Over Time Allowance</th>
                <th>Other Allowance</th>
                <th>Social Health Insurance Fund (J)</th>
                <th>Affordable Housing Levy (N)</th>
                <th>Actual Pension Contribution (K)</th>
                <th>Amount of Insurance Relief (Ksh) (S)</th>
                <th>PAYE Tax</th>
            </tr>
        </thead>
        <tbody>
            @foreach($dataExport['p10Rows'] as $row)
                <tr>
                    <td>{{ $row['PIN of Employee'] }}</td>
                    <td>{{ $row['Name of Employee'] }}</td>
                    <td>{{ $row['Resident Status'] }}</td>
                    <td>{{ $row['Type of Employee'] }}</td>
                    <td>{{ $row['Basic Salary'] }}</td>
                    <td>{{ $row['Housing Allowance'] }}</td>
                    <td>{{ $row['Transport Allowance'] }}</td>
                    <td>{{ $row['Over Time Allowance'] }}</td>
                    <td>{{ $row['Other Allowance'] }}</td>
                    <td>{{ $row['Social Health Insurance Fund (J)'] }}</td>
                    <td>{{ $row['Affordable Housing Levy (N)'] }}</td>
                    <td>{{ $row['Actual Pension Contribution (K)'] }}</td>
                    <td>{{ $row['Amount of Insurance Relief (Ksh) (S)'] }}</td>
                    <td>{{ $row['PAYE Tax'] }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th colspan="4" style="text-align:right">Totals:</th>
                <th>{{ $dataExport['p10Rows']->sum('Basic Salary') }}</th>
                <th>{{ $dataExport['p10Rows']->sum('Housing Allowance') }}</th>
                <th>{{ $dataExport['p10Rows']->sum('Transport Allowance') }}</th>
                <th>{{ $dataExport['p10Rows']->sum('Over Time Allowance') }}</th>
                <th>{{ $dataExport['p10Rows']->sum('Other Allowance') }}</th>
                <th>{{ $dataExport['p10Rows']->sum('Social Health Insurance Fund (J)') }}</th>
                <th>{{ $dataExport['p10Rows']->sum('Affordable Housing Levy (N)') }}</th>
                <th>{{ $dataExport['p10Rows']->sum('Actual Pension Contribution (K)') }}</th>
                <th>{{ $dataExport['p10Rows']->sum('Amount of Insurance Relief (Ksh) (S)') }}</th>
                <th>{{ $dataExport['summary']['total_paye'] }}</th>
            </tr>
        </tfoot>
    </table>
</body>
</html>
