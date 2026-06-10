<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payslip Preview - Payroll Calculator</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-size: 8pt; }
        body { font-family: Arial, sans-serif; background-color: #f5f5f5; color: #333; line-height: 1.3; }
        .payslip-container { max-width: 800px; margin: 10px auto; background: white; box-shadow: 0 0 5px rgba(0,0,0,0.1); border-radius: 3px; overflow: hidden; }
        .header { color: #000; padding: 10px 15px; border-bottom: 1px solid #ddd; text-align: center; }
        .company-name { font-weight: bold; }
        .payslip-title h1 { font-weight: bold; margin-bottom: 2px; }
        .preview-note { color: #666; font-style: italic; margin-top: 4px; }
        .details-section { padding: 15px; border-bottom: 1px solid #ddd; }
        .details-row { display: flex; gap: 20px; }
        .company-details, .employee-details { flex: 1; }
        .details-title { font-weight: bold; margin-bottom: 8px; padding-bottom: 4px; border-bottom: 1px solid #aaa; }
        .detail-item { display: flex; margin-bottom: 4px; }
        .detail-label { font-weight: 600; width: 110px; color: #555; }
        .main-content { padding: 15px; }
        .columns-container { display: flex; gap: 15px; margin-bottom: 15px; }
        .earnings-column, .deductions-column { flex: 1; }
        .column-title { background: #e0e0e0; padding: 6px 10px; font-weight: bold; text-align: center; border: 1px solid #ccc; }
        .items-table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        .items-table th, .items-table td { padding: 5px 6px; border: 1px solid #ddd; }
        .items-table th { background: #f0f0f0; text-align: left; }
        .amount { text-align: right; font-weight: 600; }
        .total-row { background: #e8e8e8 !important; font-weight: bold; }
        .summary-section { background: #f0f0f0; padding: 10px 15px; border-top: 1px solid #aaa; }
        .summary-row { display: flex; justify-content: space-between; margin-bottom: 5px; }
        .summary-row.net-pay { font-weight: bold; border-top: 1px solid #888; padding-top: 8px; margin-top: 8px; }
        .footer { background: #f8f8f8; padding: 10px 15px; text-align: center; color: #666; border-top: 1px solid #ddd; }
        @media print { body { background: white; } .payslip-container { box-shadow: none; margin: 0; } .no-print { display: none; } }
    </style>
</head>
<body>
    <div class="no-print" style="max-width: 800px; margin: 10px auto; text-align: right;">
        <button onclick="window.print()" style="padding: 8px 16px; cursor: pointer;">Print</button>
    </div>

    <div class="payslip-container">
        <div class="header">
            <div class="payslip-title">
                <h1>Payslip Preview</h1>
                <div class="company-name">{{ $result['country_name'] }} Payroll Calculator</div>
                <div class="preview-note">Sample payslip — not linked to any employee or payroll period</div>
            </div>
        </div>

        <div class="details-section">
            <div class="details-row">
                <div class="company-details">
                    <div class="details-title">Calculation Details</div>
                    <div class="detail-item">
                        <span class="detail-label">Jurisdiction:</span>
                        <span>{{ $result['country_name'] }}</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Currency:</span>
                        <span>{{ $result['currency'] }}</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Calculated:</span>
                        <span>{{ $result['calculated_at'] ?? now()->toDateTimeString() }}</span>
                    </div>
                </div>
                <div class="employee-details">
                    <div class="details-title">Employee Details</div>
                    <div class="detail-item">
                        <span class="detail-label">Name:</span>
                        <span>Sample Employee</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Payroll No:</span>
                        <span>PREVIEW</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Department:</span>
                        <span>N/A</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="main-content">
            <div class="columns-container">
                <div class="earnings-column">
                    <div class="column-title">EARNINGS</div>
                    <table class="items-table">
                        <thead>
                            <tr>
                                <th>Description</th>
                                <th class="amount">Amount ({{ $result['currency'] }})</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($result['earnings'] as $earning)
                                <tr>
                                    <td>{{ $earning['name'] }}</td>
                                    <td class="amount">{{ number_format($earning['amount'], 2) }}</td>
                                </tr>
                            @endforeach
                            <tr class="total-row">
                                <td>Total Earnings</td>
                                <td class="amount">{{ number_format($result['gross_salary'], 2) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="deductions-column">
                    <div class="column-title">DEDUCTIONS</div>
                    <table class="items-table">
                        <thead>
                            <tr>
                                <th>Description</th>
                                <th class="amount">Amount ({{ $result['currency'] }})</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($result['deductions'] as $deduction)
                                <tr>
                                    <td>{{ $deduction['name'] }}</td>
                                    <td class="amount">{{ number_format($deduction['amount'], 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="2" style="text-align: center;">No deductions</td>
                                </tr>
                            @endforelse
                            <tr class="total-row">
                                <td>Total Deductions</td>
                                <td class="amount">{{ number_format($result['total_deductions'], 2) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            @if (!empty($result['employer_contributions']))
                <table class="items-table">
                    <thead>
                        <tr>
                            <th colspan="2" style="text-align: center; background: #e0e0e0;">EMPLOYER CONTRIBUTIONS</th>
                        </tr>
                        <tr>
                            <th>Description</th>
                            <th class="amount">Amount ({{ $result['currency'] }})</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($result['employer_contributions'] as $contribution)
                            <tr>
                                <td>{{ $contribution['name'] }}</td>
                                <td class="amount">{{ number_format($contribution['amount'], 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>

        <div class="summary-section">
            <div class="summary-row">
                <span>Gross Pay:</span>
                <span>{{ $result['currency'] }} {{ number_format($result['gross_salary'], 2) }}</span>
            </div>
            <div class="summary-row">
                <span>Taxable Income:</span>
                <span>{{ $result['currency'] }} {{ number_format($result['taxable_income'], 2) }}</span>
            </div>
            <div class="summary-row">
                <span>Total Deductions:</span>
                <span>{{ $result['currency'] }} {{ number_format($result['total_deductions'], 2) }}</span>
            </div>
            <div class="summary-row net-pay">
                <span>Net Pay:</span>
                <span>{{ $result['currency'] }} {{ number_format($result['net_salary'], 2) }}</span>
            </div>
        </div>

        <div class="footer">
            Generated by Payroll Calculator. This preview is for estimation purposes only and is not stored in the system.
        </div>
    </div>
</body>
</html>
