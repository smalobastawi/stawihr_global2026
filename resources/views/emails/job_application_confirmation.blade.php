<!DOCTYPE html>
<html>

<head>
    <title>Application Received: {{ $job->job_title }}</title>
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
            width: 120px;
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
            background-color: #3490dc;
            /* Primary blue */
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

        .status-badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 20px;
            font-weight: bold;
            background-color: #e3f2fd;
            color: #1976d2;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>Application Received: {{ $job->job_title }}</h1>
    </div>

    <div class="content">
        <p>Dear applicant,</p>

        <p>
            Thank you for taking the time to submit your application for the <strong>{{ $job->job_title }}</strong>. We
            are glad that you are interested in a job opportunity at STAWIHR and we're here to help you find your perfect
            fit.
        </p>

        <p>
            We are currently reviewing your application. If your profile is a good fit for this position, we will
            contact
            you regarding the next steps. Please note that due to the volume of applications we receive, only
            shortlisted candidates will be contacted.
        </p>

        <p>
            If you do not hear from us within 3 weeks, please assume that your interests could not be met on this
            occasion. Keep visiting our website for other opportunities.
        </p>
    </div>

    <div class="footer">
        <p>
            Kind regards,<br>
            {{ $job->createdBy->name ?? config('app.name') }} Hiring Team
        </p>
        {{-- <p><small>{{ $job->location->address ?? '' }}</small></p> --}}
    </div>
</body>

</html>
