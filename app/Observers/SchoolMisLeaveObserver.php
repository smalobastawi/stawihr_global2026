<?php

namespace App\Observers;

use App\Lib\Enumerations\LeaveStatus;
use App\Models\LeaveApplication;
use App\Services\Integrations\SchoolMisClient;
use App\Services\Integrations\SchoolMisSettingService;
use Illuminate\Support\Facades\Log;

class SchoolMisLeaveObserver
{
    public function __construct(
        private readonly SchoolMisClient $schoolMis,
        private readonly SchoolMisSettingService $settings,
    ) {}

    public function saved(LeaveApplication $leave): void
    {
        if (! $this->settings->current()->push_on_leave_approve || ! $this->schoolMis->enabled()) {
            return;
        }

        $watched = [
            LeaveStatus::APPROVE,
            LeaveStatus::REJECT,
            LeaveStatus::RECALL,
            LeaveStatus::RECALL_APPROVED,
        ];

        if (! in_array((int) $leave->final_status, $watched, true)) {
            return;
        }

        if (! $leave->wasChanged('final_status') && ! $leave->wasRecentlyCreated) {
            // Still push when date/type fields change on an already-approved leave.
            if ((int) $leave->final_status !== LeaveStatus::APPROVE) {
                return;
            }

            if (! $leave->wasChanged(['application_from_date', 'application_to_date', 'leave_type_id', 'number_of_day', 'purpose'])) {
                return;
            }
        }

        try {
            $result = $this->schoolMis->pushLeave($leave);
            if (! ($result['ok'] ?? false) && empty($result['skipped'])) {
                Log::warning('School MIS leave push returned non-success', [
                    'leave_application_id' => $leave->leave_application_id,
                    'result' => $result,
                ]);
            }
        } catch (\Throwable $e) {
            Log::error('School MIS leave push failed', [
                'leave_application_id' => $leave->leave_application_id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
