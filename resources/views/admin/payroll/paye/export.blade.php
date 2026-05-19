<!DOCTYPE html>
<html lang="en">
<head>
    <title>PAYE Report - {{ $year }}</title>
    <meta charset="utf-8">
</head>
<style>
    table {
        margin: 0 0 40px 0;
        width: 100%;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.2);
        display: table;
        border-collapse: collapse;
    }
    table, td, th {
        border: 1px solid black;
    }
    td {
        padding: 5px;
    }
    th {
        padding: 5px;
    }
</style>
<body>

<div class="container">
    <div class="table-responsive">
        <table id="payeReportTable" class="table table-bordered">
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
                @if(isset($payeReportData) && count($payeReportData) > 0)
                    @foreach($payeReportData as $data)
                        <tr>
                            <td>{{ $data['PIN of Employee'] }}</td>
                            <td>{{ $data['Name of Employee'] }}</td>
                            <td>{{ $data['Resident Status'] }}</td>
                            <td>{{ $data['Type of Employee'] }}</td>
                            <td>{{ number_format($data['Basic Salary'], 2) }}</td>
                            <td>{{ number_format($data['Housing Allowance'], 2) }}</td>
                            <td>{{ number_format($data['Transport Allowance'], 2) }}</td>
                            <td>{{ number_format($data['Over Time Allowance'], 2) }}</td>
                            <td>{{ number_format($data['Other Allowance'], 2) }}</td>
                            <td>{{ number_format($data['Social Health Insurance Fund (J)'], 2) }}</td>
                            <td>{{ number_format($data['Affordable Housing Levy (N)'], 2) }}</td>
                            <td>{{ number_format($data['Actual Pension Contribution (K)'], 2) }}</td>
                            <td>{{ number_format($data['Amount of Insurance Relief (Ksh) (S)'], 2) }}</td>
                            <td>{{ number_format($data['PAYE Tax'], 2) }}</td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="14" class="text-center">No data available for the selected year.</td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>
</div>

</body>
</html>