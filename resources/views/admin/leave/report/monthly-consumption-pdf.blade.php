<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Monthly Leave Consumption Report - {{ $selectedYear }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 10px;
            line-height: 1.4;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #4CAF50;
        }

        .header h1 {
            color: #4CAF50;
            margin: 0;
            font-size: 18px;
        }

        .header h3 {
            margin: 5px 0;
            font-size: 14px;
        }

        .header p {
            margin: 2px 0;
            color: #666;
            font-size: 10px;
        }

        .summary {
            margin-bottom: 20px;
            padding: 10px;
            background-color: #f5f5f5;
            border-radius: 5px;
        }

        .summary table {
            width: 100%;
        }

        .summary td {
            padding: 5px;
            font-size: 11px;
        }

        .summary .label {
            font-weight: bold;
            color: #4CAF50;
        }

        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        table.data-table th {
            background-color: #4CAF50;
            color: white;
            padding: 8px 5px;
            text-align: center;
            font-weight: bold;
            font-size: 9px;
            border: 1px solid #45a049;
        }

        table.data-table td {
            padding: 5px;
            border: 1px solid #ddd;
            text-align: center;
            font-size: 9px;
        }

        table.data-table tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .total-row {
            font-weight: bold;
            background-color: #e8f5e8 !important;
        }

        .footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            text-align: center;
            font-size: 8px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 5px;
        }

        .month-highlight {
            background-color: #ffd70033;
        }

        .page-number:before {
            content: "Page " counter(page);
        }

        .watermark {
            position: fixed;
            bottom: 50%;
            right: 20%;
            opacity: 0.1;
            font-size: 60px;
            color: #4CAF50;
            transform: rotate(-45deg);
            z-index: -1;
        }
    </style>
</head>

<body>
    <div class="watermark">ANNUAL LEAVE</div>

    <div class="header">
        @if (isset($printHead))
            <h1>{{ $printHead->print_head }}</h1>
        @endif
        <h3>Monthly Leave Consumption Report (Annual Leave Only)</h3>
        <p>For the Year: <strong>{{ $selectedYear }}</strong></p>
        <p>Generated on: {{ $generatedDate }}</p>
    </div>

    <div class="summary">
        <table>
            <tr>
                <td class="label">Total Employees:</td>
                <td>{{ count($reportData) }}</td>
                <td class="label">Total Leave Days:</td>
                <td>{{ array_sum($monthlyTotals) }}</td>
            </tr>
            <tr>
                <td class="label">Employees with Leave:</td>
                <td>{{ count(array_filter($reportData, function ($item) {return $item['total'] > 0;})) }}</td>
                <td class="label">Average per Employee:</td>
                <td>{{ count($reportData) > 0 ? round(array_sum($monthlyTotals) / count($reportData), 1) : 0 }}</td>
            </tr>
        </table>
    </div>

    <table class="data-table">
        <thead>
            <tr>
                <th rowspan="2">Employee Name</th>
                <th rowspan="2">Payroll No</th>
                <th rowspan="2">Location</th>
                <th rowspan="2">Department</th>
                <th colspan="12" style="text-align: center;">{{ $selectedYear }}</th>
                <th rowspan="2">Total</th>
            </tr>
            <tr>
                @foreach ($monthNames as $month)
                    <th>{{ substr($month, 0, 3) }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach ($reportData as $data)
                <tr>
                    <td style="text-align: left;">{{ $data['employee_name'] }}</td>
                    <td>{{ $data['payroll_number'] }}</td>
                    <td>{{ $data['location'] }}</td>
                    <td>{{ $data['department'] }}</td>
                    @for ($m = 1; $m <= 12; $m++)
                        <td class="{{ $data['monthly'][$m] > 0 ? 'month-highlight' : '' }}">
                            {{ $data['monthly'][$m] > 0 ? $data['monthly'][$m] : '-' }}
                        </td>
                    @endfor
                    <td><strong>{{ $data['total'] > 0 ? $data['total'] : '-' }}</strong></td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="4" style="text-align: right;"><strong>TOTAL</strong></td>
                @for ($m = 1; $m <= 12; $m++)
                    <td><strong>{{ $monthlyTotals[$m] > 0 ? $monthlyTotals[$m] : '-' }}</strong></td>
                @endfor
                <td><strong>{{ array_sum($monthlyTotals) }}</strong></td>
            </tr>
        </tfoot>
    </table>

    <div class="footer">
        <p>This report shows Annual Leave consumption only. Generated by HR System on {{ $generatedDate }}</p>
        <span class="page-number"></span>
    </div>
</body>

</html>
