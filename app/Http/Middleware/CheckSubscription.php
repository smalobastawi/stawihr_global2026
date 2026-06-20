<?php

namespace App\Http\Middleware;

use App\Services\PortalApiService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class CheckSubscription
{
    protected PortalApiService $portalApi;

    public function __construct(PortalApiService $portalApi)
    {
        $this->portalApi = $portalApi;
    }

    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Skip for certain routes
        if ($this->shouldSkipCheck($request)) {
            return $next($request);
        }

        // Get the authenticated user
        $user = $request->user();
        if (!$user) {
            return redirect()->route('login');
        }

        // Get company/domain information
        $domain = $this->getCompanyDomain();
        if (!$domain) {
            Log::error('CheckSubscription: No domain found', ['user_id' => $user->id]);
            return $next($request); // Allow but log error
        }

        // Check subscription with Portal
        $response = $this->portalApi->checkSubscription($domain, $user->email);

        // Store subscription info in session for banner display
        $this->storeSubscriptionInfo($response);

        // Check if access should be denied
        if (!$this->portalApi->isAccessAllowed($response)) {
            return $this->handleDeniedAccess($response, $request);
        }

        // Check for package limits - will be handled by CheckPackageLimits middleware
        // but we store the info for reference

        return $next($request);
    }

    /**
     * Determine if the subscription check should be skipped.
     */
    private function shouldSkipCheck(Request $request): bool
    {
        $excludedRoutes = [
            'login',
            'logout',
            'register',
            'password.*',
            'subscription.*',
            'api.*',
            'public.*',
            'health',
            '_debugbar.*',
        ];

        foreach ($excludedRoutes as $route) {
            if ($request->routeIs($route)) {
                return true;
            }
        }

        // Skip API routes that don't need subscription check
        if ($request->is('api/internal/*')) {
            return true;
        }

        return false;
    }

    /**
     * Get the company domain for API calls.
     */
    private function getCompanyDomain(): ?string
    {
        // Option 1: From request host/subdomain
        $host = request()->getHost();
        if ($host && $host !== config('app.base_domain')) {
            return $host;
        }

        // Option 2: From authenticated user's company
        $user = auth()->user();
        if ($user && method_exists($user, 'company') && $user->company) {
            $subdomain = $user->company->subdomain ?? $user->company->slug;
            if ($subdomain) {
                return $subdomain . '.' . config('app.base_domain', 'stawihr.com');
            }
        }

        // Option 3: From company in session
        if (session()->has('company_domain')) {
            return session('company_domain');
        }

        // Option 4: From config (for testing/single-tenant)
        return config('portal.default_domain');
    }

    /**
     * Store subscription information in session for banner display.
     */
    private function storeSubscriptionInfo(array $response): void
    {
        if (!$response['success']) {
            session(['subscription_error' => true]);
            return;
        }

        $data = $response['data'] ?? [];

        session([
            'subscription_status' => $data['subscription_status'] ?? 'unknown',
            'subscription_active' => $data['access_allowed'] ?? false,
            'subscription_expires_at' => $data['subscription']['end_date'] ?? null,
            'subscription_days_remaining' => $data['subscription']['days_remaining'] ?? 0,
            'subscription_is_trial' => $data['subscription']['is_trial'] ?? false,
            'subscription_package' => $data['subscription']['package_name'] ?? null,
            'subscription_message' => $response['message'] ?? null,
        ]);
    }

    /**
     * Handle denied access based on subscription status.
     */
    private function handleDeniedAccess(array $response, Request $request): Response
    {
        $status = $this->portalApi->getSubscriptionStatus($response);
        $message = $this->getStatusMessage($status, $response);

        // Log the access denial
        Log::warning('Subscription access denied', [
            'user_id' => auth()->id(),
            'domain' => $this->getCompanyDomain(),
            'status' => $status,
        ]);

        // For API requests, return JSON response
        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json([
                'success' => false,
                'message' => $message,
                'subscription_status' => $status,
                'renewal_url' => $this->portalApi->getRenewalUrl($response),
            ], 403);
        }

        // For web requests, redirect to subscription error page
        return redirect()->route('subscription.error')
            ->with([
                'subscription_status' => $status,
                'subscription_message' => $message,
                'subscription_data' => $response['data'] ?? null,
            ]);
    }

    /**
     * Get user-friendly message for subscription status.
     */
    private function getStatusMessage(string $status, array $response): string
    {
        $customMessage = $this->portalApi->getMessage($response);
        if ($customMessage) {
            return $customMessage;
        }

        $messages = [
            'active' => 'Your subscription is active.',
            'trial_active' => 'Your trial is active.',
            'expired' => 'Your subscription has expired. Please renew to continue.',
            'trial_expired' => 'Your trial has expired. Please subscribe to continue.',
            'suspended' => 'Your account has been suspended. Please contact support.',
            'in_arrears' => 'Your account has outstanding payments. Please settle to continue.',
            'no_subscription' => 'No active subscription found. Please subscribe to continue.',
            'not_found' => 'Company account not found. Please contact support.',
            'error' => 'Unable to verify subscription. Please try again later.',
            'connection_error' => 'Unable to connect to subscription service. Please try again later.',
        ];

        return $messages[$status] ?? 'Subscription verification failed. Please contact support.';
    }
}
