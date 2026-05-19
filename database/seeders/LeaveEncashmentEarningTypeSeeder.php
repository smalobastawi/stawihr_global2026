<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\PayrollEarningTypes;

class LeaveEncashmentEarningTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Leave Encashment earning type
        PayrollEarningTypes::updateOrCreate(
            ['name' => 'Leave Encashment'],
            [
                'description' => 'Payment for encashed leave days',
                'taxable' => true,
                'is_pensionable' => true,
                'is_recurring' => false,
                'calculation_type' => 'daily_rate',
                'default_amount' => 0,
                'status' => 1,
            ]
        );

        $this->command->info('Leave Encashment earning type created successfully!');
    }
}
