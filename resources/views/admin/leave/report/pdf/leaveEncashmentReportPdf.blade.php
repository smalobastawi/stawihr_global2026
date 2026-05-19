<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Leave Encashment Report</title>
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

        .company-info {
            text-align: center;
            margin-bottom: 15px;
        }

        .company-info h2 {
            margin: 0;
            font-size: 16px;
        }

        .company-info p {
            margin: 2px 0;
            font-size: 9px;
            color: #666;
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
            text-align: left;
            font-size: 9px;
        }

        table.data-table tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .text-center {
            text-align: center !important;
        }

        .text-right {
            text-align: right !important;
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

        .badge {
            background-color: #28a745;
            color: white;
            padding: 2px 6px;
            border-radius: 3px;
            font-weight: bold;
        }
    </style>
</head>

<body>
    @if(isset($printHead) && $printHead)
    <div class="company-info">
        <h2>{{ $printHead->company_name ?? 'Company Name' }}</h2>
        <p>{{ $printHead->address ?? '' }}</p>
        <p>{{ $printHead->email ?? '' }} | {{ $printHead->phone ?? '' }}</p>
    </div>
    @endif

    <div class="header">
        <h1>Leave Encashment Report</h1>
        @if($financialYear)
        <h3>Financial Year: {{ $financialYear->name }}</h3>
        @endif
        <p>Generated on: {{ $generatedDate ?? date('d/m/Y H:i:s') }}</p>
    </div>

    <!-- Summary Section -->
    <div class="summary">
        <table>
            <tr>
                <td class="label">Total Days Encashed:</td>
                <td><strong>{{ number_format($totalDaysEncashed, 2) }}</strong></td>
                <td class="label">Total Employees:</td>
                <td><strong>{{ $encashments->unique('employee_id')->count() }}</strong></td>
                <td class="label">Total Records:</td>
                <td><strong>{{ $encashments->count() }}</strong></td>
            </tr>
        </table>
    </div>

    <!-- Data Table -->
    <table class="data-table">
        <thead>
            <tr>
                <th>#</th>
                <th>Employee Name</th>
                <th>Payroll No.</th>
                <th>Department</th>
                <th>Leave Type</th>
                <th>Days Encashed</th>
                <th>Financial Year</th>
                <th>Date</th>
                <th>Reason</th>
            </tr>
        </thead>
        <tbody>
            @forelse($encashments as $index => $encashment)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $encashment->employee->fullName() ?? 'N/A' }}</td>
                <td class="text-center">{{ $encashment->employee->payroll_number ?? 'N/A' }}</td>
                <td>{{ $encashment->employee->department->department_name ?? 'N/A' }}</td>
                <td>{{ $encashment->leaveType->leave_type_name ?? 'N/A' }}</td>
                <td class="text-center"><span class="badge">{{ number_format($encashment->adjustment_days, 2) }}</span></td>
                <td class="text-center">{{ $encashment->financialYear->name ?? 'N/A' }}</td>
                <td class="text-center">{{ $encashment->adjustment_date ? date('d/m/Y', strtotime($encashment->adjustment_date)) : 'N/A' }}</td>
                <td>{{ $encashment->reason }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="9" class="text-center">No leave encashment records found.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <p>Leave Encashment Report - Generated by {{ config('app.name') }}</p>
    </div>
</body>

</html>
