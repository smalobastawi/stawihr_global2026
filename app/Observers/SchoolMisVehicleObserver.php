<?php

namespace App\Observers;

use App\Models\Vehicle\Vehicle;
use App\Services\Integrations\HrVehicleSyncService;
use App\Services\Integrations\SyncQuiet;
use Illuminate\Support\Facades\Log;

class SchoolMisVehicleObserver
{
    public function __construct(private readonly HrVehicleSyncService $sync) {}

    public function saved(Vehicle $vehicle): void
    {
        if (SyncQuiet::running()) {
            return;
        }

        try {
            $this->sync->pushVehicle($vehicle);
        } catch (\Throwable $e) {
            Log::warning('School MIS vehicle observer push failed', [
                'vehicle_id' => $vehicle->id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
