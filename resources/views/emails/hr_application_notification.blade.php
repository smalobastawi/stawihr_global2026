<!DOCTYPE html>
<html>

<head>
    <title>New Application: {{ $job->job_title }}</title>
    <!-- [Keep all the same CSS styles from your template] -->
    <style>
        /* Paste all your existing CSS styles here */
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            /* etc */
        }

        /* ... rest of your styles ... */
    </style>
</head>

<body>
    <div class="header">
        <h1>New Job Application Received</h1>
    </div>

    <div class="content">
        <p>Hello HR Team,</p>

        <p>A new application has been submitted for the <strong>{{ $job->job_title }}</strong> position:</p>

        <div class="details">
            <div class="detail-item">
                <span class="detail-label">Applicant:</span>
                {{ $applicant->applicant_name }}
            </div>
            <div class="detail-item">
                <span class="detail-label">Email:</span>
                {{ $applicant->applicant_email }}
            </div>
            <div class="detail-item">
                <span class="detail-label">Phone:</span>
                {{ $applicant->phone }}
            </div>
            <div class="detail-item">
                <span class="detail-label">Applied On:</span>
                {{ $application->created_at->format('F j, Y \a\t g:i a') }}
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
                <span class="detail-label">Resume:</span>
                <a href="{{ asset('storage/'.$applicant->attached_resume) }}" target="_blank">Download</a>
            </div>
        </div>

        {{-- <div class="buttons">
            <a href="{{ route('hr.applications.show', $application->id) }}" class="button">
                <span class="emoji">📋</span> View Full Application
            </a>
            <a href="{{ route('job.details', ['id' => $job->job_id, 'slug' => Str::slug($job->job_title)]) }}"
                class="button">
                <span class="emoji">👀</span> View Job Posting
            </a>
        </div> --}}

        <p>Please review this application at your earliest convenience.</p>
    </div>

    <div class="footer">
        <p>Best regards,<br>{{ config('app.name') }} Recruitment System</p>
    </div>
</body>

</html>