<!DOCTYPE html>
<html>
<head>
    <title>Leave Application Rejected</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body>
    <h1>Leave Application Rejected</h1>
    
    <p>
        Your {{$content['leaveType']}} application of {{$content['no_of_days']}} days has been rejected.
        <strong>From Date:</strong> {{$content['leave_from_date']}}<br>
            <strong>To Date:</strong> {{$content['leave_to_date']}}<br>
            <strong>Number of Days:</strong> {{$content['no_of_days']}}
    </p>
    
    <p><strong>Comments:</strong></p>
    <p>{{$content['remarks']}}</p>
    
    <p>Cheers,</p>
</body>
</html>