<?php

namespace App\View\Composers;

use Illuminate\View\View;

class SubscriptionBannerComposer
{
    /**
     * Bind data to the view.
     */
    public function compose(View $view): void
    {
        $view->with('subscriptionBanner', $this->getBannerData());
    }

    /**
     * Get banner data based on subscription status.
     */
    private function getBannerData(): array
    {
        $data = [
            'show' => false,
            'type' => null,
            'message' => null,
            'action_url' => null,
            'action_text' => null,
        ];

        // Check subscription status from session
        $status = session('subscription_status');
        $isActive = session('subscription_active', false);
        $daysRemaining = session('subscription_days_remaining', 0);
        $isTrial = session('subscription_is_trial', false);

        // Check limit warnings
        $limitWarnings = session('limit_warnings', []);
        $limitsExceeded = session('limits_exceeded', false);

        // Priority 1: Subscription expired
        if (in_array($status, ['expired', 'trial_expired'])) {
            return [
                'show' => true,
                'type' => 'danger',
                'icon' => 'exclamation-circle',
                'title' => $isTrial ? 'Trial Expired' : 'Subscription Expired',
                'message' => session('subscription_message') ?? 'Your subscription has expired. Please renew to continue using the service.',
                'action_url' => config('portal.renewal_url', config('portal.base_url') . '/renew/subscription'),
                'action_text' => $isTrial ? 'Subscribe Now' : 'Renew Subscription',
                'secondary_action_url' => config('portal.support_url', config('portal.base_url') . '/contact'),
                'secondary_action_text' => 'Contact Support',
                'dismissible' => false,
            ];
        }

        // Priority 2: Subscription suspended or in arrears
        if (in_array($status, ['suspended', 'in_arrears'])) {
            return [
                'show' => true,
                'type' => 'danger',
                'icon' => 'ban',
                'title' => 'Account Suspended',
                'message' => session('subscription_message') ?? 'Your account has been suspended. Please contact support.',
                'action_url' => config('portal.support_url', config('portal.base_url') . '/contact'),
                'action_text' => 'Contact Support',
                'dismissible' => false,
            ];
        }

        // Priority 3: Limits exceeded
        if ($limitsExceeded && !empty($limitWarnings)) {
            $firstExceeded = null;
            foreach ($limitWarnings as $resource => $warning) {
                if ($warning['status'] === 'exceeded') {
                    $firstExceeded = $resource;
                    break;
                }
            }

            if ($firstExceeded) {
                return [
                    'show' => true,
                    'type' => 'warning',
                    'icon' => 'exclamation-triangle',
                    'title' => 'Package Limit Reached',
                    'message' => "You have reached the maximum number of {$firstExceeded} allowed in your package. Upgrade to add more.",
                    'action_url' => config('portal.upgrade_url', config('portal.base_url') . '/packages'),
                    'action_text' => 'Upgrade Package',
                    'dismissible' => true,
                ];
            }
        }

        // Priority 4: Approaching limits (80% threshold)
        if (!empty($limitWarnings)) {
            $approaching = [];
            foreach ($limitWarnings as $resource => $warning) {
                if ($warning['status'] === 'approaching') {
                    $approaching[] = "{$resource} ({$warning['current']}/{$warning['limit']})";
                }
            }

            if (!empty($approaching)) {
                $resources = implode(', ', $approaching);
                return [
                    'show' => true,
                    'type' => 'info',
                    'icon' => 'info-circle',
                    'title' => 'Approaching Package Limits',
                    'message' => "You are approaching the limit for: {$resources}. Consider upgrading your package.",
                    'action_url' => config('portal.upgrade_url', config('portal.base_url') . '/packages'),
                    'action_text' => 'View Packages',
                    'dismissible' => true,
                ];
            }
        }

        // Priority 5: Subscription expiring soon (less than 7 days)
        if ($isActive && $daysRemaining > 0 && $daysRemaining <= 7 && !$isTrial) {
            return [
                'show' => true,
                'type' => 'warning',
                'icon' => 'clock',
                'title' => 'Subscription Expiring Soon',
                'message' => "Your subscription expires in {$daysRemaining} day" . ($daysRemaining > 1 ? 's' : '') . ". Renew now to avoid interruption.",
                'action_url' => config('portal.renewal_url', config('portal.base_url') . '/renew/subscription'),
                'action_text' => 'Renew Now',
                'dismissible' => true,
            ];
        }

        // Priority 6: Active trial
        if ($isActive && $isTrial && $daysRemaining > 0) {
            return [
                'show' => true,
                'type' => 'info',
                'icon' => 'gift',
                'title' => 'Trial Active',
                'message' => "You have {$daysRemaining} day" . ($daysRemaining > 1 ? 's' : '') . " remaining in your trial. Subscribe now for full access.",
                'action_url' => config('portal.upgrade_url', config('portal.base_url') . '/packages'),
                'action_text' => 'Subscribe Now',
                'dismissible' => true,
            ];
        }

        // Priority 7: Portal connection error
        if (session('subscription_error')) {
            return [
                'show' => true,
                'type' => 'secondary',
                'icon' => 'wifi-off',
                'title' => 'Connection Issue',
                'message' => 'Unable to verify subscription status. Some features may be limited.',
                'action_url' => null,
                'action_text' => null,
                'dismissible' => true,
            ];
        }

        return $data;
    }
}
