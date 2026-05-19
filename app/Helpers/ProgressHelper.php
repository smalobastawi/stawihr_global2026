<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class ProgressHelper
{
    /**
     * Initialize progress tracking for a batch
     */
    public static function initializeProgress($batchId, $totalItems)
    {
        $progressKey = "progress_{$batchId}";

        Cache::put($progressKey, [
            'total' => $totalItems,
            'processed' => 0,
            'percentage' => 0,
            'status' => 'processing',
            'started_at' => now(),
            'batch_id' => $batchId
        ], now()->addHours(2)); // Keep for 2 hours

        return $batchId;
    }

    /**
     * Update progress
     */
    public static function updateProgress($batchId, $processedItems)
    {
        $progressKey = "progress_{$batchId}";
        $progress = Cache::get($progressKey);

        if ($progress) {
            $progress['processed'] = $processedItems;
            $progress['percentage'] = round(($processedItems / $progress['total']) * 100);

            Cache::put($progressKey, $progress, now()->addHours(2));

            return $progress;
        }

        return null;
    }

    /**
     * Complete progress
     */
    public static function completeProgress($batchId)
    {
        $progressKey = "progress_{$batchId}";
        $progress = Cache::get($progressKey);

        if ($progress) {
            $progress['status'] = 'completed';
            $progress['processed'] = $progress['total'];
            $progress['percentage'] = 100;
            $progress['completed_at'] = now();

            Cache::put($progressKey, $progress, now()->addHours(2));

            return $progress;
        }

        return null;
    }

    /**
     * Get progress
     */
    public static function getProgress($batchId)
    {
        $progressKey = "progress_{$batchId}";
        return Cache::get($progressKey);
    }

    /**
     * Generate a batch ID
     */
    public static function generateBatchId()
    {
        return 'batch_' . Str::random(16) . '_' . time();
    }
}
