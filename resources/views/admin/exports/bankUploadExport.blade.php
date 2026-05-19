<!DOCTYPE html>
<html lang="en">

<head>
    <title>KCB Salaries Upload</title>
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

    .printHead {
        width: 35%;
        margin: 0 auto;
    }

    table,
    td,
    th {
        border: 1px solid black;
    }

    td {
        padding: 5px;
        text-align: left;
        vertical-align: middle;
    }

    th {
        padding: 5px;
        background-color: #f0f0f0;
        font-weight: bold;
        text-align: center;
    }

    .header-row {
        background-color: #d4edda;
        font-weight: bold;
    }

    .total-row {
        background-color: #fff3cd;
        font-weight: bold;
    }

    .amount {
        text-align: right;
    }

    .center {
        text-align: center;
    }
</style>

<body>

    <div class="container">
        <div class="table-responsive">
            <h3 style="text-align: center; margin-bottom: 20px;">KCB SALARIES TEMPLATE</h3>
            <h4 style="text-align: center; margin-bottom: 30px;">{{ $payrollPeriod->name ?? 'Payroll Period' }} -
                {{ now()->format('d-M-Y H:i') }}</h4>

            <table id="bankUploadTable" class="table table-bordered">
                <thead>
                    <tr class="header-row">
                        <th>Debit/From Account</th>
                        <th>Your Location BIC/SORT Code</th>
                        <th>Beneficiary Name</th>
                        <th>Bank</th>
                        <th>Location</th>
                        <th>BIC/SORT Code (Mpesa 99999)</th>
                        <th>Account No./Phone Number</th>
                        <th>Net Pay/Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($bankUploadData as $data)
                        <tr>
                            <td class="center">{{ $data['debit_account'] }}</td>
                            <td class="center">{{ $data['branch_sort_code'] }}</td>
                            <td>{{ $data['beneficiary_name'] }}</td>
                            <td>{{ $data['bank'] }}</td>
                            <td>{{ $data['location'] }}</td>
                            <td class="center">{{ $data['bic_sort_code'] }}</td>
                            <td class="center">{{ $data['account_number'] }}</td>
                            <td class="amount">{{ $data['net_amount'] }}</td>
                        </tr>
                    @endforeach

                    @if ($bankUploadData->count() > 0)
                        <tr class="total-row">
                            <td colspan="7" style="text-align: right; font-weight: bold;">TOTAL</td>
                            <td class="amount">{{ number_format($totalAmount, 2) }}</td>
                        </tr>
                    @endif
                </tbody>
            </table>

            @if ($bankUploadData->count() == 0)
                <div style="text-align: center; padding: 20px; color: #666;">
                    <p>No payroll records found for bank upload in this period.</p>
                    <p>Ensure employees have approved payroll records with valid bank account information.</p>
                </div>
            @else
                <div style="margin-top: 20px; padding: 10px; background-color: #f8f9fa; border: 1px solid #dee2e6;">
                    <h5>Summary:</h5>
                    <ul>
                        <li><strong>Total Employees:</strong> {{ $bankUploadData->count() }}</li>
                        <li><strong>Total Amount:</strong> KES {{ number_format($totalAmount, 2) }}</li>
                        <li><strong>Period:</strong> {{ $payrollPeriod->name ?? 'N/A' }}</li>
                        <li><strong>Generated:</strong> {{ now()->format('d-M-Y H:i:s') }}</li>
                    </ul>
                </div>
            @endif
        </div>
    </div>

</body>

</html>
