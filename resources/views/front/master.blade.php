@php
    $front_setting = getFrontData();
    $logoUrl = $front_setting?->logo
        ? asset('storage/uploads/front/' . $front_setting->logo)
        : asset('admin_assets/img/logo.png');
    $companyName = $front_setting?->company_title ?: env('APP_NAME', 'StawiHR');
    $footerText = $front_setting?->footer_text ?: $companyName;
    $isAuthenticated = Auth::check();
    $loginUrl = url('/login');
    $dashboardUrl = url('dashboard');
@endphp
<!DOCTYPE html>
<html lang="en" class="{{ request()->is('/') ? 'landing-home-html' : '' }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', $companyName)</title>
    @yield('meta')

    <link rel="shortcut icon" href="{{ $logoUrl }}">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="{{ url('front-assets/css/bootstrap.min.css') }}" type="text/css">
    <link rel="stylesheet" type="text/css" href="{{ url('front-assets/css/materialdesignicons.min.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ url('front-assets/css/fontawesome.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ url('front-assets/css/selectize.css') }}" />
    <link rel="stylesheet" href="{{ url('front-assets/css/owl.carousel.css') }}" />
    <link rel="stylesheet" href="{{ url('front-assets/css/owl.theme.css') }}" />
    <link rel="stylesheet" href="{{ url('front-assets/css/owl.transitions.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ url('front-assets/css/style.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ url('front-assets/css/landing-modern.css') }}" />

    <meta name="robots" content="noindex, nofollow, noarchive, nosnippet">
    <meta name="googlebot" content="noindex, nofollow, noarchive, nosnippet">

    @stack('styles')
</head>

<body class="front-modern{{ request()->is('/') ? ' landing-home' : '' }}">
    <div id="preloader">
        <div id="status">
            <div class="spinner">
                <div class="double-bounce1"></div>
                <div class="double-bounce2"></div>
            </div>
        </div>
    </div>

    <header class="front-header">
        <div class="front-header-inner">
            <a href="{{ url('/') }}" class="front-brand">
                @if($front_setting?->logo)
                    <img src="{{ $logoUrl }}" alt="{{ $companyName }}">
                @else
                    <span class="front-brand-text">{{ $companyName }}</span>
                @endif
            </a>

            <div class="front-header-actions">
                @if (!Route::is('job.details') && !Route::is('job.internal_details'))
                    @if ($isAuthenticated)
                        <a href="{{ $dashboardUrl }}" class="front-btn front-btn-primary">
                            <i class="bi bi-speedometer2"></i> Dashboard
                        </a>
                    @else
                        <a href="{{ $loginUrl }}" class="front-btn front-btn-primary">
                            <i class="bi bi-box-arrow-in-right"></i> Sign in
                        </a>
                    @endif
                @else
                    <a href="{{ url('/') }}" class="front-btn front-btn-outline">
                        <i class="bi bi-house"></i> Home
                    </a>
                    @if ($isAuthenticated)
                        <a href="{{ $dashboardUrl }}" class="front-btn front-btn-primary">Dashboard</a>
                    @else
                        <a href="{{ $loginUrl }}" class="front-btn front-btn-primary">Sign in</a>
                    @endif
                @endif
            </div>
        </div>
    </header>

    <main class="{{ request()->is('/') ? 'landing-main' : 'front-page-content' }}">
        @yield('content')
    </main>

    @if(!request()->is('/'))
    <footer class="front-footer">
        <div class="front-footer-inner">
            <div>
                <div class="front-footer-brand">{{ $footerText }}</div>
                @if($front_setting?->contact_email || $front_setting?->contact_phone)
                    <div class="front-footer-meta mt-1">
                        @if($front_setting?->contact_email)
                            {{ $front_setting->contact_email }}
                        @endif
                        @if($front_setting?->contact_email && $front_setting?->contact_phone)
                            &middot;
                        @endif
                        @if($front_setting?->contact_phone)
                            {{ $front_setting->contact_phone }}
                        @endif
                    </div>
                @endif
            </div>
            <div class="front-footer-links">
                <a href="{{ url('/') }}">Home</a>
                @if($isAuthenticated)
                    <a href="{{ $dashboardUrl }}">Dashboard</a>
                @else
                    <a href="{{ $loginUrl }}">Sign in</a>
                @endif
            </div>
        </div>
    </footer>

    <a href="#" class="back-to-top rounded text-center" id="back-to-top">
        <i class="mdi mdi-chevron-up d-block"></i>
    </a>
    @else
    <footer class="landing-home-footer">
        <span>&copy; {{ date('Y') }} Stawitech</span>
    </footer>
    @endif

    <script src="{{ asset('front-assets/js/jquery.min.js') }}"></script>
    <script src="{{ asset('front-assets/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('front-assets/js/jquery.easing.min.js') }}"></script>
    <script src="{{ asset('front-assets/js/plugins.js') }}"></script>
    <script src="{{ asset('front-assets/js/jquery.nice-select.min.js') }}"></script>
    <script src="{{ asset('front-assets/js/owl.carousel.min.js') }}"></script>
    <script src="{{ asset('front-assets/js/counter.int.js') }}"></script>
    <script src="{{ asset('front-assets/js/app.js') }}"></script>
    <script src="{{ asset('front-assets/js/home.js') }}"></script>

    @stack('javascript')
</body>

</html>
