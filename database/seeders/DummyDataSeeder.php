<?php

namespace Database\Seeders;

use App\Services\DummyData\DummyDataService;
use Illuminate\Database\Seeder;

class DummyDataSeeder extends Seeder
{
    public function run(): void
    {
        /** @var DummyDataService $service */
        $service = app(DummyDataService::class);

        if ($service->summary()['has_data']) {
            $this->command?->warn('Dummy data already exists. Skipping DummyDataSeeder.');
            return;
        }

        $result = $service->generate(1);
        $summary = $result['summary'];

        $this->command?->info(sprintf(
            'Dummy data created: %d employees, %d payroll records.',
            $summary['employees'] ?? 0,
            $summary['payroll_records'] ?? 0
        ));
    }
}
