<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\FinancialYear;
use Carbon\Carbon;

class FinancialYearSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $currentYear = Carbon::now()->year;

        // Delete any existing financial years for current year to avoid unique constraint issues
        FinancialYear::whereYear('start_date', $currentYear)
            ->whereYear('end_date', $currentYear)
            ->delete();

        FinancialYear::create([
            'name' => $currentYear,
            'description' => "Financial Year {$currentYear}",
            'status' => 1, // Active
            'start_date' => Carbon::create($currentYear, 1, 1),
            'end_date' => Carbon::create($currentYear, 12, 31),
            'created_by' => 1, // Assuming admin user ID
            'updated_by' => 1,
            'uuid' => (string) \Illuminate\Support\Str::uuid(),
        ]);

        $this->command->info("Financial year {$currentYear} created successfully.");
    }
}