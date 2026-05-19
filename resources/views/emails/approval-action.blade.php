<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Your submission has been {{ $action }}</title>
</head>
<body>
    <h1>Your submission has been {{ $action }}</h1>
    
    <p>Hello {{ $notifiable->name }},</p>
    
    <p>Your {{ $humanReadableModelType }} submission has been <strong>{{ $action }}</strong>.</p>
    
    @if($comments)
    <p><strong>Comments:</strong><br>
    {{ $comments }}</p>
    @endif
    
    <p>
        <a href="{{ $actionUrl }}" style="display: inline-block; padding: 10px 20px; background-color: #007bff; color: white; text-decoration: none; border-radius: 5px;">
            View Submission Status
        </a>
    </p>
    
    <p>Thanks,<br>
    {{ config('app.name') }}</p>
</body>
</html>