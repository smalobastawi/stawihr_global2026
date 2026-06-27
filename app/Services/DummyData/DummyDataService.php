<?php

namespace App\Services\DummyData;

use App\Support\DummyData\DummyDataRegistry;

class DummyDataService
{
    public function __construct(
        private readonly DummyDataRegistry $registry,
        private readonly DummyDataGeneratorService $generator
    ) {
    }

    public function summary(): array
    {
        $batch = $this->registry->activeBatch();

        if (!$batch) {
            return [
                'has_data' => false,
                'batch' => null,
                'summary' => [],
                'counts' => [],
            ];
        }

        return [
            'has_data' => true,
            'batch' => $batch,
            'summary' => $batch->summary ?? [],
            'counts' => $this->registry->counts($batch),
        ];
    }

    public function generate(int $userId): array
    {
        return $this->generator->generate($userId);
    }

    public function remove(): void
    {
        $this->registry->removeAll();
    }
}
