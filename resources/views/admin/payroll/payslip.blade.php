<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payslip - {{ $payrollRecord->employeePayroll->employee->first_name ?? 'Employee' }}
        {{ $payrollRecord->employeePayroll->employee->last_name ?? '' }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-size: 8pt;
        }

        body {
            font-family: 'Arial', sans-serif;
            background-color: #f5f5f5;
            color: #333;
            line-height: 1.3;
        }

        .payslip-container {
            max-width: 800px;
            margin: 10px auto;
            background: white;
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
            border-radius: 3px;
            overflow: hidden;
        }

        /* Header Section */
        .header {
            color: #000000;
            padding: 10px 15px;
            display: flex;
            flex-direction: column;
            align-items: center;
            border-bottom: 1px solid #ddd;
            text-align: center;
        }

        .logo-section {
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 10px;
        }

        .logo-placeholder {
            width: auto;
            height: auto;
            border-radius: 3px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #555;
            font-weight: bold;
            font-size: 7pt;
        }

        .company-name {
            font-weight: bold;
        }

        .payslip-title {
            text-align: center;
        }

        .payslip-title h1 {
            font-weight: bold;
            margin-bottom: 2px;
        }

        .payslip-title .period {
            opacity: 0.8;
        }

        /* Company and Employee Details */
        .details-section {
            padding: 15px;
            background: #ffffff;
            border-bottom: 1px solid #ddd;
        }

        .details-row {
            display: flex;
            gap: 20px;
            margin-bottom: 15px;
        }

        .company-details,
        .employee-details {
            flex: 1;
        }

        .details-title {
            font-weight: bold;
            color: #444;
            margin-bottom: 8px;
            padding-bottom: 4px;
            border-bottom: 1px solid #aaa;
        }

        .detail-item {
            display: flex;
            margin-bottom: 4px;
        }

        .detail-label {
            font-weight: 600;
            width: 80px;
            color: #555;
        }

        .detail-value {
            color: #333;
        }

        /* Main Content - Earnings and Deductions */
        .main-content {
            padding: 15px;
        }

        .columns-container {
            display: flex;
            gap: 15px;
            margin-bottom: 15px;
        }

        .earnings-column,
        .deductions-column {
            flex: 1;
        }

        .column-title {
            background: #e0e0e0;
            color: #333;
            padding: 6px 10px;
            font-weight: bold;
            text-align: center;
            margin-bottom: 0;
            border: 1px solid #ccc;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }

        .items-table th {
            background: #f0f0f0;
            padding: 6px;
            text-align: left;
            font-weight: 600;
            border: 1px solid #ddd;
            color: #555;
        }

        .items-table td {
            padding: 5px 6px;
            border: 1px solid #ddd;
        }

        .items-table tr:nth-child(even) {
            background: #f8f8f8;
        }

        .amount {
            text-align: right;
            font-weight: 600;
        }

        .total-row {
            background: #e8e8e8 !important;
            font-weight: bold;
        }

        .total-row td {
            border-top: 1px solid #aaa;
        }

        /* Company Contributions */
        .contributions-section {
            margin-top: 10px;
        }

        .contributions-title {
            background: #e0e0e0;
            color: #333;
            padding: 6px 10px;
            font-weight: bold;
            text-align: center;
            margin-bottom: 0;
            border: 1px solid #ccc;
        }

        /* Summary Section */
        .summary-section {
            background: #f0f0f0;
            color: #333;
            padding: 10px 15px;
            margin-top: 15px;
            border-top: 1px solid #aaa;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
        }

        .summary-row.net-pay {
            font-weight: bold;
            border-top: 1px solid #888;
            padding-top: 8px;
            margin-top: 8px;
        }

        /* Footer */
        .footer {
            background: #f8f8f8;
            padding: 10px 15px;
            text-align: center;
            color: #666;
            border-top: 1px solid #ddd;
        }

        .currency {
            font-weight: 600;
        }

        .statutory-info {
            margin-top: 10px;
            padding: 8px;
            background-color: #f8f8f8;
            border: 1px solid #ddd;
        }

        .statutory-info h4 {
            margin: 0 0 6px 0;
        }

        .statutory-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 3px;
        }

        @media print {
            body {
                background: white;
            }

            .payslip-container {
                box-shadow: none;
                margin: 0;
            }
        }
    </style>
</head>

