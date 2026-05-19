<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Approval Required: {{ $modelType }}</title>
</head>
<body>
    <h1>Approval Required: {{ $modelType }}</h1>
    
    <p>Hello {{ $notifiable->name }},</p>
    
    <p>A {{ $modelType }} requires your approval at {{ $currentStep }} stage.</p>
    
    @if(!empty($details))
    <p><strong>Details:</strong></p>
    <ul>
        @foreach($details as $key => $value)
        <li><strong>{{ $key }}:</strong> {{ $value }}</li>
        @endforeach
    </ul>
    @endif
    
    <p>
        <a href="{{ $actionUrl }}" style="display: inline-block; padding: 10px 20px; background-color: #007bff; color: white; text-decoration: none; border-radius: 5px;">
            Review {{ $modelType }}
        </a>
    </p>
    
    <p>Please complete your review within the required timeframe.</p>
    
    <p>Thanks,<br>
    {{ config('app.name') }}</p>
</body>
</html>