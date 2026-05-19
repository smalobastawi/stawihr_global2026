<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Payslip - {{ $period->name }}</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 20px;
            background-color: #f4f4f4;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .header {
            text-align: center;
            border-bottom: 2px solid #007bff;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }

        .logo {
            margin-bottom: 15px;
        }

        .company-name {
            font-size: 24px;
            font-weight: bold;
            color: #007bff;
            margin-bottom: 10px;
        }

        .header-title {
            font-size: 20px;
            color: #333;
            margin: 0;
        }

        .greeting {
            font-size: 16px;
            margin-bottom: 20px;
        }

        .content {
            margin-bottom: 30px;
        }

        .payslip-details {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 5px;
            margin: 20px 0;
        }

        .detail-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            padding: 5px 0;
            border-bottom: 1px solid #e9ecef;
        }

        .detail-row:last-child {
            border-bottom: none;
            font-weight: bold;
            color: #007bff;
        }

        .custom-message {
            background-color: #e3f2fd;
            padding: 15px;
            border-left: 4px solid #007bff;
            margin: 20px 0;
            font-style: italic;
        }

        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e9ecef;
            text-align: center;
            color: #666;
            font-size: 14px;
        }

        .important-note {
            background-color: #fff3cd;
            color: #856404;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
            border: 1px solid #ffeaa7;
        }

        .attachment-info {
            background-color: #d4edda;
            color: #155724;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
            border: 1px solid #c3e6cb;
        }

        .button {
            display: inline-block;
            background-color: #007bff;
            color: white;
            padding: 12px 25px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            margin: 15px 0;
        }

        .contact-info {
            margin-top: 20px;
            font-size: 14px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <div class="logo">
                <!-- Company logo would go here -->
            </div>
            <div class="company-name">{{ config('app.name', 'STAWIHR') }}</div>
            <h1 class="header-title">Payslip Notification</h1>
        </div>

        <div class="content">
            <p class="greeting">Dear {{ $employee->fullName() }},</p>

            <p>We are pleased to inform you that your payslip for <strong>{{ $period->name }}</strong> is now available.
            </p>

            @if ($customMessage)
                <div class="custom-message">
                    <strong>Message from HR:</strong><br>
                    {{ $customMessage }}
                </div>
            @endif



            <div class="important-note">
                <strong>Important Notes:</strong>
                <ul style="margin: 10px 0; padding-left: 20px;">
                    <li>The attachment is encrypted with password, use your
                        {{ $employee->identity_type ? \App\Lib\Enumerations\IdentityType::toArray()[$employee->identity_type] : 'National ID' }}
                        or Passport number to open it.</li>
                    <li>Please keep this payslip for your personal records and tax filing purposes</li>
                    <li>All statutory deductions have been remitted to the respective government agencies</li>
                    <li>If you have any questions regarding your salary computation, please contact the HR Department
                    </li>
                    <li>This is an automated email - please do not reply directly to this message</li>
                </ul>
            </div>

            @if ($payrollRecord->payment_date)
                <p><strong>Payment Date:</strong> {{ $payrollRecord->payment_date->format('l, F j, Y') }}</p>
            @endif

            @if ($payrollRecord->payment_reference)
                <p><strong>Payment Reference:</strong> {{ $payrollRecord->payment_reference }}</p>
            @endif
        </div>

        <div class="footer">
            <div class="contact-info">

                <p>{{ config('app.name', 'STAWIHR') }} - HR Department</p>
            </div>

            <p style="margin-top: 20px;">
                <small>
                    This email was sent automatically from the StawiHR Payroll System.<br>
                    Generated on {{ now()->format('l, F j, Y \a\t g:i A') }}
                </small>
            </p>
        </div>
    </div>
</body>

</html>
