<!DOCTYPE html>
<html>
<head>
    <title>Leave Application Recalled</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body>
    <h1>Leave Application Recall</h1>
    
    <p>
        {{$content['staff_first_name']}} {{$content['staff_last_name']}} has recalled their leave
        {{ strtoupper($content['leaveType']) }}. Please review the details below:
    </p>
    
    <p>
        <strong>From Date:</strong> {{$content['leave_from_date']}}<br>
        <strong>To Date:</strong> {{$content['leave_to_date']}}<br>
        <strong>Number of Days:</strong> {{$content['no_of_days']}}
    </p>
    
    <p>
        <a href="{{ route('requestedApplication.viewDetails',$content['latest_leave']) }}">
            View Application Details
        </a>
    </p>
    
    <p>If the link above doesn't work, please copy and paste this URL into your browser:</p>
    <p>{{config('app.url')}}/requestedApplication/{{$content['latest_leave']}}/viewDetails</p>
</body>
</html>