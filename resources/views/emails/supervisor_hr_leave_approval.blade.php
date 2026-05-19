<!DOCTYPE html>
<html>
<head>
    <title>Leave Application Approved</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body>
    <h1>Leave application approved</h1>
    
    <p>
        {{$content['staff_first_name']}} {{$content['staff_last_name']}}'s {{ $formattedLeaveType }} 
        application of {{$content['no_of_days']}} days is now approved. <br>
        <strong>From Date:</strong> {{$content['leave_from_date']}}<br>
            <strong>To Date:</strong> {{$content['leave_to_date']}}<br>
           
    </p>
    
    <p>Kindly make the necessary arrangement for the staff to proceed on leave.</p>
</body>
</html>