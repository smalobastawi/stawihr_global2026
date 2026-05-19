<!DOCTYPE html>
<html>
<head>
    <title>Supervisor Approved Leave</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body>
    <h1>Supervisor Approved Leave!</h1>
    
    <p>
        The supervisor has approved {{$content['staff_first_name']}} {{$content['staff_last_name']}}'s 
        <p>
            <strong>From Date:</strong> {{$content['leave_from_date']}}<br>
            <strong>To Date:</strong> {{$content['leave_to_date']}}<br>
            <strong>Number of Days:</strong> {{$content['no_of_days']}}
        </p>
       
    </p>
    
    <p>
        <a href="{{config('app.url')}}/pendingLeaveRequests">
            View Details
        </a>
    </p>
    
    <p>If the link doesn't work, copy and paste this URL in your browser:</p>
    <p>{{config('app.url')}}/pendingLeaveRequests</p>
</body>
</html>