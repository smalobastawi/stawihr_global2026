<?php

namespace App\Http\Controllers\Api\Internal;

use App\Http\Controllers\Controller;
use App\Models\PortalSubscriptionStatus;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SuspensionController extends Controller
{
    public function show(Request $request): JsonResponse
    {
        $record = PortalSubscriptionStatus::current();

        return response()->json([
            'success' => true,
            'data' => $this->formatRecord($record),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'domain' => 'nullable|string|max:255',
            'portal_subscription_id' => 'nullable|integer',
            'reason' => 'required|string|min:10|max:2000',
            'support_email' => 'nullable|email|max:255',
            'support_phone' => 'nullable|string|max:50',
            'suspended_at' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $record = PortalSubscriptionStatus::applySuspension($validator->validated());

        return response()->json([
            'success' => true,
            'message' => 'Subscription suspension saved locally.',
            'data' => $this->formatRecord($record),
        ]);
    }

    public function destroy(Request $request): JsonResponse
    {
        $record = PortalSubscriptionStatus::clearSuspension();

        return response()->json([
            'success' => true,
            'message' => 'Subscription suspension cleared locally.',
            'data' => $this->formatRecord($record),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    protected function formatRecord(PortalSubscriptionStatus $record): array
    {
        return [
            'is_suspended' => $record->is_suspended,
            'reason' => $record->reason,
            'support_email' => $record->support_email,
            'support_phone' => $record->support_phone,
            'portal_subscription_id' => $record->portal_subscription_id,
            'domain' => $record->domain,
            'suspended_at' => $record->suspended_at?->toIso8601String(),
            'unsuspended_at' => $record->unsuspended_at?->toIso8601String(),
            'updated_at' => $record->updated_at?->toIso8601String(),
        ];
    }
}
