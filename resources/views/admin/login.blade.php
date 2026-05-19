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
    <meta name="robots" content="noindex, nofollow, noarchive, nosnippet">
    <meta name="googlebot" content="noindex, nofollow, noarchive, nosnippet">
    <link rel="shortcut icon" href="" type="image/x-icon" />
    <title>{{ env('APP_NAME') }}</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
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

        .padlock-icon {
            font-size: 4rem;
            /* Large icon size */
            color: #007bff;
            /* Icon color */
            margin-bottom: 20px;
            /* Space below the icon */
        }
    </style>
</head>

<body>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="login-container">
                    <div class="login-logo text-center">
                        <img src="{{ asset('storage/uploads/front/' . $front_setting->logo) }}" alt="{{ env('APP_NAME') }}">
                        {{-- <img src="C:\TRAININGS\shofco_dev\public\assets\img\logo\logo.jpg" alt="{{ env('APP_NAME') }}"> --}}
                    </div>

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if (session()->has('error'))
                        <div class="alert alert-danger">
                            {{ session()->get('error') }}
                        </div>
                    @endif

                    @if (session()->has('success'))
                        <div class="alert alert-success">
                            {{ session()->get('success') }}
                        </div>
                    @endif
                    <div class="text-center">
                        <div class="padlock-icon">
                            <i class="fas fa-lock"></i> <!-- Font Awesome padlock icon -->
                        </div>
                    </div>
                    @if (env('GOOGLE_CLIENT_ID') || env('AZURE_CLIENT_ID'))
                        <div class="text-center">
                            @if (env('GOOGLE_CLIENT_ID'))
                                <a href="{{ route('auth.google') }}" class="btn btn-danger btn-flat">
                                    <i class="fab fa-google"></i> Login with Google
                                </a>
                            @endif
                            <br>
                            <br>

                            @if (env('AZURE_CLIENT_ID'))
                                <a href="{{ route('azure.login') }}" class="btn btn-outline-primary btn-flat">
                                    <i class="fab fa-microsoft"></i> Login with Microsoft</a>
                            @endif
                        </div>
                    @endif

                    @if (env('PASSWORD_LOGIN'))
                        <hr>
                        <form method="POST" action="/login" class="form-horizontal new-lg-form" id="loginform">
@csrf
                        <div class="form-group">
                            <label for="username">Username:</label>
                            <input type="text" id="username" name="user_name" class="form-control"
                                placeholder="Enter Username" required>
                        </div>

                        <div class="form-group">
                            <label for="password">Password:</label>
                            <input type="password" id="password" name="user_password" class="form-control"
                                placeholder="Enter Password" required>
                        </div>

                        <div class="form-group">
                            {!! NoCaptcha::display() !!}
                            {!! NoCaptcha::renderJs() !!}
                        </div>


                        <button type="submit" class="btn btn-primary btn-block">Login</button>

                        <br>



                        <br><br>



                        </form>

                        <a href="#" data-bs-toggle="modal" data-bs-target="#resetModal">Forgot Password?</a>

                        <div class="modal fade" id="resetModal" tabindex="-1" aria-labelledby="resetModalLabel"
                            aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="resetModalLabel">Recover Password</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <form class="form-horizontal" id="" METHOD="post"
                                            action="{{ route('reset_password_with_token') }}">
                                            {{ csrf_field() }}
                                            <div class="form-group">
                                                <label for="resetEmail">Email Address:</label>
                                                <input type="email" id="resetEmail" name="email"
                                                    class="form-control" placeholder="Enter Email" required>
                                            </div>
                                            <button type="submit" class="btn btn-primary">Reset Password</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>
