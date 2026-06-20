<!DOCTYPE html>
<html lang="en">
@php
    $front_setting = getFrontData();
    $logoUrl = $front_setting->logo
        ? asset('storage/uploads/front/' . $front_setting->logo)
        : null;
    $heroImage = 'https://images.unsplash.com/photo-1497366216548-37526070297c?w=1280&h=900&fit=crop&q=80';
    $googleEnabled = (bool) env('GOOGLE_CLIENT_ID');
    $azureEnabled = (bool) env('AZURE_CLIENT_ID');
    $passwordLogin = env('PASSWORD_LOGIN');
@endphp
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow, noarchive, nosnippet">
    <meta name="googlebot" content="noindex, nofollow, noarchive, nosnippet">
    <title>Sign in — {{ env('APP_NAME') }}</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @if($logoUrl)
        <link rel="shortcut icon" href="{{ $logoUrl }}" type="image/x-icon">
    @endif

    <style>
        :root {
            --primary: #0d6efd;
            --primary-dark: #0a4fc7;
        }

        * {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }

        html {
            height: 100%;
            overflow: hidden;
        }

        body {
            margin: 0;
            height: 100%;
            overflow: hidden;
            background: #0f0f1a;
        }

        .auth-shell {
            height: 100dvh;
            max-height: 100dvh;
            overflow: hidden;
            display: flex;
            align-items: stretch;
        }

        .auth-hero {
            flex: 1;
            min-height: 0;
            position: relative;
            display: none;
            align-items: center;
            background-size: cover;
            background-position: center;
            padding: 2rem;
            overflow: hidden;
        }

        @media (min-width: 992px) {
            .auth-hero {
                display: flex;
            }
        }

        .auth-hero::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(15, 15, 26, 0.78) 0%, rgba(13, 110, 253, 0.48) 100%);
        }

        .auth-hero-content {
            position: relative;
            z-index: 1;
            max-width: 520px;
            color: #fff;
        }

        .auth-hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            font-size: 0.75rem;
            font-weight: 600;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            opacity: 0.85;
            margin-bottom: 1.25rem;
        }

        .auth-hero-content h1 {
            font-size: clamp(1.5rem, 2.5vw, 2.25rem);
            font-weight: 800;
            letter-spacing: -0.02em;
            line-height: 1.15;
            margin-bottom: 0.75rem;
        }

        .auth-hero-content .lead {
            font-size: 0.95rem;
            opacity: 0.92;
            max-width: 420px;
            margin-bottom: 1.25rem;
        }

        .auth-hero-features li {
            display: flex;
            align-items: flex-start;
            gap: 0.65rem;
            margin-bottom: 0.5rem;
            font-size: 0.875rem;
            opacity: 0.95;
        }

        .auth-hero-features {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .auth-hero-features i {
            color: #0dcaf0;
            font-size: 1.1rem;
            margin-top: 0.1rem;
        }

        .auth-panel {
            flex: 1;
            min-height: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0.75rem;
            background: linear-gradient(160deg, #0f0f1a 0%, #1a1a2e 100%);
            overflow: hidden;
            overscroll-behavior: contain;
        }

        @media (min-width: 992px) {
            .auth-panel {
                flex: 0 0 400px;
                max-width: 400px;
                background: transparent;
                padding: 1rem;
            }
        }

        .auth-card {
            width: 100%;
            max-width: 380px;
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(14px);
            -webkit-backdrop-filter: blur(14px);
            border-radius: 16px;
            box-shadow: 0 24px 64px rgba(0, 0, 0, 0.28);
            border: 1px solid rgba(255, 255, 255, 0.65);
            padding: 1.15rem 1.25rem 1rem;
        }

        @media (max-width: 575.98px) {
            .auth-card {
                padding: 1rem 0.9rem 0.85rem;
                border-radius: 14px;
            }
        }

        .auth-logo {
            max-height: 32px;
            max-width: 140px;
            object-fit: contain;
            margin-bottom: 0.5rem;
        }

        .auth-card h2 {
            font-size: 1.05rem;
            font-weight: 700;
            margin-bottom: 0.15rem;
            color: #1a1a2e;
        }

        .auth-card .subtitle {
            color: #6c757d;
            font-size: 0.75rem;
            margin-bottom: 0.75rem;
            line-height: 1.35;
        }

        .auth-divider {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            color: #6c757d;
            font-size: 0.75rem;
            margin: 0.6rem 0;
        }

        .auth-divider::before,
        .auth-divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: #dee2e6;
        }

        .btn-social-google {
            background: #1a73e8;
            border: 1px solid #1a73e8;
            color: #fff;
            font-weight: 600;
            font-size: 0.8125rem;
            border-radius: 8px;
            padding: 0.45rem 0.85rem;
            box-shadow: 0 1px 3px rgba(26, 115, 232, 0.35);
            transition: background 0.2s, box-shadow 0.2s;
        }

        .btn-social-google:hover {
            background: #1765cc;
            border-color: #1765cc;
            color: #fff;
            box-shadow: 0 2px 6px rgba(26, 115, 232, 0.45);
        }

        .btn-social-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 1.35rem;
            height: 1.35rem;
            background: #fff;
            border-radius: 50%;
            color: #ea4335;
            font-size: 0.85rem;
            font-weight: 700;
        }

        .btn-social-microsoft {
            background: #0078d4;
            border: 1px solid #0078d4;
            color: #fff;
            font-weight: 600;
            font-size: 0.8125rem;
            border-radius: 8px;
            padding: 0.45rem 0.85rem;
            box-shadow: 0 1px 3px rgba(0, 120, 212, 0.35);
            transition: background 0.2s, box-shadow 0.2s;
        }

        .btn-social-microsoft:hover {
            background: #106ebe;
            border-color: #106ebe;
            color: #fff;
            box-shadow: 0 2px 6px rgba(0, 120, 212, 0.45);
        }

        .microsoft-logo {
            display: inline-grid;
            grid-template-columns: repeat(2, 5px);
            grid-template-rows: repeat(2, 5px);
            gap: 2px;
        }

        .microsoft-logo span:nth-child(1) { background: #f25022; }
        .microsoft-logo span:nth-child(2) { background: #7fba00; }
        .microsoft-logo span:nth-child(3) { background: #00a4ef; }
        .microsoft-logo span:nth-child(4) { background: #ffb900; }

        .form-label {
            font-weight: 500;
            font-size: 0.8125rem;
            color: #374151;
            margin-bottom: 0.25rem;
        }

        .form-control {
            border-radius: 8px;
            padding: 0.45rem 0.75rem;
            font-size: 0.875rem;
            border-color: #dee2e6;
        }

        .form-control:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.15);
        }

        .btn-sign-in {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            border: none;
            font-weight: 600;
            font-size: 0.875rem;
            border-radius: 8px;
            padding: 0.5rem 1rem;
            box-shadow: 0 2px 8px rgba(13, 110, 253, 0.3);
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .btn-sign-in:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 16px rgba(13, 110, 253, 0.4);
            background: linear-gradient(135deg, var(--primary-dark) 0%, #083a9e 100%);
        }

        .auth-footer-links {
            font-size: 0.8125rem;
        }

        .auth-field {
            margin-bottom: 0.55rem;
        }

        .auth-social-grid {
            display: grid;
            gap: 0.45rem;
        }

        .recaptcha-wrap {
            transform: scale(0.88);
            transform-origin: left top;
            height: 68px;
            margin-bottom: -4px;
            overflow: hidden;
        }

        .auth-card .alert {
            padding: 0.4rem 0.65rem;
            font-size: 0.75rem;
            margin-bottom: 0.55rem;
        }

        @media (max-height: 720px) {
            .auth-panel {
                align-items: flex-start;
                padding-top: 0.5rem;
                overflow-y: auto;
            }
        }

        .auth-footer-links a {
            color: var(--primary);
            text-decoration: none;
            font-weight: 500;
        }

        .auth-footer-links a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="auth-shell">
        <section class="auth-hero" style="background-image: url('{{ $heroImage }}');">
            <div class="auth-hero-content">
                <div class="auth-hero-badge">
                    <i class="bi bi-shield-check"></i> Secure HR portal
                </div>
                <h1>Manage HR &amp; payroll in one place</h1>
                <p class="lead">Sign in to access your dashboard, employee records, payroll, leave, and team tools.</p>
                <ul class="auth-hero-features">
                    <li><i class="bi bi-check-circle-fill"></i> Payroll processing &amp; payslips</li>
                    <li><i class="bi bi-check-circle-fill"></i> Employee records &amp; onboarding</li>
                    <li><i class="bi bi-check-circle-fill"></i> Leave &amp; attendance tracking</li>
                    <li><i class="bi bi-check-circle-fill"></i> HR reports &amp; compliance tools</li>
                    <li><i class="bi bi-check-circle-fill"></i> Performance, training &amp; appraisals</li>
                </ul>
            </div>
        </section>

        <section class="auth-panel">
            <div class="auth-card">
                @if($logoUrl)
                    <img src="{{ $logoUrl }}" alt="{{ env('APP_NAME') }}" class="auth-logo">
                @endif

                <h2>Sign in to your account</h2>
                <p class="subtitle">Welcome back.</p>

                @if ($errors->any())
                    <div class="alert alert-danger py-2 small">
                        @foreach ($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    </div>
                @endif

                @if (session()->has('error'))
                    <div class="alert alert-danger py-2 small">{{ session()->get('error') }}</div>
                @endif

                @if (session()->has('success'))
                    <div class="alert alert-success py-2 small">{{ session()->get('success') }}</div>
                @endif

                @if ($googleEnabled || $azureEnabled)
                    <div class="auth-social-grid">
                        @if ($googleEnabled)
                            <a href="{{ route('auth.google') }}" class="btn btn-social-google d-flex align-items-center justify-content-center gap-2">
                                <span class="btn-social-icon">G</span>
                                Login with Google
                            </a>
                        @endif
                        @if ($azureEnabled)
                            <a href="{{ route('azure.login') }}" class="btn btn-social-microsoft d-flex align-items-center justify-content-center gap-2">
                                <span class="microsoft-logo" aria-hidden="true">
                                    <span></span><span></span><span></span><span></span>
                                </span>
                                Login with Microsoft
                            </a>
                        @endif
                    </div>
                @endif

                @if ($passwordLogin && ($googleEnabled || $azureEnabled))
                    <div class="auth-divider">Or continue with username</div>
                @endif

                @if ($passwordLogin)
                    <form method="POST" action="/login" id="loginform">
                        @csrf
                        <div class="auth-field">
                            <label for="username" class="form-label">Username</label>
                            <input type="text" id="username" name="user_name" class="form-control"
                                   placeholder="Username" value="{{ old('user_name') }}" required autofocus>
                        </div>

                        <div class="auth-field">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" id="password" name="user_password" class="form-control"
                                   placeholder="Password" required>
                        </div>

                        @if(class_exists('NoCaptcha'))
                            <div class="recaptcha-wrap">
                                {!! NoCaptcha::display() !!}
                                {!! NoCaptcha::renderJs() !!}
                            </div>
                        @endif

                        <div class="d-flex justify-content-end auth-footer-links mb-2">
                            <a href="#" data-bs-toggle="modal" data-bs-target="#resetModal">Forgot password?</a>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-sign-in">Sign in</button>
                        </div>
                    </form>
                @endif

                <p class="text-center mb-0 mt-2 auth-footer-links">
                    <a href="{{ route('user.guide') }}" target="_blank" rel="noopener">
                        <i class="bi bi-book me-1"></i> User Guide
                    </a>
                </p>
            </div>
        </section>
    </div>

    <div class="modal fade" id="resetModal" tabindex="-1" aria-labelledby="resetModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border-radius: 16px;">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold" id="resetModalLabel">Recover password</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body pt-2">
                    <p class="text-muted small">Enter your email address and we will send reset instructions.</p>
                    <form method="POST" action="{{ route('reset_password_with_token') }}">
                        @csrf
                        <div class="mb-3">
                            <label for="resetEmail" class="form-label">Email address</label>
                            <input type="email" id="resetEmail" name="email" class="form-control"
                                   placeholder="you@company.com" required>
                        </div>
                        <button type="submit" class="btn btn-primary btn-sign-in w-100">Send reset link</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
