<?php

namespace App\Observers;

use App\Models\Employee;
use App\Services\Integrations\SchoolMisClient;
use App\Services\Integrations\SchoolMisSettingService;
use Illuminate\Support\Facades\Log;

class SchoolMisEmployeeObserver
{
    public function __construct(
        private readonly SchoolMisClient $schoolMis,
        private readonly SchoolMisSettingService $settings,
    ) {}

    public function saved(Employee $employee): void
    {
        if (! $this->settings->current()->push_on_employee_save || ! $this->schoolMis->enabled()) {
            return;
        }

        try {
            $result = $this->schoolMis->pushEmployee($employee);

            if (((int) $employee->status !== 1 || filled($employee->date_of_leaving)) && ($result['ok'] ?? false)) {
                $this->schoolMis->deactivateEmployee($employee->employee_id);
            }

            if (! ($result['ok'] ?? false) && empty($result['skipped'])) {
                Log::warning('School MIS employee push returned non-success', [
                    'employee_id' => $employee->employee_id,
                    'result' => $result,
                ]);
            }
        } catch (\Throwable $e) {
            Log::error('School MIS employee push failed', [
                'employee_id' => $employee->employee_id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
