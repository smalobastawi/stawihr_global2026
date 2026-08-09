<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class SchoolMisSetting extends Model
{
    protected $fillable = [
        'is_enabled',
        'school_base_url',
        'school_api_key',
        'pull_api_token_hash',
        'pull_api_token_hint',
        'pull_api_token_generated_at',
        'pull_api_token_revoked_at',
        'push_on_employee_save',
        'push_on_leave_approve',
        'sync_vehicles',
        'push_on_vehicle_change',
        'timeout',
    ];

    protected $casts = [
        'is_enabled' => 'boolean',
        'school_api_key' => 'encrypted',
        'push_on_employee_save' => 'boolean',
        'push_on_leave_approve' => 'boolean',
        'sync_vehicles' => 'boolean',
        'push_on_vehicle_change' => 'boolean',
        'timeout' => 'integer',
        'pull_api_token_generated_at' => 'datetime',
        'pull_api_token_revoked_at' => 'datetime',
    ];

    /**
     * Singleton settings row (safe during migrate when the table is missing).
     */
    public static function current(): self
    {
        if (! Schema::hasTable('school_mis_settings')) {
            return new self([
                'is_enabled' => (bool) config('school_mis.enabled', false),
                'school_base_url' => config('school_mis.base_url'),
                'school_api_key' => config('school_mis.api_key'),
                'push_on_employee_save' => (bool) config('school_mis.push_on_employee_save', true),
                'push_on_leave_approve' => (bool) config('school_mis.push_on_leave_approve', true),
                'sync_vehicles' => true,
                'push_on_vehicle_change' => true,
                'timeout' => (int) config('school_mis.timeout', 30),
            ]);
        }

        $settings = self::query()->first();

        if (! $settings) {
            $settings = self::query()->create([
                'is_enabled' => (bool) config('school_mis.enabled', false),
                'school_base_url' => config('school_mis.base_url') ?: null,
                'school_api_key' => filled(config('school_mis.api_key'))
                    ? (string) config('school_mis.api_key')
                    : null,
                'push_on_employee_save' => (bool) config('school_mis.push_on_employee_save', true),
                'push_on_leave_approve' => (bool) config('school_mis.push_on_leave_approve', true),
                'sync_vehicles' => true,
                'push_on_vehicle_change' => true,
                'timeout' => (int) config('school_mis.timeout', 30),
            ]);
        }

        return $settings;
    }

    public function hasPullApiToken(): bool
    {
        return filled($this->pull_api_token_hash) && $this->pull_api_token_revoked_at === null;
    }

    public function acceptsPullRequests(): bool
    {
        return (bool) $this->is_enabled && $this->hasPullApiToken();
    }

    public function pushConfigured(): bool
    {
        return (bool) $this->is_enabled
            && filled($this->resolvedSchoolBaseUrl())
            && filled($this->resolvedSchoolApiKey());
    }

    public function resolvedSchoolBaseUrl(): ?string
    {
        $url = $this->school_base_url ?: config('school_mis.base_url');

        return filled($url) ? rtrim((string) $url, '/') : null;
    }

    public function resolvedSchoolApiKey(): ?string
    {
        $key = $this->school_api_key ?: config('school_mis.api_key');

        return filled($key) ? (string) $key : null;
    }
}
