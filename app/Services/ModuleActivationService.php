<?php

namespace App\Services;

use App\Models\Module;
use Illuminate\Support\Collection;

class ModuleActivationService
{
    public const ALWAYS_ENABLED = ['Administration', 'Settings'];

    /**
     * Sidebar pmMenu names that differ from route/DB module names.
     */
    public const MENU_ALIASES = [
        'Awards' => 'Award',
    ];

    public function refreshSession(): void
    {
        session(['enabled_module_names' => $this->loadEnabledModuleNames()->all()]);
    }

    public function invalidateSessionCache(): void
    {
        session()->forget('enabled_module_names');
    }

    public function isEnabled(?string $moduleName): bool
    {
        if ($moduleName === null || $moduleName === '') {
            return true;
        }

        $resolvedName = $this->resolveModuleName($moduleName);

        if (in_array($resolvedName, self::ALWAYS_ENABLED, true)) {
            return true;
        }

        $enabled = session('enabled_module_names');

        if ($enabled === null) {
            $this->refreshSession();
            $enabled = session('enabled_module_names', []);
        }

        return in_array($resolvedName, $enabled, true);
    }

    public function resolveModuleName(string $moduleName): string
    {
        return self::MENU_ALIASES[$moduleName] ?? $moduleName;
    }

    public function menuPermissionName(string $moduleName): string
    {
        foreach (self::MENU_ALIASES as $menuName => $dbName) {
            if ($dbName === $moduleName) {
                return $menuName;
            }
        }

        return $moduleName;
    }

    public function enabledModules(): Collection
    {
        return Module::query()
            ->where('is_enabled', true)
            ->orderBy('name')
            ->get();
    }

    public function manageableModules(): Collection
    {
        return Module::query()
            ->whereNotIn('name', self::ALWAYS_ENABLED)
            ->orderBy('name')
            ->get();
    }

    public function filterPermissionsForEnabledModules(array $permissions): array
    {
        $disabledModuleIds = Module::query()
            ->where('is_enabled', false)
            ->pluck('id', 'name');

        if ($disabledModuleIds->isEmpty()) {
            return $permissions;
        }

        return array_values(array_filter($permissions, function (string $permission) use ($disabledModuleIds) {
            foreach ($disabledModuleIds as $moduleName => $moduleId) {
                if ($permission === 'pmMenu__' . $moduleName) {
                    return false;
                }

                if ($permission === 'pmMenu__' . $this->menuPermissionName($moduleName)) {
                    return false;
                }

                if (str_starts_with($permission, 'module_id__' . $moduleId . '__')) {
                    return false;
                }
            }

            return true;
        }));
    }

    private function loadEnabledModuleNames(): Collection
    {
        return Module::query()
            ->where('is_enabled', true)
            ->pluck('name')
            ->merge(self::ALWAYS_ENABLED)
            ->unique()
            ->values();
    }
}
