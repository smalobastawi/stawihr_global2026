<?php

namespace App\Services\Integrations;

use App\Models\SchoolMisSetting;
use App\Services\ModuleActivationService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class VehicleSyncAccess
{
    public const CACHE_KEY = 'school_mis.vehicle_sync.peer_capability';

    public const MODULE_NAME = 'Vehicle Management';

    public function __construct(private readonly ModuleActivationService $modules) {}

    public function settings(): SchoolMisSetting
    {
        return SchoolMisSetting::current();
    }

    public function syncConfigured(): bool
    {
        $settings = $this->settings();

        return $settings->pushConfigured()
            && (bool) ($settings->sync_vehicles ?? true);
    }

    public function localModuleEnabled(): bool
    {
        return $this->modules->isEnabled(self::MODULE_NAME);
    }

    public function remoteModuleEnabled(): ?bool
    {
        $cached = Cache::get(self::CACHE_KEY);
        if (! is_array($cached) || ! array_key_exists('vehicles_module_enabled', $cached)) {
            return null;
        }

        return (bool) $cached['vehicles_module_enabled'];
    }

    public function canView(): bool
    {
        if ($this->localModuleEnabled()) {
            return true;
        }

        return $this->syncConfigured() && ($this->remoteModuleEnabled() ?? true);
    }

    public function canWrite(): bool
    {
        return $this->localModuleEnabled();
    }

    public function isViewOnly(): bool
    {
        return $this->canView() && ! $this->canWrite();
    }

    public function shouldPushOutbound(): bool
    {
        $settings = $this->settings();

        return $this->canWrite()
            && $settings->pushConfigured()
            && (bool) ($settings->sync_vehicles ?? true)
            && (bool) ($settings->push_on_vehicle_change ?? true);
    }

    /**
     * @return array{vehicles_module_enabled: bool, sync_vehicles: bool, can_write_vehicles: bool, ok: bool}
     */
    public function localCapability(): array
    {
        $settings = $this->settings();

        return [
            'ok' => true,
            'vehicles_module_enabled' => $this->localModuleEnabled(),
            'sync_vehicles' => (bool) ($settings->sync_vehicles ?? true),
            'can_write_vehicles' => $this->canWrite(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function refreshPeerCapability(): array
    {
        $settings = $this->settings();
        if (! $settings->pushConfigured()) {
            Cache::forget(self::CACHE_KEY);

            return ['ok' => false, 'message' => 'School MIS integration is not configured.'];
        }

        try {
            $url = $settings->resolvedSchoolBaseUrl().'/api/v1/integrations/hr/health';
            $response = Http::acceptJson()
                ->timeout((int) ($settings->timeout ?: 30))
                ->withToken((string) $settings->resolvedSchoolApiKey())
                ->withHeaders(['X-Integration-Provider' => 'hr'])
                ->get($url);

            if (! $response->successful()) {
                return ['ok' => false, 'status' => $response->status()];
            }

            $payload = $response->json('data') ?? [];
            $capability = [
                'ok' => true,
                'vehicles_module_enabled' => (bool) ($payload['vehicles_module_enabled'] ?? false),
                'sync_vehicles' => (bool) ($payload['sync_vehicles'] ?? false),
                'can_write_vehicles' => (bool) ($payload['can_write_vehicles'] ?? false),
                'checked_at' => now()->toIso8601String(),
            ];
            Cache::put(self::CACHE_KEY, $capability, now()->addMinutes(10));

            return $capability;
        } catch (\Throwable $e) {
            return ['ok' => false, 'message' => $e->getMessage()];
        }
    }
}
