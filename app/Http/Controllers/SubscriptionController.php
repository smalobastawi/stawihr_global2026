<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    /**
     * Display subscription error page.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function error(Request $request)
    {
        $status = session('subscription_status', 'unknown');
        $message = session('subscription_message', 'An error occurred with your subscription.');
        $data = session('subscription_data');

        // Determine appropriate actions based on status
        $actions = $this->getActionsForStatus($status, $data);

        return view('subscription.error', [
            'status' => $status,
            'message' => $message,
            'subscription' => $data['subscription'] ?? null,
            'user' => $data['user'] ?? null,
            'actions' => $actions,
        ]);
    }

    /**
     * Get available actions based on subscription status.
     *
     * @param string $status
     * @param array|null $data
     * @return array
     */
    private function getActionsForStatus(string $status, ?array $data): array
    {
        $actions = [];
        $isTrial = $data['subscription']['is_trial'] ?? false;

        switch ($status) {
            case 'expired':
            case 'trial_expired':
                $actions[] = [
                    'label' => $isTrial ? 'Subscribe Now' : 'Renew Subscription',
                    'url' => config('portal.renewal_url', config('portal.base_url') . '/renew/subscription'),
                    'style' => 'primary',
                    'icon' => 'credit-card',
                ];
                $actions[] = [
                    'label' => 'Request Extension',
                    'url' => config('portal.support_url', config('portal.base_url') . '/contact') . '?subject=Extension%20Request',
                    'style' => 'secondary',
                    'icon' => 'envelope',
                ];
                break;

            case 'suspended':
                $actions[] = [
                    'label' => 'Contact Support',
                    'url' => config('portal.support_url', config('portal.base_url') . '/contact') . '?subject=Account%20Suspension',
                    'style' => 'primary',
                    'icon' => 'support',
                ];
                break;

            case 'in_arrears':
                $actions[] = [
                    'label' => 'Pay Outstanding Balance',
                    'url' => config('portal.base_url') . '/invoices',
                    'style' => 'primary',
                    'icon' => 'money-bill',
                ];
                $actions[] = [
                    'label' => 'Contact Billing',
                    'url' => config('portal.support_url', config('portal.base_url') . '/contact') . '?subject=Billing%20Inquiry',
                    'style' => 'secondary',
                    'icon' => 'envelope',
                ];
                break;

            case 'no_subscription':
                $actions[] = [
                    'label' => 'Choose a Package',
                    'url' => config('portal.upgrade_url', config('portal.base_url') . '/packages'),
                    'style' => 'primary',
                    'icon' => 'box',
                ];
                break;

            default:
                $actions[] = [
                    'label' => 'Contact Support',
                    'url' => config('portal.support_url', config('portal.base_url') . '/contact'),
                    'style' => 'primary',
                    'icon' => 'support',
                ];
        }

        // Always add logout option
        $actions[] = [
            'label' => 'Logout',
            'url' => route('logout'),
            'style' => 'outline-secondary',
            'icon' => 'sign-out-alt',
        ];

        return $actions;
    }

    /**
     * Manual subscription status refresh.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function refresh(Request $request)
    {
        // Clear subscription cache
        $domain = $this->getCompanyDomain();
        if ($domain) {
            app(\App\Services\PortalApiService::class)->clearSubscriptionCache($domain, auth()->user()?->email);
        }

        // Clear session data
        session()->forget([
            'subscription_status',
            'subscription_active',
            'subscription_expires_at',
            'subscription_days_remaining',
            'subscription_is_trial',
            'subscription_package',
            'subscription_message',
            'subscription_error',
        ]);

        return redirect()->route('dashboard')
            ->with('success', 'Subscription status refreshed. Please try again.');
    }

    /**
     * Get company domain.
     */
    private function getCompanyDomain(): ?string
    {
        $host = request()->getHost();
        if ($host && $host !== config('app.base_domain')) {
            return $host;
        }

        $user = auth()->user();
        if ($user && method_exists($user, 'company') && $user->company) {
            $subdomain = $user->company->subdomain ?? $user->company->slug;
            if ($subdomain) {
                return $subdomain . '.' . config('app.base_domain', 'stawihr.com');
            }
        }

        return config('portal.default_domain');
    }
}
