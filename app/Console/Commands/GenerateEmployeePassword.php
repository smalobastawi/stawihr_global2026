<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class GenerateEmployeePassword extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'employee:generate-password';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate a secure 8-character password for all users with the Employee role';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // Fetch all users with the "Employee" role
        $users = User::role('Employee')->get();

        if ($users->isEmpty()) {
            $this->error('No Employee users found.');
            return;
        }

        foreach ($users as $user) {
            // Generate an 8-character password with uppercase, lowercase, numbers, and symbols
            $newPassword = Str::upper(Str::random(2)) . Str::random(2) . rand(10, 99) . Str::random(2);
            
            // Update the user's password
            $user->update(['password' => Hash::make($newPassword)]);

            $employee = $user->employeeDetails();

            $this->info("Generated password for {$user->email}: $newPassword");
        }

        return Command::SUCCESS;
    }
}
