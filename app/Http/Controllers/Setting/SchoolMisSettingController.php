<?php

namespace App\Http\Controllers\Setting;

use App\Http\Controllers\Controller;
use App\Services\Integrations\SchoolMisClient;
use App\Services\Integrations\SchoolMisSettingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Http;
use Illuminate\View\View;
use Throwable;

class SchoolMisSettingController extends Controller
{
    public function __construct(
        private readonly SchoolMisSettingService $settings,
        private readonly SchoolMisClient $schoolMis,
    ) {}

    public function index(): View
    {
        $settings = $this->settings->current();

        return view('admin.setting.school_mis.index', [
            'settings' => $settings,
            'plainPullToken' => session('plain_pull_token'),
            'internalSyncBase' => url('/api/internal/sync'),
            'healthUrl' => url('/api/internal/health'),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'school_base_url' => ['nullable', 'url', 'max:500'],
            'school_api_key' => ['nullable', 'string', 'max:500'],
            'timeout' => ['nullable', 'integer', 'min:5', 'max:120'],
        ]);

        $result = $this->settings->update([
            'is_enabled' => $request->boolean('is_enabled'),
            'school_base_url' => $validated['school_base_url'] ?? null,
            'school_api_key' => $validated['school_api_key'] ?? null,
            'push_on_employee_save' => $request->boolean('push_on_employee_save'),
            'push_on_leave_approve' => $request->boolean('push_on_leave_approve'),
            'sync_vehicles' => $request->boolean('sync_vehicles'),
            'push_on_vehicle_change' => $request->boolean('push_on_vehicle_change'),
            'timeout' => $validated['timeout'] ?? 30,
        ]);

        $redirect = redirect()
            ->route('schoolMisSettings.index')
            ->with('success', 'School MIS integration settings saved.');

        if (filled($result['plain_pull_token'])) {
            $redirect->with('plain_pull_token', $result['plain_pull_token'])
                ->with('success', 'School MIS integration enabled. Copy the API key now — it will not be shown again. Paste it into StawiSMS as the HR remote system token.');
        }

        return $redirect;
    }

    public function toggle(Request $request): RedirectResponse
    {
        $settings = $this->settings->current();
        $result = $this->settings->setEnabled(! $settings->is_enabled);

        $redirect = redirect()->route('schoolMisSettings.index');

        if ($result['settings']->is_enabled) {
            $redirect->with('success', 'School MIS integration enabled.');
            if (filled($result['plain_pull_token'])) {
                $redirect->with('plain_pull_token', $result['plain_pull_token'])
                    ->with('success', 'School MIS integration enabled. Copy the API key now — paste it into StawiSMS as the HR remote system token.');
            }
        } else {
            $redirect->with('success', 'School MIS integration disabled. School pull and HR push are paused.');
        }

        return $redirect;
    }

    public function generateKey(Request $request): RedirectResponse
    {
        $plain = $this->settings->generatePullApiToken();

        return redirect()
            ->route('schoolMisSettings.index')
            ->with('plain_pull_token', $plain)
            ->with('success', 'New API key generated. Copy it now — it will not be shown again. Paste it into StawiSMS Integrations as the remote system token.');
    }

    public function revokeKey(Request $request): RedirectResponse
    {
        $this->settings->revokePullApiToken();

        return redirect()
            ->route('schoolMisSettings.index')
            ->with('success', 'API key revoked. StawiSMS can no longer pull with the previous key.');
    }

    public function testConnection(Request $request): RedirectResponse
    {
        $settings = $this->settings->current();

        if (! $settings->pushConfigured()) {
            return back()->with('error', 'Enable the integration and set School base URL + School API key before testing.');
        }

        try {
            $url = $settings->resolvedSchoolBaseUrl().'/api/v1/integrations/hr/health';
            $response = Http::acceptJson()
                ->timeout((int) ($settings->timeout ?: 30))
                ->withToken((string) $settings->resolvedSchoolApiKey())
                ->withHeaders(['X-Integration-Provider' => 'hr'])
                ->get($url);

            if ($response->successful()) {
                return back()->with('success', 'Connected to StawiSMS health endpoint (HTTP '.$response->status().').');
            }

            return back()->with('error', 'StawiSMS responded HTTP '.$response->status().': '.$response->body());
        } catch (Throwable $e) {
            return back()->with('error', 'Connection failed: '.$e->getMessage());
        }
    }

    public function pushNow(Request $request): RedirectResponse
    {
        if (! $this->schoolMis->enabled()) {
            return back()->with('error', 'School MIS push is not configured. Enable the integration and set School base URL + API key.');
        }

        try {
            Artisan::call('school-mis:push');
            $output = trim(Artisan::output());

            return back()->with('success', 'Push completed. '.$output);
        } catch (Throwable $e) {
            return back()->with('error', 'Push failed: '.$e->getMessage());
        }
    }
}
