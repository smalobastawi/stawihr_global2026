<?php

namespace App\Services\DummyData;

use App\Models\DummyDataBatch;
use App\Models\DummyDataRecord;
use App\Support\DummyData\DummyDataRegistry;

class DummyDataBatchCleanup
{
    public static function removeIncompleteBatch(DummyDataRegistry $registry): void
    {
        $batch = $registry->activeBatch();

        if (!$batch) {
            return;
        }

        DummyDataRecord::query()->where('batch_id', $batch->id)->delete();
        $batch->delete();
    }
}
