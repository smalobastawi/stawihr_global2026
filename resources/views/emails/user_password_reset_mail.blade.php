<!DOCTYPE html>
<html>

<head>
    <title></title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
</head>

<body style="background-color: #f4f4f4;">
<div class="container-fluid">
    <p>A new password reset link has been generated for you.</p>
    <p>Kindly clik the link below to set a new password</p>

    <p style="font-size: 20px; font-family: Helvetica, Arial, sans-serif;  padding: 15px 25px; border-radius: 2px; border: 1px solid #FFA73B; display: inline-block;">
        Username: {{$content['username']}}
        <br>
        Reset Link:<a href="{{ $content['url']}}">Reset Password</a>
    </p>
</div>


<!-- HIDDEN PREHEADER TEXT -->
</body>

</html>