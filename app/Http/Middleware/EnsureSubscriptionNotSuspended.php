<?php

namespace App\Http\Middleware;

use App\Models\PortalSubscriptionStatus;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureSubscriptionNotSuspended
{
    /**
     * @var array<int, string>
     */
    protected array $exceptRouteNames = [
        'subscription.suspended',
        'home.logout',
        'login',
        'verify',
        'verify-otp',
        'resend-otp',
    ];

    /**
     * @var array<int, string>
     */
    protected array $exceptPaths = [
        'login',
        'verify',
        'subscription/suspended',
        'logout',
        'changePassword',
        'password',
        'reset_password_without_token',
        'reset_password_with_token',
        'resetPassword',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return $next($request);
        }

        if ($this->shouldBypass($request)) {
            return $next($request);
        }

        $status = PortalSubscriptionStatus::current();

        if ($status->is_suspended) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'This subscription has been suspended.',
                    'data' => [
                        'is_suspended' => true,
                        'reason' => $status->reason,
                        'support_email' => $status->support_email,
                        'support_phone' => $status->support_phone,
                    ],
                ], 403);
            }

            return redirect()->route('subscription.suspended');
        }

        return $next($request);
    }

    protected function shouldBypass(Request $request): bool
    {
        $routeName = $request->route()?->getName();

        if ($routeName && in_array($routeName, $this->exceptRouteNames, true)) {
            return true;
        }

        foreach ($this->exceptPaths as $path) {
            if ($request->is($path) || $request->is($path.'/*')) {
                return true;
            }
        }

        return false;
    }
}
