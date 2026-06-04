@php
    $front_setting = getFrontData();
@endphp
<!DOCTYPE html>
<html lang="en" class="no-js">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Home Page')</title>
    @yield('meta')

    <meta name="author" content="shakhawat" />
    <link rel="shortcut icon" href="{{ url('admin_assets/img/logo.png') }}">
    <!-- Bootstrap core CSS -->
    <link rel="stylesheet" href="{{ url('front-assets/css/bootstrap.min.css') }}" type="text/css">
    <!--Material Icon -->
    <link rel="stylesheet" type="text/css" href="{{ url('front-assets/css/materialdesignicons.min.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ url('front-assets/css/fontawesome.css') }}" />
    <!-- selectize css -->
    <link rel="stylesheet" type="text/css" href="{{ url('front-assets/css/selectize.css') }}" />
    <!--Slider-->
    <link rel="stylesheet" href="{{ url('front-assets/css/owl.carousel.css') }}" />
    <link rel="stylesheet" href="{{ url('front-assets/css/owl.theme.css') }}" />
    <link rel="stylesheet" href="{{ url('front-assets/css/owl.transitions.css') }}" />
    <meta name="robots" content="noindex, nofollow, noarchive, nosnippet">
    <meta name="googlebot" content="noindex, nofollow, noarchive, nosnippet">
    <!-- Custom  Css -->
    <link rel="stylesheet" type="text/css" href="{{ url('front-assets/css/style.css') }}" />

    <style>
        /* Hide the login button on larger screens */
        #login-button {
            display: none;
        }

        /* Show the login button on smaller screens */
        @media (max-width: 768px) {
            #login-button {
                display: inline-block;
            }
        }
    </style>

</head>

<body>
    <!-- Loader -->
    <div id="preloader">
        <div id="status">
            <div class="spinner">
                <div class="double-bounce1"></div>
                <div class="double-bounce2"></div>
            </div>
        </div>
    </div>
    <!-- Loader -->

    <!-- Navigation Bar-->
    <header id="topnav" class="defaultscroll scroll-active">

        <!-- Menu Start -->
        <div class="container">
            <!-- Logo container-->
            <div>
                <a href="{{ url('/') }}" class="logo">
                    <img src="{{ asset('storage/uploads/front/' . $front_setting->logo) }}" alt="" class="logo-light"
                        height="70" />
                </a>
            </div>
            <div class="buy-button">
                @if (Auth::check())
                    <a href="{{ url('dashboard') }}" class="btn btn-primary">
                        <i class="mdi mdi-dashboard"></i> Dashboard
                    </a>
                @else
                    <a href="{{ url('/login') }}" class="btn btn-primary">Login</a>
                @endif
            </div>
            <!--end login button-->
            <!-- End Logo container-->
            <div class="menu-extras">
                <div class="menu-item">
                    <!-- Mobile menu toggle-->
                    <a class="navbar-toggle">
                        <div class="lines">
                            <span></span>
                            <span></span>
                            <span></span>
                        </div>
                    </a>
                    <!-- End mobile menu toggle-->
                </div>
            </div>

            @if (!Route::is('job.details') && !Route::is('job.internal_details'))
                <div id="navigation">
                    <!-- Navigation Menu-->
                    <ul class="navigation-menu">

                        @if (Route::getCurrentRoute()->uri() == '/')
                           
                            
                            <li id="login-button" class="buy-button">
                                @if (Auth::check())
                                    <a href="{{ url('dashboard') }}"><i></i> Dashboard</a>
                                @else
                                    <a href="{{ url('/login') }}" id="login-button">Login</a>
                                @endif
                            </li>
                        @else
                          
                        @endif


                    </ul>
                    <!--end navigation menu-->
                </div>
                <!--end navigation-->
            @endif

        </div>
        <!--end container-->
        <!--end end-->
    </header>
    <!--end header-->
    <!-- Navbar End -->



    @yield('content')

    <footer class="footer footer-bar">
        <div class="container text-center">
            <div class="row justify-content-center">
                <div class="col-12">
                    <div class="">
                        <p class="mb-0">StawiHR</p>
                    </div>
                </div>
            </div>
        </div>
        <!--end container-->
    </footer>
    <!--end footer-->
    <!-- Footer End -->

    <!-- Back to top -->
    <a href="#" class="back-to-top rounded text-center" id="back-to-top">
        <i class="mdi mdi-chevron-up d-block"> </i>
    </a>
    <!-- Back to top -->

    <!-- javascript -->
    <script src="{{ asset('front-assets/js/jquery.min.js') }}"></script>
    <script src="{{ asset('front-assets/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('front-assets/js/jquery.easing.min.js') }}"></script>
    <script src="{{ asset('front-assets/js/plugins.js') }}"></script>

    <!-- selectize js -->
    <script src="{{ asset('front-assets/js/jquery.nice-select.min.js') }}j"></script>
    <script src="{{ asset('front-assets/js/jquery.nice-select.min.js') }}"></script>

    <script src="{{ asset('front-assets/js/owl.carousel.min.js') }}"></script>
    <script src="{{ asset('front-assets/js/counter.int.js') }}"></script>

    <script src="{{ asset('front-assets/js/app.js') }}"></script>
    <script src="{{ asset('front-assets/js/home.js') }}"></script>

    @stack('javascript')
</body>

</html>
