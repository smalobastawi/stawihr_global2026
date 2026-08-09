<?php

namespace App\Console\Commands;

use App\Lib\Enumerations\LeaveStatus;
use App\Models\Employee;
use App\Models\LeaveApplication;
use App\Services\Integrations\SchoolMisClient;
use Illuminate\Console\Command;

class PushSchoolMisSync extends Command
{
    protected $signature = 'school-mis:push
        {--employees : Push employees only}
        {--leave : Push approved leave only}
        {--from= : Leave window start (YYYY-MM-DD)}
        {--to= : Leave window end (YYYY-MM-DD)}';

    protected $description = 'Push employees and/or approved leave to the configured School MIS (StawiSMS) inbound API';

    public function handle(SchoolMisClient $client): int
    {
        if (! $client->enabled()) {
            $this->error('School MIS push is disabled or missing School base URL / School API key. Configure Settings → School MIS Integration.');

            return self::FAILURE;
        }

        $pushEmployees = (bool) $this->option('employees') || (! $this->option('employees') && ! $this->option('leave'));
        $pushLeave = (bool) $this->option('leave') || (! $this->option('employees') && ! $this->option('leave'));

        if ($pushEmployees) {
            $employees = Employee::query()
                ->withoutGlobalScopes()
                ->with(['department:department_id,department_name', 'designation:designation_id,designation_name'])
                ->orderBy('employee_id')
                ->get();

            $result = $client->pushEmployees($employees, 'hr-bulk-employees-'.now()->format('YmdHis'));
            $this->line('Employees push: '.json_encode($result['body']['data'] ?? $result));
            if (! ($result['ok'] ?? false)) {
                return self::FAILURE;
            }
        }

        if ($pushLeave) {
            $from = $this->option('from') ?: now()->subMonths(2)->toDateString();
            $to = $this->option('to') ?: now()->addMonths(2)->toDateString();

            $leaves = LeaveApplication::query()
                ->withoutGlobalScopes()
                ->with([
                    'leaveType:leave_type_id,leave_type_name',
                    'employee:employee_id,staff_no,email',
                ])
                ->where('final_status', LeaveStatus::APPROVE)
                ->whereDate('application_to_date', '>=', $from)
                ->whereDate('application_from_date', '<=', $to)
                ->orderBy('leave_application_id')
                ->get();

            $result = $client->pushLeaves($leaves, 'hr-bulk-leave-'.now()->format('YmdHis'));
            $this->line('Leave push: '.json_encode($result['body']['data'] ?? $result));
            if (! ($result['ok'] ?? false)) {
                return self::FAILURE;
            }
        }

        $this->info('School MIS push completed.');

        return self::SUCCESS;
    }
}
