<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class LinkUsersToEmployeesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * This seeder links existing users to employees based on matching email addresses.
     * It updates the employee.user_id field to match the user.id where emails match.
     *
     * @return void
     */
    public function run()
    {
        $this->command->info('Starting to link users to employees by email...');

        // Get all users
        $users = DB::table('user')->get();
        $linkedCount = 0;
        $skippedCount = 0;
        $notFoundCount = 0;

        foreach ($users as $user) {
            // Check if user already has an employee record linked by user_id
            $existingLinkedEmployee = DB::table('employee')
                ->where('user_id', $user->id)
                ->first();

            if ($existingLinkedEmployee) {
                $this->command->info("User {$user->email} (ID: {$user->id}) is already linked to employee {$existingLinkedEmployee->employee_id}");
                $skippedCount++;
                continue;
            }

            // Find employee with matching email
            $employee = DB::table('employee')
                ->where('email', $user->email)
                ->first();

            if ($employee) {
                // Check if this employee is already linked to a different user
                if ($employee->user_id && $employee->user_id != $user->id) {
                    $this->command->warn("Employee {$employee->email} (ID: {$employee->employee_id}) is already linked to a different user (ID: {$employee->user_id}). Skipping...");
                    $skippedCount++;
                    continue;
                }

                // Link the employee to the user
                DB::table('employee')
                    ->where('employee_id', $employee->employee_id)
                    ->update(['user_id' => $user->id]);

                $this->command->info("Linked user {$user->email} (ID: {$user->id}) to employee {$employee->employee_id}");
                $linkedCount++;
            } else {
                $this->command->warn("No employee found with email: {$user->email} for user ID: {$user->id}");
                $notFoundCount++;
            }
        }

        // Summary
        $this->command->info('----------------------------------------');
        $this->command->info('Linking process completed!');
        $this->command->info("Total users processed: {$users->count()}");
        $this->command->info("Successfully linked: {$linkedCount}");
        $this->command->info("Already linked/skipped: {$skippedCount}");
        $this->command->info("No matching employee found: {$notFoundCount}");
        $this->command->info('----------------------------------------');

        // Show unlinked users
        $unlinkedUsers = DB::table('user')
            ->leftJoin('employee', 'user.id', '=', 'employee.user_id')
            ->whereNull('employee.user_id')
            ->select('user.id', 'user.email', 'user.user_name')
            ->get();

        if ($unlinkedUsers->count() > 0) {
            $this->command->warn('Users without linked employees:');
            foreach ($unlinkedUsers as $unlinkedUser) {
                $this->command->warn("  - {$unlinkedUser->user_name} ({$unlinkedUser->email}, ID: {$unlinkedUser->id})");
            }
        } else {
            $this->command->info('All users are now linked to employees!');
        }
    }
}
