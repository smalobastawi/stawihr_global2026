<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>Account Suspended — {{ config('app.name') }}</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
        }
        .suspended-card {
            max-width: 560px;
            width: 100%;
            border: none;
            border-radius: 12px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.08);
        }
        .suspended-icon {
            font-size: 3.5rem;
            color: #dc3545;
        }
        .reason-box {
            background: #fff5f5;
            border-left: 4px solid #dc3545;
            border-radius: 0 8px 8px 0;
        }
        .contact-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 0;
            border-bottom: 1px solid #eee;
        }
        .contact-item:last-child {
            border-bottom: none;
        }
    </style>
</head>
<body>
    <div class="card suspended-card">
        <div class="card-body p-4 p-md-5 text-center">
            <div class="suspended-icon mb-3">
                <i class="fas fa-ban"></i>
            </div>
            <h1 class="h3 mb-2">Account Suspended</h1>
            <p class="text-muted mb-4">
                Your organization's StawiHR subscription has been suspended. You cannot access the system until the subscription is reactivated.
            </p>

            @if($status->reason)
            <div class="reason-box text-start p-3 mb-4">
                <strong class="d-block mb-2">Reason for suspension</strong>
                <p class="mb-0 text-secondary">{{ $status->reason }}</p>
            </div>
            @endif

            <div class="text-start mb-4">
                <h2 class="h6 text-uppercase text-muted mb-3">Contact Support</h2>
                @if($status->support_email)
                <div class="contact-item">
                    <i class="fas fa-envelope text-primary"></i>
                    <a href="mailto:{{ $status->support_email }}">{{ $status->support_email }}</a>
                </div>
                @endif
                @if($status->support_phone)
                <div class="contact-item">
                    <i class="fas fa-phone text-primary"></i>
                    <a href="tel:{{ preg_replace('/\s+/', '', $status->support_phone) }}">{{ $status->support_phone }}</a>
                </div>
                @endif
                @if($portalSupportUrl)
                <div class="contact-item">
                    <i class="fas fa-life-ring text-primary"></i>
                    <a href="{{ $portalSupportUrl }}" target="_blank" rel="noopener">Visit support portal</a>
                </div>
                @endif
                @if(!$status->support_email && !$status->support_phone && !$portalSupportUrl)
                <p class="text-muted small mb-0">Please contact your system administrator for assistance.</p>
                @endif
            </div>

            @if($status->suspended_at)
            <p class="text-muted small mb-4">Suspended on {{ $status->suspended_at->format('F j, Y \a\t g:i A') }}</p>
            @endif

            <a href="{{ route('home.logout') }}" class="btn btn-outline-secondary">
                <i class="fas fa-sign-out-alt me-1"></i> Sign Out
            </a>
        </div>
    </div>
</body>
</html>
