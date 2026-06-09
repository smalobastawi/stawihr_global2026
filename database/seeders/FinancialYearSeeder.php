<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\FinancialYear;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class FinancialYearSeeder extends Seeder
{
    public function run()
    {
        $companyId = Company::orderBy('id')->value('id');
        if (!$companyId) {
            $this->command->warn('No company found. Skipping financial year seeding.');

            return;
        }

        $currentYear = Carbon::now()->year;

        FinancialYear::withoutGlobalScopes()
            ->where('company_id', $companyId)
            ->whereYear('start_date', $currentYear)
            ->whereYear('end_date', $currentYear)
            ->delete();

        FinancialYear::withoutGlobalScopes()->create([
            'company_id' => $companyId,
            'name' => (string) $currentYear,
            'description' => "Financial Year {$currentYear}",
            'status' => 1,
            'start_date' => Carbon::create($currentYear, 1, 1),
            'end_date' => Carbon::create($currentYear, 12, 31),
            'created_by' => 1,
            'updated_by' => 1,
            'uuid' => (string) Str::uuid(),
        ]);

        $this->command->info("Financial year {$currentYear} created for company #{$companyId}.");
    }
}
