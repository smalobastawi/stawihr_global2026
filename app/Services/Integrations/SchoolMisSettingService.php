<?php

namespace App\Services\Integrations;

use App\Models\SchoolMisSetting;
use Illuminate\Support\Str;

class SchoolMisSettingService
{
    public function current(): SchoolMisSetting
    {
        return SchoolMisSetting::current();
    }

    /**
     * @param  array{
     *     is_enabled?: bool,
     *     school_base_url?: ?string,
     *     school_api_key?: ?string,
     *     push_on_employee_save?: bool,
     *     push_on_leave_approve?: bool,
     *     sync_vehicles?: bool,
     *     push_on_vehicle_change?: bool,
     *     timeout?: int
     * }  $data
     * @return array{settings: SchoolMisSetting, plain_pull_token: ?string}
     */
    public function update(array $data): array
    {
        $settings = $this->current();
        $wasEnabled = (bool) $settings->is_enabled;
        $plainPullToken = null;

        $payload = [
            'is_enabled' => (bool) ($data['is_enabled'] ?? $settings->is_enabled),
            'school_base_url' => array_key_exists('school_base_url', $data)
                ? ($data['school_base_url'] ?: null)
                : $settings->school_base_url,
            'push_on_employee_save' => (bool) ($data['push_on_employee_save'] ?? $settings->push_on_employee_save),
            'push_on_leave_approve' => (bool) ($data['push_on_leave_approve'] ?? $settings->push_on_leave_approve),
            'sync_vehicles' => (bool) ($data['sync_vehicles'] ?? $settings->sync_vehicles ?? true),
            'push_on_vehicle_change' => (bool) ($data['push_on_vehicle_change'] ?? $settings->push_on_vehicle_change ?? true),
            'timeout' => max(5, min(120, (int) ($data['timeout'] ?? $settings->timeout ?? 30))),
        ];

        if (array_key_exists('school_api_key', $data)) {
            $key = $data['school_api_key'];
            if (filled($key) && ! str_contains((string) $key, '•')) {
                $payload['school_api_key'] = $key;
            }
        }

        $settings->fill($payload)->save();

        // Activating without a pull token auto-generates one for the school system.
        if ($settings->is_enabled && ! $wasEnabled && ! $settings->hasPullApiToken()) {
            $plainPullToken = $this->generatePullApiToken($settings);
        }

        return [
            'settings' => $settings->fresh(),
            'plain_pull_token' => $plainPullToken,
        ];
    }

    public function setEnabled(bool $enabled): array
    {
        return $this->update(['is_enabled' => $enabled]);
    }

    /**
     * Generate / rotate the token StawiSMS stores as the HR outbound token.
     */
    public function generatePullApiToken(?SchoolMisSetting $settings = null): string
    {
        $settings ??= $this->current();
        $plain = 'stwihr_'.Str::lower(bin2hex(random_bytes(32)));

        $settings->forceFill([
            'pull_api_token_hash' => $this->hashToken($plain),
            'pull_api_token_hint' => $this->hint($plain),
            'pull_api_token_generated_at' => now(),
            'pull_api_token_revoked_at' => null,
        ])->save();

        return $plain;
    }

    public function revokePullApiToken(?SchoolMisSetting $settings = null): SchoolMisSetting
    {
        $settings ??= $this->current();

        $settings->forceFill([
            'pull_api_token_hash' => null,
            'pull_api_token_hint' => null,
            'pull_api_token_revoked_at' => now(),
        ])->save();

        return $settings->fresh();
    }

    public function tokenMatches(string $plainToken, ?SchoolMisSetting $settings = null): bool
    {
        $plainToken = trim($plainToken);
        if ($plainToken === '') {
            return false;
        }

        $settings ??= $this->current();

        if ($settings->hasPullApiToken()) {
            return hash_equals((string) $settings->pull_api_token_hash, $this->hashToken($plainToken));
        }

        // Backward-compatible env token until an admin generates a UI key.
        $envToken = (string) (config('portal.internal_api_token') ?: '');

        return $envToken !== '' && hash_equals($envToken, $plainToken);
    }

    private function hashToken(string $plain): string
    {
        return hash('sha256', $plain);
    }

    private function hint(string $plain): string
    {
        if (strlen($plain) <= 16) {
            return Str::mask($plain, '•', 4, -4);
        }

        return substr($plain, 0, 10).'…'.substr($plain, -4);
    }
}