<body>
    <div class="payslip-container">
        <!-- Header Section -->
        <div class="header">
            <div class="logo-section">
                <div class="logo-placeholder">
                    <img src="{{ asset('storage/uploads/front/' . getFrontData()->logo) }}" alt=""
                        class="logo-light" style="height:80px; width: auto; max-width: 200px;" />
                </div>
            </div>
            <div class="payslip-title">
                <h1>Payslip</h1>
                <div class="period">Pay Period: {{ $payrollRecord->payrollPeriod->name ?? 'N/A' }}</div>
            </div>
        </div>

        <!-- Company and Employee Details -->
        <div class="details-section">
            <div class="details-row">
                <div class="company-details">
                    <div class="details-title">Company Details</div>
                    <div class="detail-item">
                        <span class="detail-label">Company:</span>
                        <span class="detail-value">{{ config('app.name', 'STAWIHR') }}</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Address:</span>
                        <span class="detail-value">P.O. Box 1234-00100, Nairobi, Kenya</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Tel:</span>
                        <span class="detail-value">_________________</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Email:</span>
                        <span class="detail-value">info@stawihr.org</span>
                    </div>
                </div>
                <div class="employee-details">
                    <div class="details-title">Employee Details</div>
                    <div class="detail-item">
                        <span class="detail-label">Name:</span>
                        <span class="detail-value">{{ $payrollRecord->employeePayroll->employee->first_name ?? 'N/A' }}
                            {{ $payrollRecord->employeePayroll->employee->last_name ?? '' }}</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Payroll No:</span>
                        <span class="detail-value">{{ $payrollRecord->employeePayroll->payroll_number ?? 'N/A' }}</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Department:</span>
                        <span
                            class="detail-value">{{ $payrollRecord->employeePayroll->employee->department->department_name ?? 'N/A' }}</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Position:</span>
                        <span
                            class="detail-value">{{ $payrollRecord->employeePayroll->employee->designation->designation_name ?? 'N/A' }}</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">KRA PIN:</span>
                        <span class="detail-value">{{ $payrollRecord->employeePayroll->kra_pin ?? 'N/A' }}</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">NSSF No:</span>
                        <span class="detail-value">{{ $payrollRecord->employeePayroll->nssf_number ?? 'N/A' }}</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">SHIF No:</span>
                        <span class="detail-value">{{ $payrollRecord->employeePayroll->shif_number ?? 'N/A' }}</span>
                    </div>

                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <div class="columns-container">
                <!-- Earnings Column -->
                <div class="earnings-column">
                    <div class="column-title">EARNINGS</div>
                    <table class="items-table">
                        <thead>
                            <tr>
                                <th>Description</th>
                                <th class="amount">Amount (KES)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Basic Income</td>
                                <td class="amount currency">{{ number_format($payrollRecord->basic_salary, 2) }}</td>
                            </tr>

                            <!-- Overtime Total Entry -->
                            @php
                                $totalOvertime = 0;
                                $allowanceDetails = $payrollRecord->getAllowanceDetails();
                                $overtimeEntries = $allowanceDetails->filter(function ($item) {
                                    return str_contains(strtolower($item->name), 'overtime') ||
                                        str_contains(strtolower($item->code), 'overtime');
                                });
                                $totalOvertime = $overtimeEntries->sum('amount');
                                $regularAllowances = $allowanceDetails->filter(function ($item) {
                                    return !str_contains(strtolower($item->name), 'overtime') &&
                                        !str_contains(strtolower($item->code), 'overtime');
                                });
                            @endphp

                            @if ($totalOvertime > 0)
                                <tr>
                                    <td>Overtime</td>
                                    <td class="amount currency">{{ number_format($totalOvertime, 2) }}</td>
                                </tr>
                            @endif

                            <!-- Regular Allowances (excluding overtime) -->
                            @foreach ($regularAllowances as $allowance)
                                @if ($allowance->name == 'Basic Income')
                                    @continue
                                @endif
                                <tr>
                                    <td>{{ $allowance->name }}</td>
                                    <td class="amount currency">{{ number_format($allowance->amount, 2) }}</td>
                                </tr>
                            @endforeach

                            <tr class="total-row">
                                <td><strong>Total Earnings</strong></td>
                                <td class="amount currency">
                                    <strong>{{ number_format($payrollRecord->gross_salary, 2) }}</strong>
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <!-- Company Contributions -->
                    <div class="contributions-section">
                        <div class="contributions-title">COMPANY CONTRIBUTIONS</div>
                        <table class="items-table">
                            <thead>
                                <tr>
                                    <th>Description</th>
                                    <th class="amount">Amount (KES)</th>
                                </tr>
                            </thead>
                            <tbody>

                                <tr>
                                    <td>NSSF Employer</td>
                                    <td class="amount currency">
                                        {{ number_format($payrollRecord->nssf_contribution, 2) }}</td>
                                </tr>

                                <tr>
                                    <td>Housing Levy</td>
                                    <td class="amount currency">{{ number_format($payrollRecord->housing_levy, 2) }}
                                    </td>
                                </tr>


                                @if ($payrollRecord->pension_contribution > 0)
                                    <tr>
                                        <td>Pension Employer</td>
                                        <td class="amount currency">
                                            {{ number_format($payrollRecord->pension_contribution, 2) }}</td>
                                    </tr>
                                @endif
                                <tr class="total-row">
                                    <td><strong>Total Contributions</strong></td>
                                    <td class="amount currency"><strong>
                                            @php
                                                echo number_format(
                                                    ($payrollRecord->industrial_training_levy ?? 0) +
                                                        ($payrollRecord->nssf_tier1_company_contribution ?? 0) +
                                                        ($payrollRecord->nssf_tier2_company_contribution ?? 0) +
                                                        ($payrollRecord->housing_levy_company_contribution ?? 0) +
                                                        ($payrollRecord->employer_pension_contribution ?? 0) +
                                                        ($payrollRecord->shif_company_contribution ?? 0),
                                                    2,
                                                );
                                            @endphp
                                        </strong></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Deductions Column -->
                <div class="deductions-column">
                    <div class="column-title">DEDUCTIONS</div>
                    <table class="items-table">
                        <thead>
                            <tr>
                                <th>Description</th>
                                <th class="amount">Amount (KES)</th>
                            </tr>
                        </thead>
                        <tbody>

                            <tr>
                                <td>PAYE Tax</td>
                                <td class="amount currency">{{ number_format($payrollRecord->paye_tax, 2) }}</td>
                            </tr>

                            <tr>
                                <td>NSSF Employee</td>
                                <td class="amount currency">{{ number_format($payrollRecord->nssf_contribution, 2) }}
                                </td>
                            </tr>

                            <tr>
                                <td>SHIF Employee</td>
                                <td class="amount currency">{{ number_format($payrollRecord->shif_contribution, 2) }}
                                </td>
                            </tr>

                            <tr>
                                <td>Housing Levy</td>
                                <td class="amount currency">{{ number_format($payrollRecord->housing_levy, 2) }}</td>
                            </tr>

                            @if ($payrollRecord->pension_contribution > 0)
                                <tr>
                                    <td>Pension Employee</td>
                                    <td class="amount currency">
                                        {{ number_format($payrollRecord->pension_contribution, 2) }}</td>
                                </tr>
                            @endif

                            @foreach ($payrollRecord->getDeductionDetails() as $deduction)
                                <tr>
                                    <td>{{ $deduction->name }}</td>
                                    <td class="amount currency">{{ number_format($deduction->amount, 2) }}</td>
                                </tr>
                            @endforeach
                            <tr class="total-row">
                                <td><strong>Total Deductions</strong></td>
                                <td class="amount currency">
                                    <strong>{{ number_format($payrollRecord->total_deductions, 2) }}</strong>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Summary Section -->
        <div class="summary-section">
            <div class="summary-row">
                <span>Gross Pay:</span>
                <span class="currency">KES {{ number_format($payrollRecord->gross_salary, 2) }}</span>
            </div>
            <div class="summary-row">
                <span>Total Deductions:</span>
                <span class="currency">KES {{ number_format($payrollRecord->total_deductions, 2) }}</span>
            </div>
            @if ($payrollRecord->loan_deductions > 0)
                <div class="summary-row">
                    <span>Loan Deductions:</span>
                    <span class="currency">KES {{ number_format($payrollRecord->loan_deductions, 2) }}</span>
                </div>
            @endif
            <div class="summary-row net-pay">
                <span>Net Pay:</span>
                <span class="currency">KES {{ number_format($payrollRecord->net_salary, 2) }}</span>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p><strong>Payment Method:</strong>
                {{ ucfirst(str_replace('_', ' ', $payrollRecord->payment_method ?? 'Bank Transfer')) }}</p>
            @if ($payrollRecord->payment_reference)
                <p><strong>Payment Reference:</strong> {{ $payrollRecord->payment_reference }}</p>
            @endif
            @if ($payrollRecord->payment_date)
                <p><strong>Payment Date:</strong> {{ $payrollRecord->payment_date->format('d/m/Y') }}</p>
            @endif

            <div style="margin-top: 10px; border-top: 1px solid #ddd; padding-top: 5px;">
                <p><strong>Important Notes:</strong></p>
                <ul style="margin: 3px 0; padding-left: 15px; list-style-position: inside;">
                    <li>This payslip is computer generated and does not require a signature.</li>
                    <li>Please retain this payslip for your records and tax purposes.</li>
                    <li>For any queries regarding your salary, please contact the HR Department.</li>
                    <li>All statutory deductions are remitted to the respective government agencies as per Kenyan law.
                    </li>
                </ul>
            </div>

            <div style="text-align: center; margin-top: 8px; color: #666;">
                Generated on {{ now()->format('d/m/Y H:i:s') }} |
                STAWIHR |
                Confidential Document | Powerd by StawiHR
            </div>
        </div>
    </div>

    <script>
        // Auto print when page loads
        window.onload = function() {
            // Uncomment the line below to auto-print
            window.print();
        };
    </script>
</body>

</html>
