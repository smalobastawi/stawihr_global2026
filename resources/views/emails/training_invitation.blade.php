<!DOCTYPE html>
<html>
<head>
    <title>Training Invitation</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .button {
            display: inline-block;
            padding: 10px 20px;
            margin: 10px 5px;
            border-radius: 4px;
            text-decoration: none;
            font-weight: bold;
        }
        .button-success {
            background-color: #28a745;
            color: white;
        }
        .button-error {
            background-color: #dc3545;
            color: white;
        }
        .disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border: 1px solid transparent;
            border-radius: 4px;
        }
        .alert-info {
            color: #31708f;
            background-color: #d9edf7;
            border-color: #bce8f1;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Training Invitation: {{ $training->subject }}</h1>
        
        <p>Dear {{ $employee->first_name }},</p>

        @if($hasResponded)
            <div class="alert alert-info">
                You've already responded to this invitation.
            </div>
        @elseif($isExpired) 
            <div class="alert alert-info">
                This invitation expired on {{ $training->start_date->format('M j, Y') }}
            </div>
        @else
            <p>You have been invited to attend the following training:</p>
        
            <p><strong>Subject:</strong> {{ $training->subject }}</p>
            <p><strong>Description:</strong> {{ $training->description }}</p>
            @if($training->start_date && $training->end_date)
                <p><strong>Dates:</strong> 
                    {{ \Carbon\Carbon::parse($training->start_date)->format('M j, Y') }} 
                    to 
                    {{ \Carbon\Carbon::parse($training->end_date)->format('M j, Y') }}
                </p>
            @endif

            <p>Please respond to this invitation:</p>

            <div>
                <a href="{{ $acceptUrl }}" class="button button-success @if($hasResponded) disabled @endif">
                    ✅ Accept Invitation
                </a>
                <a href="{{ $declineUrl }}" class="button button-error @if($hasResponded) disabled @endif">
                    ❌ Decline Invitation
                </a>
            </div>
            @if($training->start_date)
                <p>
                    <em>
                        Please respond before {{ \Carbon\Carbon::parse($training->start_date)->format('M j, Y') }}
                    </em>
                </p>
            @endif
        @endif
        <p>Thanks,<br>{{ config('app.name') }}</p>
    </div>
</body>
</html>


