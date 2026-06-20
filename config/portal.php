<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Portal Base URL
    |--------------------------------------------------------------------------
    |
    | The base URL of the Stawi Self Portal where subscription
    | management and billing occur.
    |
    */

    'base_url' => env('PORTAL_BASE_URL', 'https://portal.stawihr.com'),

    /*
    |--------------------------------------------------------------------------
    | Portal API Token
    |--------------------------------------------------------------------------
    |
    | The API token for authenticating with the Portal's external API.
    | This should match the EXTERNAL_API_TOKEN in the Portal's .env
    |
    */

    'api_token' => env('PORTAL_API_TOKEN', ''),

    /*
    |--------------------------------------------------------------------------
    | Internal API Token
    |--------------------------------------------------------------------------
    |
    | Token used by the Stawi Self Portal when querying this HR instance.
    | Must match HR_BACKEND_API_TOKEN in the Portal .env file.
    |
    */

    'internal_api_token' => env('HR_BACKEND_API_TOKEN', env('PORTAL_API_TOKEN', '')),

    /*
    |--------------------------------------------------------------------------
    | API Timeout
    |--------------------------------------------------------------------------
    |
    | The timeout in seconds for API requests to the Portal.
    |
    */

    'timeout' => env('PORTAL_API_TIMEOUT', 10),

    /*
    |--------------------------------------------------------------------------
    | Retry Attempts
    |--------------------------------------------------------------------------
    |
    | Number of retry attempts for failed API calls.
    |
    */

    'retry_attempts' => env('PORTAL_RETRY_ATTEMPTS', 3),

    /*
    |--------------------------------------------------------------------------
    | Cache TTL
    |--------------------------------------------------------------------------
    |
    | Time in seconds to cache subscription status responses.
    | This reduces API calls but may delay status updates.
    |
    */

    'cache_ttl' => env('PORTAL_CACHE_TTL', 300), // 5 minutes

    /*
    |--------------------------------------------------------------------------
    | Fetch Limits from Portal
    |--------------------------------------------------------------------------
    |
    | Whether to fetch package limits from the Portal API.
    | If false, uses local default limits from middleware.
    |
    */

    'fetch_limits_from_portal' => env('PORTAL_FETCH_LIMITS', false),

    /*
    |--------------------------------------------------------------------------
    | Renewal URL
    |--------------------------------------------------------------------------
    |
    | URL where users are redirected to renew their subscription.
    |
    */

    'renewal_url' => env('PORTAL_RENEWAL_URL', env('PORTAL_BASE_URL', 'https://portal.stawihr.com') . '/renew/subscription'),

    /*
    |--------------------------------------------------------------------------
    | Upgrade URL
    |--------------------------------------------------------------------------
    |
    | URL where users are redirected to upgrade their package.
    |
    */

    'upgrade_url' => env('PORTAL_UPGRADE_URL', env('PORTAL_BASE_URL', 'https://portal.stawihr.com') . '/packages'),

    /*
    |--------------------------------------------------------------------------
    | Support URL
    |--------------------------------------------------------------------------
    |
    | URL where users can contact support.
    |
    */

    'support_url' => env('PORTAL_SUPPORT_URL', env('PORTAL_BASE_URL', 'https://portal.stawihr.com') . '/contact'),

    /*
    |--------------------------------------------------------------------------
    | Default Domain
    |--------------------------------------------------------------------------
    |
    | Default company domain for testing or single-tenant setups.
    |
    */

    'default_domain' => env('PORTAL_DEFAULT_DOMAIN', null),

    /*
    |--------------------------------------------------------------------------
    | Strict Mode
    |--------------------------------------------------------------------------
    |
    | If true, blocks all access when Portal is unreachable.
    | If false, allows access with warning when Portal is down.
    |
    */

    'strict_mode' => env('PORTAL_STRICT_MODE', false),
];
