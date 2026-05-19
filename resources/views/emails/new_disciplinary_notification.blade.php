<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Disciplinary Notification</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            margin: 0;
            padding: 0;
            color: #333;
        }
        .email-container {
            width: 100%;
            max-width: 600px;
            margin: 20px auto;
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 8px;
            overflow: hidden;
        }
        .email-header {
            background-color: #4CAF50;
            color: #fff;
            text-align: center;
            padding: 20px;
        }
        .email-body {
            padding: 20px;
        }
        .email-footer {
            text-align: center;
            font-size: 12px;
            color: #777;
            padding: 20px;
            background-color: #f1f1f1;
        }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            background-color: #4CAF50;
            color: #fff;
            text-decoration: none;
            border-radius: 4px;
            margin-top: 20px;
        }
        .btn:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="email-header">
            <h1>Disciplinary Notification</h1>
        </div>
        <div class="email-body">
            <p>Dear {{ $user->first_name }},</p>
            <p>We regret to inform you that a disciplinary issue has been raised regarding your conduct. Please see the details below:</p>
            <ul>
                <li><strong>Issue:</strong> {{ $issue }}</li>
                <li><strong>Date:</strong> {{ $date }}</li>
                <li><strong>Details:</strong> {{ $details }}</li>
            </ul>
            <p>We kindly request you to respond to this matter by <strong>{{ $response_date }}</strong>.</p>
            <a href="{{ $response_link }}" class="btn">Respond Now</a>
            <p>If you have any questions, please do not hesitate to contact HR.</p>
            <p>Thank you for your attention to this matter.</p>
            <p>Best regards,</p>
            <p><strong>{{ helper_companyInfo()->legal_Name }}</strong></p>
        </div>
        <div class="email-footer">
            <p>&copy; {{ date('Y') }} {{ helper_companyInfo()->legal_Name }}. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
