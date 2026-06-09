<?php

namespace App\Http\Controllers\Setting;

use App\Http\Controllers\Controller;
use App\Models\Module;
use App\Services\ModuleActivationService;
use Illuminate\Http\Request;

class ModuleSettingsController extends Controller
{
    public function __construct(
        private readonly ModuleActivationService $moduleActivation
    ) {
    }

    public function index()
    {
        $modules = $this->moduleActivation->manageableModules();

        return view('admin.setting.module_settings.index', compact('modules'));
    }

    public function update(Request $request)
    {
        $enabledIds = collect($request->input('enabled_modules', []))
            ->map(fn ($id) => (int) $id)
            ->filter()
            ->all();

        $modules = $this->moduleActivation->manageableModules();

        foreach ($modules as $module) {
            $module->update([
                'is_enabled' => in_array($module->id, $enabledIds, true),
            ]);
        }

        $this->moduleActivation->invalidateSessionCache();
        $this->moduleActivation->refreshSession();

        return redirect()
            ->route('moduleSettings.index')
            ->with('success', 'Module settings updated successfully.');
    }
}
