<!DOCTYPE html>
<html lang="en">
@php
    $front_setting = getFrontData();
@endphp
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="shortcut icon" href="{{ asset('storage/uploads/front/'.$front_setting->logo) }}" type="image/x-icon"/>
    <title>{{env('APP_NAME')}}</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"> 
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <script src="{!! asset('admin_assets/plugins/bower_components/jquery/dist/jquery.min.js')!!}"></script>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons"
        rel="stylesheet">

    <script type="text/javascript">
        var base_url = "{{ url('/').'/' }}";
    </script>
    <style>
        body {
            background-color: #f8f9fa; 
            font-family: 'Open Sans', sans-serif; 
        }
        .login-container {
            max-width: 400px;
            margin: 0 auto;
            padding: 40px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            background-color: #fff;
        }
        .login-logo img {
            max-width: 150px; 
            margin-bottom: 20px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .btn-primary {
            background-color: #007bff; 
        }
        .btn-primary:hover {
            background-color: #0069d9;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="login-container">
                <div class="login-logo text-center">
                    <img src="{{ asset('storage/uploads/front/'.$front_setting->logo) }}" alt="{{env('APP_NAME')}}">
                </div>

                @if($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if(session()->has('error'))
                    <div class="alert alert-danger">
                        {{ session()->get('error') }}
                    </div>
                @endif

                @if(session()->has('success'))
                    <div class="alert alert-success">
                        {{ session()->get('success') }}
                    </div>
                @endif

                <form method="POST" action="/verify-otp" class="form-horizontal new-lg-form" id="verifyForm">
@csrf
                    <div class="form-group">
                        <input type="hidden" name="change_password" >
                        <label for="username">Verification code:</label>
                        <input type="text" id="verification_code" name="verification_code" class="form-control" placeholder="Enter verification code" required>
                    </div>

                    <button type="submit" class="btn btn-primary btn-block">Submit</button>
                    <button type="button" class="btn btn-warning btn-block" style="color:white" id="resend-otp">Resend OTP</button>

                </form>

                <a href="/login" >Back to login</a>

            
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
        $(document).ready(function(){

		$('#resend-otp').click(function (e) {
			e.preventDefault(); // Prevent default form submission (if inside a form)
			var $otpButton = $(this);
			$otpButton.prop('disabled', true).text('Sending...'); 
			$.ajax({
				url: `/resend-otp`,
				type: "POST",
				data: {
					
					_token: "{{ csrf_token() }}", 
				},
				success: function(response) {
					if (response.success) {
						alert('OTP sent successfully! Please check your email and SMS.');
					} else {

						alert('Error sending OTP.Please try again');
					}
				},
				error: function() {
					alert('There was an error sending the OTP.Please try again');
				},
				complete: function() {
					$otpButton.prop('disabled', false).text('Resend OTP'); 
				}
			});
		});
    });
</script>
</body>
</html>