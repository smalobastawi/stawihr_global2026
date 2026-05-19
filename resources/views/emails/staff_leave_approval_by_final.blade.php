<!DOCTYPE html>
<html>
<head>
    <title>Leave Application Approved</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body>
    <h1>Leave application approved!</h1>
    
    <p>Your {{$content['leaveType']}} application of {{$content['no_of_days']}} days has been approved.</p>
    <strong>From Date:</strong> {{$content['leave_from_date']}}<br>
            <strong>To Date:</strong> {{$content['leave_to_date']}}<br>
            <strong>Number of Days:</strong> {{$content['no_of_days']}}
    <p>Kindly handover to your supervisor before proceeding on leave.</p>
    
    <p>Enjoy!</p>
    
    <p>Cheers</p>
</body>
</html>