# StawiHR Backend - Portal Integration Guide

This guide explains how to integrate the StawiHR backend (`stawihr_global2026`) with the Stawi Self Portal for subscription management and billing.

---

## Table of Contents

1. [Overview](#overview)
2. [Installation](#installation)
3. [Configuration](#configuration)
4. [Middleware Setup](#middleware-setup)
5. [Route Registration](#route-registration)
6. [View Composer Setup](#view-composer-setup)
7. [Using the Banner Component](#using-the-banner-component)
8. [API Endpoints](#api-endpoints)
9. [Testing](#testing)
10. [Troubleshooting](#troubleshooting)

---

## Overview

This integration enables:

1. **Subscription Verification**: StawiHR checks with the Portal to verify subscription status on each request
2. **Package Limit Enforcement**: Blocks creation of resources beyond package limits
3. **Banner Alerts**: Shows banners for expiring subscriptions, exceeded limits, and payment issues
4. **Stats Reporting**: Portal can query employee/user/department counts from StawiHR
5. **Renewal Flow**: Users are redirected to the Portal for subscription renewal

---

## Installation

### 1. Copy Integration Files

Copy these files from this folder to your StawiHR Laravel application:

```
stawihr_global2026/
├── app/
│   ├── Services/
│   │   └── PortalApiService.php          → app/Services/PortalApiService.php
│   ├── Http/
│   │   ├── Middleware/
│   │   │   ├── CheckSubscription.php     → app/Http/Middleware/CheckSubscription.php
│   │   │   ├── CheckPackageLimits.php    → app/Http/Middleware/CheckPackageLimits.php
│   │   │   └── InternalApiAuth.php       → app/Http/Middleware/InternalApiAuth.php
│   │   └── Controllers/
│   │       ├── Api/
│   │       │   └── Internal/
│   │       │       └── StatsController.php → app/Http/Controllers/Api/Internal/StatsController.php
│   │       └── SubscriptionController.php  → app/Http/Controllers/SubscriptionController.php
│   └── View/
│       └── Composers/
│           └── SubscriptionBannerComposer.php → app/View/Composers/SubscriptionBannerComposer.php
├── config/
│   └── portal.php                          → config/portal.php
├── resources/
│   └── views/
│       ├── components/
│       │   └── subscription-banner.blade.php → resources/views/components/subscription-banner.blade.php
│       └── subscription/
│           └── error.blade.php             → resources/views/subscription/error.blade.php
└── routes/
    └── internal-api.php                    → routes/internal-api.php
```

### 2. Install Dependencies

Ensure you have these Laravel features available:

```bash
# HTTP Client (usually included in Laravel)
php artisan make:provider PortalServiceProvider
```

---

## Configuration

### 1. Environment Variables

Add to your `.env` file:

```env
# Portal Configuration
PORTAL_BASE_URL=https://portal.stawihr.com
PORTAL_API_TOKEN=your_shared_api_token_here
PORTAL_API_TIMEOUT=10
PORTAL_RETRY_ATTEMPTS=3
PORTAL_CACHE_TTL=300
PORTAL_STRICT_MODE=false

# URLs
PORTAL_RENEWAL_URL=https://portal.stawihr.com/renew/subscription
PORTAL_UPGRADE_URL=https://portal.stawihr.com/packages
PORTAL_SUPPORT_URL=https://portal.stawihr.com/contact
```

> **Note**: The `PORTAL_API_TOKEN` must match the `EXTERNAL_API_TOKEN` in the Portal's `.env` file.

### 2. Publish Config

Copy the config file:

```bash
cp config/portal.php /path/to/your/stawihr/config/portal.php
```

---

## Middleware Setup

### 1. Register Middleware Aliases

In `bootstrap/app.php`:

```php
use App\Http\Middleware\CheckSubscription;
use App\Http\Middleware\CheckPackageLimits;
use App\Http\Middleware\InternalApiAuth;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function () {
            // Register internal API routes
            Route::middleware('api')
                ->prefix('api/internal')
                ->group(base_path('routes/internal-api.php'));
        },
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'check.subscription' => CheckSubscription::class,
            'check.limits' => CheckPackageLimits::class,
            'internal.api.auth' => InternalApiAuth::class,
        ]);
    })
    // ... rest of configuration
```

### 2. Apply Middleware to Routes

In `routes/web.php`:

```php
// Public routes (no subscription check needed)
Route::get('/', [HomeController::class, 'index']);
Route::get('/login', [AuthController::class, 'login'])->name('login');

// Protected routes - subscription and limit checking
Route::middleware([
    'auth',
    'check.subscription',  // Verify subscription is active
    'check.limits',          // Check package limits
])->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Employee management
    Route::get('/employees', [EmployeeController::class, 'index'])->name('employees.index');
    Route::get('/employees/create', [EmployeeController::class, 'create'])->name('employees.create');
    Route::post('/employees', [EmployeeController::class, 'store'])->name('employees.store');

    // User management
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');

    // Departments
    Route::get('/departments', [DepartmentController::class, 'index'])->name('departments.index');
    Route::get('/departments/create', [DepartmentController::class, 'create'])->name('departments.create');
    Route::post('/departments', [DepartmentController::class, 'store'])->name('departments.store');

    // ... other protected routes
});

// Subscription error page (accessible without active subscription)
Route::get('/subscription-error', [SubscriptionController::class, 'error'])
    ->name('subscription.error');
Route::get('/subscription-refresh', [SubscriptionController::class, 'refresh'])
    ->name('subscription.refresh');
```

---

## View Composer Setup

### 1. Register View Composer

In a service provider (e.g., `app/Providers/AppServiceProvider.php`):

```php
use App\View\Composers\SubscriptionBannerComposer;
use Illuminate\Support\Facades\View;

public function boot(): void
{
    // Register subscription banner composer for all views
    View::composer('*', SubscriptionBannerComposer::class);

    // Or for specific layouts only:
    // View::composer(['layouts.app', 'layouts.dashboard'], SubscriptionBannerComposer::class);
}
```

### 2. Add Banner to Layout

In your main layout file (e.g., `resources/views/layouts/app.blade.php`):

```blade
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    {{-- ... head content ... --}}
</head>
<body>
    {{-- Subscription Banner - shown at top of page --}}
    <x-subscription-banner />

    {{-- Navigation --}}
    <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
        {{-- ... nav content ... --}}
    </nav>

    {{-- Main Content --}}
    <main class="py-4">
        @yield('content')
    </main>

    {{-- Scripts --}}
    @stack('scripts')
</body>
</html>
```

---

## Using the Banner Component

The subscription banner automatically appears based on the subscription status and shows:

### Banner Types

1. **Danger (Red)**: Subscription expired, suspended, or in arrears
2. **Warning (Yellow)**: Approaching expiry or package limits
3. **Info (Blue)**: Trial active, approaching limits warning
4. **Secondary (Gray)**: Connection issues

### Dismissible Banners

Some banners can be dismissed (temporarily hidden):

- Click the **×** button on the right side of the banner
- Banner reappears after 1 hour for important notifications

### Banner Actions

Each banner includes action buttons:

- **Renew/Subscribe**: Takes user to Portal payment page
- **Upgrade**: Shows package upgrade options
- **Contact Support**: Opens support contact form

---

## API Endpoints

### Internal API (Portal → StawiHR)

These endpoints allow the Portal to query your StawiHR instance:

#### 1. Get Statistics

```http
POST /api/internal/stats
Headers:
  X-API-Token: {PORTAL_API_TOKEN}
  Accept: application/json

Body:
{
  "stats": ["companies", "users", "employees", "departments"]
}

Response:
{
  "success": true,
  "data": {
    "company_id": 123,
    "timestamp": "2026-06-19 14:30:00",
    "stats": {
      "companies": { "count": 1, "name": "Acme Corp" },
      "users": { "count": 5, "active": 4, "inactive": 1 },
      "employees": { "count": 25, "active": 23, "on_leave": 2 },
      "departments": { "count": 4, "list": [...] }
    }
  }
}
```

#### 2. Get Summary

```http
GET /api/internal/summary
Headers:
  X-API-Token: {PORTAL_API_TOKEN}

Response:
{
  "success": true,
  "data": {
    "company": { "id": 123, "name": "Acme Corp" },
    "counts": {
      "users": 5,
      "employees": 25,
      "departments": 4
    }
  }
}
```

#### 3. Health Check

```http
GET /api/internal/health
(No authentication required)

Response:
{
  "status": "ok",
  "timestamp": "2026-06-19 14:30:00",
  "version": "1.0.0"
}
```

---

## Testing

### 1. Test Portal Connection

Create a test route in `routes/web.php`:

```php
Route::get('/test-portal-connection', function () {
    $service = app(\App\Services\PortalApiService::class);

    $domain = request()->getHost();
    $response = $service->checkSubscription($domain, auth()->user()?->email, false);

    return response()->json([
        'domain' => $domain,
        'access_allowed' => $service->isAccessAllowed($response),
        'status' => $service->getSubscriptionStatus($response),
        'is_expired' => $service->isExpired($response),
        'package_info' => $service->getPackageInfo($response),
        'raw_response' => $response,
    ]);
})->middleware('auth');
```

### 2. Test Stats Endpoint

```bash
curl -X POST https://your-hr-instance.com/api/internal/stats \
  -H "X-API-Token: your_api_token" \
  -H "Content-Type: application/json" \
  -d '{"stats": ["users", "employees"]}'
```

### 3. Simulate Different Statuses

Temporarily modify the response in `PortalApiService::checkSubscription()` to test banners:

```php
// For testing - force specific status
return [
    'success' => true,
    'data' => [
        'access_allowed' => false,
        'subscription_status' => 'expired',
        'subscription' => [
            'end_date' => '2026-01-01',
            'package_name' => 'Basic',
        ],
    ],
    'message' => 'Your subscription has expired.',
];
```

---

## Troubleshooting

### Issue: "Unauthorized. Invalid API token"

**Cause**: API tokens don't match between Portal and StawiHR

**Solution**:
1. In Portal `.env`: Check `EXTERNAL_API_TOKEN`
2. In StawiHR `.env`: Check `PORTAL_API_TOKEN`
3. Ensure both values are identical

### Issue: "Unable to connect to subscription service"

**Cause**: Network connectivity or DNS issues

**Solution**:
1. Check `PORTAL_BASE_URL` is correct
2. From StawiHR server, run: `curl -I https://portal.stawihr.com`
3. Check firewall rules allow outbound HTTPS

### Issue: Banner not showing

**Cause**: View composer not registered or middleware not applied

**Solution**:
1. Verify `SubscriptionBannerComposer` is registered in AppServiceProvider
2. Check `<x-subscription-banner />` is in your layout
3. Ensure `CheckSubscription` middleware is applied to the route

### Issue: Limits not being enforced

**Cause**: `CheckPackageLimits` middleware not applied

**Solution**:
1. Add `'check.limits'` to route middleware array
2. Check `package_limits` in session storage
3. Verify employee count query is correct for your database schema

### Issue: Users can still access despite expired subscription

**Cause**: Session caching or middleware bypass

**Solution**:
1. Clear session cache: `php artisan cache:clear`
2. Check `CheckSubscription` middleware is applied correctly
3. Set `PORTAL_STRICT_MODE=true` in `.env`

---

## Security Considerations

1. **API Token Security**:
   - Use strong, random tokens (32+ characters)
   - Rotate tokens periodically
   - Never commit tokens to version control

2. **HTTPS Only**:
   - All API communication should use HTTPS
   - Set `SECURE_HEADERS` in production

3. **Rate Limiting**:
   Add to internal API routes in `routes/internal-api.php`:
   ```php
   Route::middleware([
       'internal.api.auth',
       'throttle:60,1', // 60 requests per minute
   ])->group(function () {
       // ... routes
   });
   ```

4. **IP Whitelisting** (optional):
   Add to `InternalApiAuth` middleware to restrict by IP:
   ```php
   $allowedIps = config('portal.allowed_ips', []);
   if (!empty($allowedIps) && !in_array($request->ip(), $allowedIps)) {
       return response()->json(['success' => false, 'message' => 'IP not allowed'], 403);
   }
   ```

---

## Customization

### Custom Package Limits

Edit `CheckPackageLimits.php`:

```php
protected array $defaultLimits = [
    'starter' => [
        'max_employees' => 10,
        'max_departments' => 2,
        'max_users' => 2,
    ],
    'growth' => [
        'max_employees' => 50,
        'max_departments' => 10,
        'max_users' => 10,
    ],
    // ... add your custom tiers
];
```

### Custom Banner Styling

Edit `resources/views/components/subscription-banner.blade.php`:

Modify the CSS in the `@push('styles')` section to match your design.

### Custom Limit Check Thresholds

Edit `CheckPackageLimits.php`:

```php
// Change from 80% to 90%
private float $warningThreshold = 0.9;
```

---

## Support

For issues or questions:

- **Portal Admin**: admin@stawihr.com
- **Technical Support**: support@stawihr.com
- **API Documentation**: https://portal.stawihr.com/docs/api

---

## Changelog

| Version | Date | Changes |
|---------|------|---------|
| 1.0.0 | 2026-06-19 | Initial integration release |
