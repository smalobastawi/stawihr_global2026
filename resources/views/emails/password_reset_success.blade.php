<!DOCTYPE html>
<html>

<body>
<div class="container-fluid">
<p>Hello, {{$content['username']}}</p>
<p>Your new password has been successfully set</p>
    <p>Username: {{$content['username']}}</p>
<p> <a href="{{url('/login')}}">Click here to login</a></p>
</div>
</body>
</html>