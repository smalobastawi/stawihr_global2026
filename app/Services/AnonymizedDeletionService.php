<?php

namespace App\Services;

use App\Lib\Enumerations\GeneralStatus;
use App\Models\AnonymizedRecordBackup;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class AnonymizedDeletionService
{
    private const USER_PII_FIELDS = [
        'user_name',
        'email',
        'msisdn',
        'google_id',
        'token',
        'google_access_token',
        'refresh_token',
        'verification_code',
        'google_ids',
    ];

    private const EMPLOYEE_PII_FIELDS = [
        'email',
        'first_name',
        'last_name',
        'middle_name',
        'phone',
        'personal_email',
        'work_email',
        'national_id',
        'address',
        'emergency_name',
        'emergency_phone',
        'emergency_contacts',
        'driving_license_number',
        'KRA_Pin',
        'NSSF_no',
        'NHIF_no',
        'shif_number',
        'bank_account_number',
        'bank_account_name',
        'staff_no',
        'payroll_number',
        'biometric_user_id',
    ];

    public function anonymizeUser(User $user): void
    {
        if ($this->hasActiveBackup($user->id)) {
            throw new \RuntimeException('User is already anonymized.');
        }

        if ($user->id === Auth::id()) {
            throw new \RuntimeException('You cannot delete your own account.');
        }

        DB::transaction(function () use ($user) {
            $employee = Employee::withTrashed()->where('user_id', $user->id)->first();
            $roleNames = $user->roles()->pluck('name')->toArray();
            $userFields = $this->existingPiiFields($user, self::USER_PII_FIELDS);
            $employeeFields = $employee
                ? $this->existingPiiFields($employee, self::EMPLOYEE_PII_FIELDS)
                : [];

            $userSnapshot = $this->snapshotFields($user, $userFields);
            $employeeSnapshot = $employee
                ? $this->snapshotFields($employee, $employeeFields)
                : null;

            AnonymizedRecordBackup::create([
                'user_id' => $user->id,
                'employee_id' => $employee?->employee_id,
                'user_data' => $userSnapshot,
                'employee_data' => $employeeSnapshot,
                'role_names' => $roleNames,
                'anonymized_by' => Auth::id(),
                'anonymized_at' => now(),
            ]);

            $this->applyUserAnonymization($user, $userFields);
            $user->syncRoles([]);

            if ($employee) {
                $this->applyEmployeeAnonymization($employee, $employeeFields);
                $employee->status = GeneralStatus::INACTIVE;
                $employee->save();
                $employee->delete();
            }

            $user->status = GeneralStatus::INACTIVE;
            $user->updated_by = Auth::id();
            $user->save();
            $user->delete();
        });
    }

    public function anonymizeEmployee(Employee $employee): void
    {
        if (!$employee->user_id) {
            throw new \RuntimeException('Employee has no linked user account.');
        }

        $user = User::withTrashed()->findOrFail($employee->user_id);
        $this->anonymizeUser($user);
    }

    public function restoreEmployee(int $employeeId): void
    {
        $backup = AnonymizedRecordBackup::restorable()
            ->where('employee_id', $employeeId)
            ->latest('id')
            ->firstOrFail();

        $this->restoreUser($backup->user_id);
    }

    public function hasRestorableBackupByEmployee(int $employeeId): bool
    {
        return AnonymizedRecordBackup::restorable()
            ->where('employee_id', $employeeId)
            ->exists();
    }

    public function restoreUser(int $userId): void
    {
        $backup = AnonymizedRecordBackup::restorable()
            ->where('user_id', $userId)
            ->latest('id')
            ->firstOrFail();

        DB::transaction(function () use ($backup) {
            $user = User::withTrashed()->findOrFail($backup->user_id);
            $this->assertUniqueValuesAvailable($backup->user_data, $user->id);

            $user->restore();
            $this->fillExistingFields($user, $backup->user_data);
            $user->status = GeneralStatus::ACTIVE;
            $user->updated_by = Auth::id();
            $user->save();

            if (!empty($backup->role_names)) {
                $user->syncRoles($backup->role_names);
            }

            if ($backup->employee_id && $backup->employee_data) {
                $employee = Employee::withTrashed()->findOrFail($backup->employee_id);
                $this->assertEmployeeUniqueValuesAvailable($backup->employee_data, $employee->employee_id);

                $employee->restore();
                $this->fillExistingFields($employee, $backup->employee_data);
                $employee->status = GeneralStatus::ACTIVE;
                $employee->updated_by = Auth::id();
                $employee->save();
            }

            $backup->update([
                'restored_by' => Auth::id(),
                'restored_at' => now(),
            ]);
        });
    }

    public function hasRestorableBackup(int $userId): bool
    {
        return AnonymizedRecordBackup::restorable()
            ->where('user_id', $userId)
            ->exists();
    }

    private function hasActiveBackup(int $userId): bool
    {
        return $this->hasRestorableBackup($userId);
    }

    private function existingPiiFields($model, array $fields): array
    {
        $columns = Schema::getColumnListing($model->getTable());

        return array_values(array_intersect($fields, $columns));
    }

    private function snapshotFields($model, array $fields): array
    {
        $snapshot = [];
        foreach ($fields as $field) {
            $snapshot[$field] = $model->{$field};
        }

        return $snapshot;
    }

    private function fillExistingFields($model, array $data): void
    {
        $columns = Schema::getColumnListing($model->getTable());
        $filtered = array_intersect_key($data, array_flip($columns));
        $model->fill($filtered);
    }

    private function applyUserAnonymization(User $user, array $fields): void
    {
        $suffix = $user->id . '.' . now()->timestamp . '.' . Str::lower(Str::random(6));

        $values = [
            'user_name' => 'deleted_user_' . $suffix,
            'email' => 'deleted.user.' . $suffix . '@anonymized.stawihr.local',
            'msisdn' => null,
            'google_id' => null,
            'token' => null,
            'google_access_token' => null,
            'refresh_token' => null,
            'verification_code' => null,
            'google_ids' => null,
            'password' => Str::random(64),
        ];

        foreach ($fields as $field) {
            if (array_key_exists($field, $values)) {
                $user->{$field} = $values[$field];
            }
        }
    }

    private function applyEmployeeAnonymization(Employee $employee, array $fields): void
    {
        $suffix = $employee->employee_id . '.' . now()->timestamp . '.' . Str::lower(Str::random(6));

        $values = [
            'email' => 'deleted.employee.' . $suffix . '@anonymized.stawihr.local',
            'first_name' => 'Deleted',
            'last_name' => 'Employee',
            'middle_name' => null,
            'phone' => null,
            'personal_email' => null,
            'work_email' => null,
            'national_id' => 'DELETED_' . $suffix,
            'address' => null,
            'emergency_name' => null,
            'emergency_phone' => null,
            'emergency_contacts' => null,
            'driving_license_number' => null,
            'KRA_Pin' => null,
            'NSSF_no' => null,
            'NHIF_no' => null,
            'shif_number' => null,
            'bank_account_number' => null,
            'bank_account_name' => null,
            'staff_no' => 'DELETED_' . $suffix,
            'payroll_number' => null,
            'biometric_user_id' => null,
        ];

        foreach ($fields as $field) {
            if (array_key_exists($field, $values)) {
                $employee->{$field} = $values[$field];
            }
        }
    }

    private function assertUniqueValuesAvailable(array $userData, int $ignoreUserId): void
    {
        if (!empty($userData['email']) && User::where('email', $userData['email'])->where('id', '!=', $ignoreUserId)->exists()) {
            throw new \RuntimeException('The original email is already in use by another account.');
        }

        if (!empty($userData['user_name']) && User::where('user_name', $userData['user_name'])->where('id', '!=', $ignoreUserId)->exists()) {
            throw new \RuntimeException('The original username is already in use by another account.');
        }
    }

    private function assertEmployeeUniqueValuesAvailable(array $employeeData, int $ignoreEmployeeId): void
    {
        if (!empty($employeeData['email']) && Employee::where('email', $employeeData['email'])->where('employee_id', '!=', $ignoreEmployeeId)->exists()) {
            throw new \RuntimeException('The original employee email is already in use.');
        }

        if (!empty($employeeData['staff_no']) && Employee::where('staff_no', $employeeData['staff_no'])->where('employee_id', '!=', $ignoreEmployeeId)->exists()) {
            throw new \RuntimeException('The original staff number is already in use.');
        }
    }
}
