<?php

namespace App\Support;

use App\Models\Company;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class CompanyContext
{
    public static function user(): ?User
    {
        return Auth::user();
    }

    public static function isSuperAdmin(): bool
    {
        $user = self::user();

        return $user ? $user->hasRole('SuperAdmin') : false;
    }

    /**
     * Company IDs the current user is allowed to access.
     */
    public static function permittedCompanyIds(): array
    {
        $user = self::user();
        if (!$user) {
            return [];
        }

        if (self::isSuperAdmin()) {
            return Company::where('status', 'active')->pluck('id')->map(fn ($id) => (int) $id)->all();
        }

        $permitted = $user->PermittedCompanies()
            ->pluck('company_id')
            ->filter()
            ->unique()
            ->map(fn ($id) => (int) $id)
            ->values()
            ->all();

        if (!empty($permitted)) {
            return $permitted;
        }

        if ($user->company_id) {
            return [(int) $user->company_id];
        }

        return [];
    }

    /**
     * Explicit company selection stored in session (header switcher).
     */
    public static function sessionCompanyId(): ?int
    {
        $id = session('active_company_id');

        if ($id === null || $id === '') {
            return null;
        }

        return (int) $id;
    }

    public static function isAllCompaniesSelected(): bool
    {
        return self::sessionCompanyId() === null;
    }

    public static function isCompanySelected(int $companyId): bool
    {
        $selectedId = self::sessionCompanyId();

        return $selectedId !== null && $selectedId === (int) $companyId;
    }

    /**
     * Company name for the header switcher label.
     */
    public static function selectedCompanyName(): string
    {
        $companyId = self::sessionCompanyId();
        if ($companyId === null) {
            return self::isSuperAdmin() ? 'All Companies (SuperAdmin)' : 'All Companies';
        }

        $fromList = self::switchableCompanies()
            ->first(fn (Company $company) => (int) $company->id === $companyId);

        return $fromList?->name
            ?? Company::find($companyId)?->name
            ?? session('active_company_name')
            ?? (self::isSuperAdmin() ? 'All Companies (SuperAdmin)' : 'All Companies');
    }

    /**
     * Whether the user is in "all companies" mode (no active company filter).
     */
    public static function isAllCompaniesMode(): bool
    {
        $user = self::user();
        if (!$user) {
            return true;
        }

        if (self::sessionCompanyId() !== null) {
            return false;
        }

        if (self::isSuperAdmin()) {
            return true;
        }

        return count(self::permittedCompanyIds()) > 1;
    }

    /**
     * Company IDs that should be applied to queries.
     * null = no filter (all permitted / all companies mode).
     */
    public static function scopedCompanyIds(): ?array
    {
        $user = self::user();
        if (!$user) {
            return null;
        }

        $permitted = self::permittedCompanyIds();
        $activeCompanyId = self::sessionCompanyId();

        if ($activeCompanyId) {
            if (empty($permitted) || in_array($activeCompanyId, $permitted, true)) {
                return [$activeCompanyId];
            }
        }

        if (self::isAllCompaniesMode()) {
            if (self::isSuperAdmin()) {
                return null;
            }

            return !empty($permitted) ? $permitted : null;
        }

        if (count($permitted) === 1) {
            return $permitted;
        }

        return !empty($permitted) ? $permitted : null;
    }

    public static function activeCompanyId(): ?int
    {
        return self::sessionCompanyId();
    }

    public static function activeCompany(): ?Company
    {
        $companyId = self::sessionCompanyId();
        if (!$companyId) {
            return null;
        }

        $permitted = self::permittedCompanyIds();
        if (!empty($permitted) && !in_array($companyId, $permitted, true)) {
            return null;
        }

        return Company::find($companyId);
    }

    public static function defaultCompanyIdForNewRecord(): ?int
    {
        return self::activeCompanyId() ?? self::user()?->company_id;
    }

    public static function canSwitchCompanies(): bool
    {
        $user = self::user();
        if (!$user) {
            return false;
        }

        if (self::isSuperAdmin()) {
            return true;
        }

        return count(self::permittedCompanyIds()) > 1;
    }

    /**
     * Companies available in the header switcher.
     */
    public static function switchableCompanies()
    {
        if (!self::canSwitchCompanies()) {
            return collect();
        }

        return Company::whereIn('id', self::permittedCompanyIds())
            ->where('status', 'active')
            ->orderBy('name')
            ->get();
    }
}
