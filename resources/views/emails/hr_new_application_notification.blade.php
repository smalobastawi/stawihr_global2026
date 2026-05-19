<!DOCTYPE html>
<html>

<head>
    <title>New Application: {{ $job->job_title }}</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            line-height: 1.6;
            color: #333333;
            margin: 0;
            padding: 0;
            background-color: #f5f5f5;
        }

        .email-container {
            max-width: 600px;
            margin: 20px auto;
            background: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .header {
            background-color: #3f51b5;
            color: white;
            padding: 20px;
            text-align: center;
        }

        .header h1 {
            margin: 0;
            font-size: 24px;
        }

        .content {
            padding: 25px;
        }

        p {
            margin: 0 0 15px 0;
        }

        .details {
            background-color: #f9f9f9;
            border-radius: 5px;
            padding: 15px;
            margin: 20px 0;
        }

        .detail-item {
            margin-bottom: 10px;
            display: flex;
        }

        .detail-label {
            font-weight: bold;
            min-width: 120px;
            display: inline-block;
        }

        .buttons {
            margin: 25px 0;
            text-align: center;
        }

        .button {
            display: inline-block;
            background-color: #3f51b5;
            color: white;
            text-decoration: none;
            padding: 10px 15px;
            border-radius: 4px;
            margin: 0 10px;
            font-weight: bold;
        }

        .button:hover {
            background-color: #303f9f;
        }

        .emoji {
            margin-right: 8px;
        }

        .footer {
            text-align: center;
            padding: 15px;
            background-color: #f0f0f0;
            font-size: 14px;
            color: #666666;
        }

        a {
            color: #3f51b5;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>
    <div class="email-container">
        <div class="header">
            <h1>New Job Application Received</h1>
        </div>

        <div class="content">
            <p>Hello HR Team,</p>

            <p>
                A new application has been submitted for the <strong>{{ $job->job_title }}</strong> position at
                {{ $job->location->location_name ?? 'Main Location' }}:
            </p>

            <div class="details">
                <div class="detail-item">
                    <span class="detail-label">Applicant:</span>
                    {{ $applicant->applicant_name }}
                </div>
                <div class="detail-item">
                    <span class="detail-label">Email:</span>
                    <a href="mailto:{{ $applicant->applicant_email }}">{{ $applicant->applicant_email }}</a>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Phone:</span>
                    <a href="tel:{{ $applicant->phone }}">{{ $applicant->phone }}</a>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Applied On:</span>
                    {{ $application->application_date->format('F j, Y \a\t g:i a') }}
                </div>
                <div class="detail-item">
                    <span class="detail-label">Experience:</span>
                    {{ $applicant->years_of_experience }} years
                </div>
                <div class="detail-item">
                    <span class="detail-label">Qualification:</span>
                    {{ $applicant->highest_qualification }}
                </div>
                <div class="detail-item">
                    <span class="detail-label">Source:</span>
                    {{ ucfirst(str_replace('_', ' ', $applicant->application_source)) }}
                </div>
                <div class="detail-item">
                    <span class="detail-label">Resume:</span>
                    <a href="{{ route('download.CV', $applicant->job_applicant_id) }}" target="_blank">Download
                        Resume</a>
                </div>
                {{-- @if ($applicant->cover_letter)
                    <div class="detail-item">
                        <span class="detail-label">Cover Letter:</span>
                        <a href="#" onclick="alert('Cover letter available in application details')">View</a>
                    </div>
                @endif --}}
            </div>

            <p><strong>Cover Letter Summary:</strong></p>
            <div style="background-color: #f9f9f9; padding: 15px; border-left: 3px solid #3f51b5; margin-bottom: 20px;">
                {{ Str::limit($applicant->cover_letter, 200) ?: 'No cover letter provided.' }}
            </div>

            {{-- <div class="buttons">
                @if (isset($job->job_id))
                    <a href="{{ url('/hr/job-applications/' . $application->id) }}" class="button">
                        <span class="emoji">📋</span> View Application
                    </a>
                    <a href="{{ url('/jobs/' . $job->job_id) }}" class="button">
                        <span class="emoji">👀</span> View Job Posting
                    </a>
                @endif
            </div> --}}

            <p>Please review this application at your earliest convenience.</p>
        </div>

        <div class="footer">
            <p>Best regards,<br>{{ config('app.name') }} Recruitment Team</p>
            <p style="margin-top: 10px; font-size: 12px;">
                <em>This is an automated notification. Please do not reply directly to this email.</em>
            </p>
        </div>
    </div>
</body>

</html>
