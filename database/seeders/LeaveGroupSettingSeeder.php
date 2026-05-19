<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class LeaveGroupSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $time = Carbon::now();

        // Fetch leave types
        $leaveTypes = DB::table('leave_type')->pluck('leave_type_id', 'leave_type_name');

        // Fetch leave groups
        $leaveGroups = DB::table('leave_groups')->pluck('id', 'name');

        // Male Group ID
        $maleGroupId = $leaveGroups['Male Group'] ?? null;
        // Female Group ID
        $femaleGroupId = $leaveGroups['Female Group'] ?? null;

        $settings = [];

        foreach ($leaveTypes as $name => $leaveTypeId) {
            $gender = 'all';
            $groupId = null;

            // Determine gender and group based on leave type
            if ($name === 'Paternity Leave') {
                $gender = 'male';
                $groupId = $maleGroupId;
            } elseif ($name === 'Maternity Leave') {
                $gender = 'female';
                $groupId = $femaleGroupId;
            } else {
                // Common leaves for both groups
                $groupId = null; // will be handled below
            }

            // Build settings row
            $baseSetting = [
                'leave_type_id' => $leaveTypeId,
                'annual_entitlement' => 20,
                'carryover_days' => 0,
                'max_carryover_days' => null,
                'earning_rate' => 1.0,
                'gender' => $gender,
                'probation_period_days' => 0,
                'notice_period_days' => 0,
                'allow_half_day' => false,
                'paid' => true,
                'accrual_frequency' => 'once',
                'applicable_on' => 'calendar_days',
                'max_consecutive_days' => null,
                'active' => true,
                'allow_advanced_leave' => false,
                'advanced_period_months' => 1,
                'advanced_limit_days' => null,
                'created_at' => $time,
                'updated_at' => $time,
            ];

            if ($groupId) {
                // Gender-specific leave type: add to that group only
                $settings[] = array_merge($baseSetting, ['leave_group_id' => $groupId]);
            } else {
                // Common leave type: add to both groups
                if ($maleGroupId) {
                    $settings[] = array_merge($baseSetting, ['leave_group_id' => $maleGroupId]);
                }
                if ($femaleGroupId) {
                    $settings[] = array_merge($baseSetting, ['leave_group_id' => $femaleGroupId]);
                }
            }
        }

        if (!empty($settings)) {
            DB::table('leave_group_settings')->truncate();
            DB::table('leave_group_settings')->insert($settings);
        }
    }
}
