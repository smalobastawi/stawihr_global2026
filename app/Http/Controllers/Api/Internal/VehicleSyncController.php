<?php

namespace App\Http\Controllers\Api\Internal;

use App\Http\Controllers\Controller;
use App\Services\Integrations\HrVehicleSyncService;
use App\Services\Integrations\VehicleSyncAccess;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class VehicleSyncController extends Controller
{
    public function __construct(
        private readonly HrVehicleSyncService $sync,
        private readonly VehicleSyncAccess $access,
    ) {}

    public function index(): JsonResponse
    {
        if (! ($this->access->settings()->sync_vehicles ?? true)) {
            return response()->json([
                'success' => false,
                'message' => 'Vehicle sync is disabled on this HR instance.',
            ], 403);
        }

        return response()->json([
            'success' => true,
            'data' => $this->sync->serializeAll(),
            'meta' => [
                'capability' => $this->access->localCapability(),
            ],
        ]);
    }

    public function upsert(Request $request): JsonResponse
    {
        if (! ($this->access->settings()->sync_vehicles ?? true)) {
            return response()->json([
                'success' => false,
                'message' => 'Vehicle sync is disabled on this HR instance.',
            ], 403);
        }

        $data = $request->validate([
            'vehicles' => ['required', 'array', 'min:1'],
            'vehicles.*' => ['array'],
            'sync_key' => ['nullable', 'string', 'max:120'],
        ]);

        $summary = $this->sync->upsertFromSchool($data['vehicles']);

        return response()->json([
            'success' => true,
            'data' => $summary,
            'message' => 'Vehicle upsert completed.',
        ]);
    }
}
