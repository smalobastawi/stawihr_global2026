<?php

namespace App\Services\Integrations;

use App\Lib\Enumerations\GeneralStatus;
use App\Models\Employee;
use App\Models\Vehicle\Vehicle;
use App\Models\Vehicle\VehicleAssignment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class HrVehicleSyncService
{
    public function __construct(
        private readonly SchoolMisClient $client,
        private readonly VehicleSyncAccess $access,
    ) {}

    /**
     * @return list<array<string, mixed>>
     */
    public function serializeAll(): array
    {
        return Vehicle::query()
            ->with(['currentAssignment.employee'])
            ->orderBy('id')
            ->get()
            ->map(fn (Vehicle $vehicle) => $this->serializeVehicle($vehicle))
            ->values()
            ->all();
    }

    /**
     * @return array<string, mixed>
     */
    public function serializeVehicle(Vehicle $vehicle): array
    {
        $vehicle->loadMissing(['currentAssignment.employee']);
        $assignment = $vehicle->currentAssignment;
        $employee = $assignment?->employee;

        return [
            'external_id' => $vehicle->id,
            'hr_vehicle_id' => $vehicle->id,
            'school_vehicle_id' => $vehicle->school_vehicle_id,
            'registration_number' => $vehicle->registration_number,
            'name' => trim(($vehicle->make ?: '').' '.($vehicle->model ?: '')) ?: null,
            'make' => $vehicle->make,
            'model' => $vehicle->model,
            'vehicle_type' => 'other',
            'capacity' => 14,
            'status' => $vehicle->status,
            'is_active' => (int) $vehicle->status === GeneralStatus::ACTIVE,
            'notes' => $vehicle->remarks,
            'driver_name' => $employee?->full_name ?? ($employee ? trim($employee->first_name.' '.$employee->last_name) : null),
            'driver_phone' => $employee?->phone,
            'current_assignment' => $assignment ? [
                'employee_external_id' => $assignment->employee_id,
                'employee_id' => $assignment->employee_id,
                'staff_no' => $employee?->staff_no,
                'email' => $employee?->email,
                'phone' => $employee?->phone,
                'employee_name' => $employee?->full_name ?? ($employee ? trim($employee->first_name.' '.$employee->last_name) : null),
                'assigned_from' => optional($assignment->assigned_from)->toDateString(),
            ] : null,
        ];
    }

    public function pushVehicle(Vehicle $vehicle): void
    {
        if (SyncQuiet::running() || ! $this->access->shouldPushOutbound()) {
            return;
        }

        $result = $this->client->pushVehicles([$this->serializeVehicle($vehicle)]);
        if (! ($result['ok'] ?? false) && empty($result['skipped'])) {
            Log::warning('School MIS vehicle push failed', ['vehicle_id' => $vehicle->id, 'result' => $result]);
        }
    }

    /**
     * @param  list<array<string, mixed>>  $vehicles
     * @return array{created:int,updated:int,skipped:int,errors:list<array<string,mixed>>}
     */
    public function upsertFromSchool(array $vehicles): array
    {
        $summary = ['created' => 0, 'updated' => 0, 'skipped' => 0, 'errors' => []];

        SyncQuiet::run(function () use ($vehicles, &$summary) {
            foreach ($vehicles as $index => $payload) {
                try {
                    $result = $this->upsertOne($payload);
                    $summary[$result]++;
                } catch (\Throwable $e) {
                    $summary['skipped']++;
                    $summary['errors'][] = [
                        'index' => $index,
                        'registration_number' => $payload['registration_number'] ?? null,
                        'message' => $e->getMessage(),
                    ];
                }
            }
        });

        return $summary;
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    private function upsertOne(array $payload): string
    {
        $registration = strtoupper(trim((string) ($payload['registration_number'] ?? '')));
        if ($registration === '') {
            throw new \InvalidArgumentException('registration_number is required.');
        }

        $schoolId = $payload['school_vehicle_id'] ?? $payload['external_id'] ?? null;

        $vehicle = null;
        if (filled($schoolId)) {
            $vehicle = Vehicle::query()->where('school_vehicle_id', $schoolId)->first();
        }
        $vehicle ??= Vehicle::query()->whereRaw('UPPER(registration_number) = ?', [$registration])->first();

        $status = $this->mapStatus($payload['status'] ?? 1);
        $name = trim((string) ($payload['name'] ?? ''));
        [$make, $model] = $this->splitName($name, $payload['make'] ?? null, $payload['model'] ?? null);

        $attributes = [
            'registration_number' => $registration,
            'make' => $make,
            'model' => $model,
            'status' => $status,
            'remarks' => $payload['notes'] ?? $payload['remarks'] ?? null,
            'school_vehicle_id' => $schoolId ? (int) $schoolId : $vehicle?->school_vehicle_id,
            'sync_origin' => 'school',
            'last_synced_at' => now(),
            'updated_by' => Auth::id(),
        ];

        return DB::transaction(function () use ($vehicle, $attributes, $payload) {
            if ($vehicle) {
                $vehicle->fill($attributes)->save();
            } else {
                $attributes['created_by'] = Auth::id();
                $vehicle = Vehicle::query()->create($attributes);
            }

            $this->syncAssignment($vehicle, $payload['current_assignment'] ?? null);

            return isset($attributes['created_by']) ? 'created' : 'updated';
        });
    }

    /**
     * @param  array<string, mixed>|null  $assignment
     */
    private function syncAssignment(Vehicle $vehicle, ?array $assignment): void
    {
        $employeeId = $assignment['employee_external_id']
            ?? $assignment['employee_id']
            ?? null;

        $employee = null;
        if (filled($employeeId)) {
            $employee = Employee::query()->where('employee_id', $employeeId)->first();
        }
        if (! $employee && filled($assignment['staff_no'] ?? null)) {
            $employee = Employee::query()->where('staff_no', $assignment['staff_no'])->first();
        }
        if (! $employee && filled($assignment['email'] ?? null)) {
            $employee = Employee::query()->whereRaw('LOWER(email) = ?', [strtolower((string) $assignment['email'])])->first();
        }

        $current = $vehicle->getCurrentAssignment();

        if (! $employee) {
            if ($current) {
                $current->update([
                    'assigned_to' => now()->toDateString(),
                    'return_reason' => 'Synced unassigned from school',
                    'returned_at' => now(),
                    'returned_by' => Auth::id(),
                ]);
            }

            return;
        }

        if ($current && (int) $current->employee_id === (int) $employee->employee_id) {
            return;
        }

        if ($current) {
            $current->update([
                'assigned_to' => now()->toDateString(),
                'return_reason' => 'Reassigned via school sync',
                'returned_at' => now(),
                'returned_by' => Auth::id(),
            ]);
        }

        VehicleAssignment::query()->create([
            'vehicle_id' => $vehicle->id,
            'employee_id' => $employee->employee_id,
            'assigned_from' => $assignment['assigned_from'] ?? now()->toDateString(),
            'assignment_reason' => 'Synced from school',
            'assigned_by' => Auth::id(),
            'company_id' => $vehicle->company_id,
            'created_by' => Auth::id(),
        ]);
    }

    private function mapStatus(mixed $status): int
    {
        if (is_numeric($status)) {
            return (int) $status;
        }

        return match (strtolower((string) $status)) {
            'active' => GeneralStatus::ACTIVE,
            'maintenance', 'suspended' => GeneralStatus::SUSPENDED,
            default => GeneralStatus::INACTIVE,
        };
    }

    /**
     * @return array{0:?string,1:?string}
     */
    private function splitName(string $name, mixed $make, mixed $model): array
    {
        if (filled($make) || filled($model)) {
            return [$make ? (string) $make : null, $model ? (string) $model : null];
        }

        if ($name === '') {
            return [null, null];
        }

        $parts = preg_split('/\s+/', $name, 2) ?: [];

        return [$parts[0] ?? $name, $parts[1] ?? null];
    }
}
