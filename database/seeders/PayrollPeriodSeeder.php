<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Payroll\PayrollPeriod;
use Carbon\Carbon;

class PayrollPeriodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * This seeder creates 12 monthly payroll periods for the current financial year.
     * The current period is set to the current month.
     *
     * @return void
     */
    public function run()
    {
        $currentYear = Carbon::now()->year;
        $currentMonth = Carbon::now()->month;

        // Clear existing periods for the current year to avoid duplicates
        PayrollPeriod::whereYear('start_date', $currentYear)->delete();

        $periods = [
            ['month' => 1, 'name' => 'January ' . $currentYear],
            ['month' => 2, 'name' => 'February ' . $currentYear],
            ['month' => 3, 'name' => 'March ' . $currentYear],
            ['month' => 4, 'name' => 'April ' . $currentYear],
            ['month' => 5, 'name' => 'May ' . $currentYear],
            ['month' => 6, 'name' => 'June ' . $currentYear],
            ['month' => 7, 'name' => 'July ' . $currentYear],
            ['month' => 8, 'name' => 'August ' . $currentYear],
            ['month' => 9, 'name' => 'September ' . $currentYear],
            ['month' => 10, 'name' => 'October ' . $currentYear],
            ['month' => 11, 'name' => 'November ' . $currentYear],
            ['month' => 12, 'name' => 'December ' . $currentYear],
        ];

        foreach ($periods as $index => $period) {
            $startDate = Carbon::create($currentYear, $period['month'], 1);
            $endDate = $startDate->copy()->endOfMonth();
            $payDate = $endDate->copy()->addDays(5);

            // Check if period already exists
            $exists = PayrollPeriod::where('start_date', $startDate)
                ->where('end_date', $endDate)
                ->exists();

            if (!$exists) {
                // Input period typically starts on the 26th of the previous month
                // and ends on the 25th of the current month
                $inputPeriodStart = $startDate->copy()->subMonth()->day(26);
                $inputPeriodEnd = $startDate->copy()->day(25);

                PayrollPeriod::create([
                    'name' => $period['name'],
                    'period_type' => PayrollPeriod::PERIOD_MONTHLY,
                    'start_date' => $startDate,
                    'end_date' => $endDate,
                    'pay_date' => $payDate,
                    'status' => PayrollPeriod::STATUS_OPEN,
                    'is_current' => ($period['month'] === $currentMonth),
                    'created_by' => 1,
                    'input_period_start' => $inputPeriodStart,
                    'input_period_end' => $inputPeriodEnd,
                ]);

                $this->command->info("Created payroll period: {$period['name']}");
            } else {
                $this->command->info("Skipped existing period: {$period['name']}");
            }
        }

        // Ensure the current period is properly set
        $currentPeriod = PayrollPeriod::where('month_number', $currentMonth)
            ->whereYear('start_date', $currentYear)
            ->first();

        if ($currentPeriod) {
            // Make sure it's marked as current and others are not
            PayrollPeriod::where('is_current', true)->update(['is_current' => false]);
            $currentPeriod->update(['is_current' => true]);
            $this->command->info("Set current period to: {$currentPeriod->name}");
        }

        $totalPeriods = PayrollPeriod::whereYear('start_date', $currentYear)->count();
        $this->command->info("Total payroll periods for {$currentYear}: {$totalPeriods}");
    }
}
