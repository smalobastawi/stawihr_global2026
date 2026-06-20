<?php

namespace App\Http\Middleware;

use App\Models\Employee;
use App\Models\User;
use App\Services\PortalApiService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class CheckPackageLimits
{
    protected PortalApiService $portalApi;

    // Default limits for different packages
    protected array $defaultLimits = [
        'basic' => [
            'max_employees' => 25,
            'max_departments' => 5,
            'max_users' => 5,
        ],
        'standard' => [
            'max_employees' => 100,
            'max_departments' => 15,
            'max_users' => 15,
        ],
        'professional' => [
            'max_employees' => 500,
            'max_departments' => 50,
            'max_users' => 50,
        ],
        'enterprise' => [
            'max_employees' => null, // unlimited
            'max_departments' => null,
            'max_users' => null,
        ],
    ];

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

        $user = $request->user();
        if (!$user) {
            return $next($request);
        }

        $company = $this->getCompany($user);
        if (!$company) {
            return $next($request);
        }

        // Get current counts
        $counts = $this->getCurrentCounts($company);

        // Get package limits (from cache, portal, or defaults)
        $limits = $this->getPackageLimits($company);

        // Check if limits are exceeded
        $exceededLimits = $this->checkLimits($counts, $limits);

        // Store limit info in session for banner display
        $this->storeLimitInfo($counts, $limits, $exceededLimits);

        // If limits exceeded on a critical route, block access
        if (!empty($exceededLimits) && $this->isCriticalRoute($request)) {
            $limitName = array_key_first($exceededLimits);
            return $this->handleLimitExceeded($limitName, $exceededLimits[$limitName], $request);
        }

        return $next($request);
    }

    /**
     * Determine if the limit check should be skipped.
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

        return false;
    }

    /**
     * Check if current route is critical for limit enforcement.
     */
    private function isCriticalRoute(Request $request): bool
    {
        // Block employee creation if limit exceeded
        if ($request->routeIs('employees.create', 'employees.store', 'employees.import')) {
            return true;
        }

        // Block user creation if limit exceeded
        if ($request->routeIs('users.create', 'users.store', 'users.invite')) {
            return true;
        }

        // Block department creation if limit exceeded
        if ($request->routeIs('departments.create', 'departments.store')) {
            return true;
        }

        return false;
    }

    /**
     * Get company from user.
     */
    private function getCompany($user): ?\App\Models\Company
    {
        if (method_exists($user, 'company')) {
            return $user->company;
        }

        if (isset($user->company_id)) {
            return \App\Models\Company::find($user->company_id);
        }

        return null;
    }

    /**
     * Get current resource counts for the company.
     */
    private function getCurrentCounts($company): array
    {
        return [
            'employees' => Employee::where('company_id', $company->id)->count(),
            'users' => User::where('company_id', $company->id)->count(),
            'departments' => \App\Models\Department::where('company_id', $company->id)->count(),
        ];
    }

    /**
     * Get package limits for the company.
     */
    private function getPackageLimits($company): array
    {
        // Try to get from session cache first
        if (session()->has('package_limits')) {
            return session('package_limits');
        }

        // Try to get from subscription info in session
        $subscriptionPackage = session('subscription_package', 'basic');

        $limits = $this->defaultLimits[strtolower($subscriptionPackage)] ?? $this->defaultLimits['basic'];

        // Check if we need to fetch fresh data from portal
        if (config('portal.fetch_limits_from_portal', false)) {
            try {
                $domain = $this->getCompanyDomain($company);
                $response = $this->portalApi->checkSubscription($domain, null, true);

                if ($response['success']) {
                    $packageInfo = $this->portalApi->getPackageInfo($response);
                    // Update limits based on package info
                    if (isset($packageInfo['max_employees'])) {
                        $limits['max_employees'] = $packageInfo['max_employees'];
                    }
                }
            } catch (\Exception $e) {
                Log::warning('Failed to fetch package limits from portal', [
                    'company_id' => $company->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return $limits;
    }

    /**
     * Check if any limits are exceeded.
     */
    private function checkLimits(array $counts, array $limits): array
    {
        $exceeded = [];

        if ($limits['max_employees'] !== null && $counts['employees'] >= $limits['max_employees']) {
            $exceeded['employees'] = [
                'current' => $counts['employees'],
                'limit' => $limits['max_employees'],
                'overage' => $counts['employees'] - $limits['max_employees'],
            ];
        }

        if ($limits['max_users'] !== null && $counts['users'] >= $limits['max_users']) {
            $exceeded['users'] = [
                'current' => $counts['users'],
                'limit' => $limits['max_users'],
                'overage' => $counts['users'] - $limits['max_users'],
            ];
        }

        if ($limits['max_departments'] !== null && $counts['departments'] >= $limits['max_departments']) {
            $exceeded['departments'] = [
                'current' => $counts['departments'],
                'limit' => $limits['max_departments'],
                'overage' => $counts['departments'] - $limits['max_departments'],
            ];
        }

        return $exceeded;
    }

    /**
     * Store limit information in session for banner display.
     */
    private function storeLimitInfo(array $counts, array $limits, array $exceededLimits): void
    {
        $limitWarnings = [];

        // Check for approaching limits (80% threshold)
        $threshold = 0.8;

        foreach ($counts as $resource => $count) {
            $limit = $limits["max_{$resource}"] ?? null;

            if ($limit !== null) {
                $percentage = $count / $limit;

                if ($percentage >= 1) {
                    $limitWarnings[$resource] = [
                        'status' => 'exceeded',
                        'current' => $count,
                        'limit' => $limit,
                        'percentage' => round($percentage * 100, 1),
                    ];
                } elseif ($percentage >= $threshold) {
                    $limitWarnings[$resource] = [
                        'status' => 'approaching',
                        'current' => $count,
                        'limit' => $limit,
                        'percentage' => round($percentage * 100, 1),
                    ];
                }
            }
        }

        session([
            'current_counts' => $counts,
            'package_limits' => $limits,
            'limit_warnings' => $limitWarnings,
            'limits_exceeded' => !empty($exceededLimits),
        ]);
    }

    /**
     * Handle limit exceeded on critical route.
     */
    private function handleLimitExceeded(string $resource, array $details, Request $request): Response
    {
        $message = "You have reached the maximum limit of {$details['limit']} {$resource}. ";
        $message .= "Please upgrade your package to add more {$resource}.";

        Log::warning('Package limit exceeded', [
            'user_id' => auth()->id(),
            'resource' => $resource,
            'details' => $details,
        ]);

        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json([
                'success' => false,
                'message' => $message,
                'limit_exceeded' => true,
                'resource' => $resource,
                'current' => $details['current'],
                'limit' => $details['limit'],
                'upgrade_url' => config('portal.upgrade_url', config('portal.base_url') . '/packages'),
            ], 403);
        }

        return redirect()->back()
            ->with('error', $message)
            ->with('limit_exceeded', true)
            ->with('show_upgrade_banner', true);
    }

    /**
     * Get company domain for API calls.
     */
    private function getCompanyDomain($company): ?string
    {
        $subdomain = $company->subdomain ?? $company->slug ?? $company->domain;

        if ($subdomain) {
            return $subdomain . '.' . config('app.base_domain', 'stawihr.com');
        }

        return request()->getHost();
    }
}
