<!DOCTYPE html>
<html>
<head>
    <title>Training Confirmation: {{ $training->subject }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            text-align: center;
            padding-bottom: 20px;
            border-bottom: 1px solid #eee;
            margin-bottom: 20px;
        }
        h1 {
            color: #2c3e50;
            font-size: 24px;
            margin-bottom: 10px;
        }
        .content {
            padding: 15px 0;
        }
        .details {
            background-color: #f9f9f9;
            padding: 15px;
            border-radius: 5px;
            margin: 15px 0;
        }
        .detail-item {
            margin-bottom: 8px;
        }
        .detail-label {
            font-weight: bold;
            display: inline-block;
            width: 80px;
        }
        .buttons {
            margin: 25px 0;
            text-align: center;
        }
        .button {
            display: inline-block;
            padding: 12px 25px;
            margin: 0 10px 10px 0;
            border-radius: 4px;
            text-decoration: none;
            font-weight: bold;
            color: white !important;
            text-align: center;
        }
        .button-google {
            background-color: #34a853; /* Google green */
        }
        .button-outlook {
            background-color: #0078d4; /* Outlook blue */
        }
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #eee;
            text-align: center;
            font-size: 14px;
            color: #777;
        }
        .emoji {
            font-size: 18px;
            margin-right: 8px;
            vertical-align: middle;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Training Confirmed: {{ $training->subject }}</h1>
    </div>
    
    <div class="content">
        <p>Dear {{ $employee->first_name }},</p>
        
        <p>Your attendance for the following training has been confirmed:</p>
        
        <div class="details">
            <div class="detail-item">
                <span class="detail-label">Date:</span>
                {{ $training->start_date->format('l, F j, Y') }}
            </div>
            <div class="detail-item">
                <span class="detail-label">Time:</span>
                {{ $training->start_date->format('g:i a') }} - {{ $training->end_date->format('g:i a') }}
            </div>
            <div class="detail-item">
                <span class="detail-label">Location:</span>
                {{ $training->location ?? 'Online' }}
            </div>
        </div>
        
        <p>Add this event to your calendar:</p>
        
        <div class="buttons">
            <a href="{{ $googleCalendarUrl }}" class="button button-google">
                <span class="emoji">📅</span> Add to Google Calendar
            </a>
            <a href="{{ $outlookCalendarUrl }}" class="button button-outlook">
                <span class="emoji">📅</span> Add to Outlook Calendar
            </a>
        </div>
        
        <p>We look forward to seeing you at the training!</p>
    </div>
    
    <div class="footer">
        <p>Thanks,<br>{{ config('app.name') }}</p>
    </div>
</body>
</html> 