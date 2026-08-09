<?php

namespace App\Services\Integrations;

use App\Lib\Enumerations\LeaveStatus;
use App\Models\Employee;
use App\Models\LeaveApplication;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SchoolMisClient
{
    public function __construct(private readonly SchoolMisSettingService $settings) {}

    public function enabled(): bool
    {
        return $this->settings->current()->pushConfigured();
    }

    /**
     * @param  Collection<int, Employee>|iterable<Employee>  $employees
     * @return array{ok: bool, status?: int, body?: mixed, skipped?: bool, message?: string}
     */
    public function pushEmployees(iterable $employees, ?string $syncKey = null): array
    {
        if (! $this->enabled()) {
            return ['ok' => false, 'skipped' => true, 'message' => 'School MIS push is not configured.'];
        }

        $payload = [
            'sync_key' => $syncKey ?: 'hr-employees-'.now()->format('YmdHis'),
            'employees' => collect($employees)->map(fn (Employee $employee) => $this->serializeEmployee($employee))->values()->all(),
        ];

        return $this->post('/api/v1/integrations/hr/employees/upsert', $payload);
    }

    /**
     * @param  Collection<int, LeaveApplication>|iterable<LeaveApplication>  $leaves
     * @return array{ok: bool, status?: int, body?: mixed, skipped?: bool, message?: string}
     */
    public function pushLeaves(iterable $leaves, ?string $syncKey = null): array
    {
        if (! $this->enabled()) {
            return ['ok' => false, 'skipped' => true, 'message' => 'School MIS push is not configured.'];
        }

        $payload = [
            'sync_key' => $syncKey ?: 'hr-leave-'.now()->format('YmdHis'),
            'leaves' => collect($leaves)->map(fn (LeaveApplication $leave) => $this->serializeLeave($leave))->values()->all(),
        ];

        return $this->post('/api/v1/integrations/hr/leave/upsert', $payload);
    }

    public function pushEmployee(Employee $employee): array
    {
        return $this->pushEmployees([$employee], 'hr-employee-'.$employee->employee_id.'-'.now()->format('YmdHis'));
    }

    public function pushLeave(LeaveApplication $leave): array
    {
        return $this->pushLeaves([$leave], 'hr-leave-'.$leave->leave_application_id.'-'.now()->format('YmdHis'));
    }

    public function deactivateEmployee(int|string $externalId): array
    {
        if (! $this->enabled()) {
            return ['ok' => false, 'skipped' => true, 'message' => 'School MIS push is not configured.'];
        }

        return $this->post('/api/v1/integrations/hr/employees/'.$externalId.'/deactivate', []);
    }

    /**
     * @param  list<array<string, mixed>>  $vehicles
     * @return array{ok: bool, status?: int, body?: mixed, skipped?: bool, message?: string}
     */
    public function pushVehicles(array $vehicles, ?string $syncKey = null): array
    {
        $settings = $this->settings->current();
        if (! $settings->pushConfigured() || ! ($settings->sync_vehicles ?? true)) {
            return ['ok' => false, 'skipped' => true, 'message' => 'School MIS vehicle sync is not configured.'];
        }

        return $this->post('/api/v1/integrations/hr/vehicles/upsert', [
            'sync_key' => $syncKey ?: 'hr-vehicles-'.now()->format('YmdHis'),
            'vehicles' => array_values($vehicles),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function serializeEmployee(Employee $employee): array
    {
        $employee->loadMissing(['department:department_id,department_name', 'designation:designation_id,designation_name']);

        return [
            'external_id' => $employee->employee_id,
            'staff_no' => $employee->staff_no,
            'first_name' => $employee->first_name,
            'middle_name' => $employee->middle_name,
            'last_name' => $employee->last_name,
            'email' => $employee->email,
            'phone' => $employee->phone !== null ? (string) $employee->phone : null,
            'department' => $employee->department?->department_name,
            'designation' => $employee->designation?->designation_name,
            'status' => match ((int) $employee->status) {
                1 => 'active',
                2 => 'suspended',
                default => 'inactive',
            },
            'joined_on' => optional($employee->date_of_joining)->toDateString()
                ?? (is_string($employee->date_of_joining) ? $employee->date_of_joining : null),
            'left_on' => optional($employee->date_of_leaving)->toDateString()
                ?? (is_string($employee->date_of_leaving) ? $employee->date_of_leaving : null),
            'meta' => array_filter([
                'payroll_number' => $employee->payroll_number ?? null,
                'national_id' => $employee->national_id ?? null,
                'company_id' => $employee->company_id ?? null,
            ], fn ($value) => filled($value)),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function serializeLeave(LeaveApplication $leave): array
    {
        $leave->loadMissing([
            'leaveType:leave_type_id,leave_type_name',
            'employee:employee_id,staff_no,email',
        ]);

        $status = match ((int) $leave->final_status) {
            LeaveStatus::APPROVE => 'approved',
            LeaveStatus::REJECT => 'rejected',
            LeaveStatus::RECALL, LeaveStatus::RECALL_APPROVED => 'cancelled',
            default => 'pending',
        };

        return [
            'external_id' => $leave->leave_application_id,
            'employee_external_id' => $leave->employee_id,
            'staff_no' => $leave->employee?->staff_no,
            'email' => $leave->employee?->email,
            'leave_type' => $leave->leaveType?->leave_type_name,
            'starts_on' => optional($leave->application_from_date)->toDateString(),
            'ends_on' => optional($leave->application_to_date)->toDateString(),
            'days' => $leave->number_of_day !== null ? (float) $leave->number_of_day : null,
            'status' => $status,
            'purpose' => $leave->purpose,
            'notes' => $leave->remarks,
            'approved_on' => $leave->approve_date,
        ];
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return array{ok: bool, status?: int, body?: mixed, message?: string}
     */
    private function post(string $path, array $payload): array
    {
        $settings = $this->settings->current();
        $url = rtrim((string) $settings->resolvedSchoolBaseUrl(), '/').'/'.ltrim($path, '/');

        try {
            $response = Http::acceptJson()
                ->timeout((int) ($settings->timeout ?: config('school_mis.timeout', 30)))
                ->withToken((string) $settings->resolvedSchoolApiKey())
                ->withHeaders([
                    'X-Integration-Provider' => 'hr',
                ])
                ->post($url, $payload);

            if (! $response->successful()) {
                Log::warning('School MIS push failed', [
                    'url' => $url,
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
            }

            return [
                'ok' => $response->successful(),
                'status' => $response->status(),
                'body' => $response->json() ?? $response->body(),
            ];
        } catch (\Throwable $e) {
            Log::error('School MIS push exception', [
                'url' => $url,
                'error' => $e->getMessage(),
            ]);

            return [
                'ok' => false,
                'message' => $e->getMessage(),
            ];
        }
    }
}
