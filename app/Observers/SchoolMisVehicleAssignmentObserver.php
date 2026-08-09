<?php

namespace App\Observers;

use App\Models\Vehicle\VehicleAssignment;
use App\Services\Integrations\HrVehicleSyncService;
use App\Services\Integrations\SyncQuiet;
use Illuminate\Support\Facades\Log;

class SchoolMisVehicleAssignmentObserver
{
    public function __construct(private readonly HrVehicleSyncService $sync) {}

    public function saved(VehicleAssignment $assignment): void
    {
        if (SyncQuiet::running()) {
            return;
        }

        $vehicle = $assignment->vehicle;
        if (! $vehicle) {
            return;
        }

        try {
            $this->sync->pushVehicle($vehicle);
        } catch (\Throwable $e) {
            Log::warning('School MIS vehicle assignment push failed', [
                'assignment_id' => $assignment->id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
