<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PortalApiService
{
    private string $baseUrl;
    private string $apiToken;
    private int $timeout;
    private int $cacheTtl;

    public function __construct()
    {
        $this->baseUrl = rtrim(config('portal.base_url'), '/');
        $this->apiToken = config('portal.api_token');
        $this->timeout = config('portal.timeout', 10);
        $this->cacheTtl = config('portal.cache_ttl', 300); // 5 minutes default
    }

    /**
     * Check user subscription status with the Portal.
     *
     * @param string $domain The company domain/subdomain
     * @param string|null $email User email (optional)
     * @param bool $useCache Whether to use cached results
     * @return array Subscription status information
     */
    public function checkSubscription(string $domain, ?string $email = null, bool $useCache = true): array
    {
        $cacheKey = "subscription:{$domain}" . ($email ? ":{$email}" : '');

        if ($useCache && $cached = Cache::get($cacheKey)) {
            return $cached;
        }

        try {
            $response = Http::withHeaders([
                'X-API-Token' => $this->apiToken,
                'Accept' => 'application/json',
            ])
            ->timeout($this->timeout)
            ->retry(config('portal.retry_attempts', 3), 100)
            ->post("{$this->baseUrl}/api/external/v1/check-subscription", [
                'domain' => $domain,
                'email' => $email,
            ]);

            if ($response->successful()) {
                $result = $response->json();

                if ($useCache) {
                    Cache::put($cacheKey, $result, $this->cacheTtl);
                }

                return $result;
            }

            Log::error('Portal API error', [
                'status' => $response->status(),
                'body' => $response->body(),
                'domain' => $domain,
            ]);

            return $this->getErrorResponse('Failed to verify subscription status.', $response->status());
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::error('Portal API connection failed', [
                'error' => $e->getMessage(),
                'domain' => $domain,
            ]);

            return $this->getErrorResponse('Unable to connect to subscription service.', 'connection_error');
        } catch (\Exception $e) {
            Log::error('Portal API unexpected error', [
                'error' => $e->getMessage(),
                'domain' => $domain,
            ]);

            return $this->getErrorResponse('An error occurred while verifying subscription.', 'error');
        }
    }

    /**
     * Clear the subscription cache for a domain.
     *
     * @param string $domain
     * @param string|null $email
     * @return void
     */
    public function clearSubscriptionCache(string $domain, ?string $email = null): void
    {
        $cacheKey = "subscription:{$domain}" . ($email ? ":{$email}" : '');
        Cache::forget($cacheKey);
    }

    /**
     * Determine if user should be allowed access based on subscription check.
     *
     * @param array $response The API response
     * @return bool
     */
    public function isAccessAllowed(array $response): bool
    {
        if (!($response['success'] ?? false)) {
            return false;
        }

        return $response['data']['access_allowed'] ?? false;
    }

    /**
     * Get subscription status from response.
     *
     * @param array $response The API response
     * @return string
     */
    public function getSubscriptionStatus(array $response): string
    {
        return $response['data']['subscription_status'] ?? 'unknown';
    }

    /**
     * Check if subscription has expired.
     *
     * @param array $response The API response
     * @return bool
     */
    public function isExpired(array $response): bool
    {
        $status = $this->getSubscriptionStatus($response);
        return in_array($status, ['expired', 'trial_expired']);
    }

    /**
     * Check if subscription is in arrears or suspended.
     *
     * @param array $response The API response
     * @return bool
     */
    public function isRestricted(array $response): bool
    {
        $status = $this->getSubscriptionStatus($response);
        return in_array($status, ['suspended', 'in_arrears', 'no_subscription']);
    }

    /**
     * Check if subscription is approaching expiry.
     *
     * @param array $response The API response
     * @param int $daysThreshold Days before expiry to consider as "approaching"
     * @return bool
     */
    public function isExpiringSoon(array $response, int $daysThreshold = 7): bool
    {
        if (!$this->isAccessAllowed($response)) {
            return false;
        }

        $daysRemaining = $response['data']['subscription']['days_remaining'] ?? 0;

        return $daysRemaining > 0 && $daysRemaining <= $daysThreshold;
    }

    /**
     * Get human-readable message from response.
     *
     * @param array $response The API response
     * @return string|null
     */
    public function getMessage(array $response): ?string
    {
        return $response['message']
            ?? $response['data']['status_details']['description']
            ?? null;
    }

    /**
     * Get renewal URL from subscription data.
     *
     * @param array $response The API response
     * @return string
     */
    public function getRenewalUrl(array $response): string
    {
        $domain = $response['data']['user']['domain'] ?? '';
        return config('portal.renewal_url', $this->baseUrl . '/renew/subscription');
    }

    /**
     * Get package information from subscription.
     *
     * @param array $response The API response
     * @return array
     */
    public function getPackageInfo(array $response): array
    {
        $subscription = $response['data']['subscription'] ?? [];

        return [
            'name' => $subscription['package_name'] ?? 'Unknown',
            'max_employees' => $subscription['max_employees'] ?? null,
            'days_remaining' => $subscription['days_remaining'] ?? 0,
            'is_trial' => $subscription['is_trial'] ?? false,
        ];
    }

    /**
     * Report usage stats to the Portal.
     *
     * @param string $domain
     * @param array $stats
     * @return array
     */
    public function reportStats(string $domain, array $stats): array
    {
        try {
            $response = Http::withHeaders([
                'X-API-Token' => $this->apiToken,
                'Accept' => 'application/json',
            ])
            ->timeout($this->timeout)
            ->post("{$this->baseUrl}/api/external/v1/report-stats", [
                'domain' => $domain,
                'stats' => $stats,
            ]);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('Failed to report stats to Portal', [
                'status' => $response->status(),
                'domain' => $domain,
            ]);

            return ['success' => false];
        } catch (\Exception $e) {
            Log::error('Error reporting stats to Portal', [
                'error' => $e->getMessage(),
                'domain' => $domain,
            ]);

            return ['success' => false];
        }
    }

    /**
     * Get error response structure.
     *
     * @param string $message
     * @param string $code
     * @return array
     */
    private function getErrorResponse(string $message, string $code): array
    {
        return [
            'success' => false,
            'message' => $message,
            'error_code' => $code,
            'data' => [
                'access_allowed' => false,
                'subscription_status' => 'error',
            ],
        ];
    }
}
