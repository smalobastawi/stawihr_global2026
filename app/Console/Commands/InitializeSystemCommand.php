<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class InitializeSystemCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'stawihr:init';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Initialize the StawiHR system by running migrations, seeders, and setting up permissions';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting StawiHR system initialization...');

        // Run migrations
        $this->info('Running database migrations...');
        $this->call('migrate:fresh');

        // Run seeders
        $this->info('Seeding database...');
        $this->call('db:seed');

        // Run permission creation command
        $this->info('Creating route permissions...');
        $this->call('permission:create-permission-routes');

        // Assign SuperAdmin role to the user with support@stawitech.com email
        $this->info('Assigning SuperAdmin role to support@stawitech.com...');
        $this->assignSuperAdminRole();

        $this->info('StawiHR system initialization completed successfully!');
    }

    private function assignSuperAdminRole()
    {
        $user = \App\Models\User::where('email', 'support@stawitech.com')->first();
        if ($user) {
            $role = \App\Models\Role::where('name', 'SuperAdmin')->first();
            if ($role) {
                $user->role_id = $role->id;
                $user->save();
                $this->info('SuperAdmin role assigned successfully.');
            } else {
                $this->error('SuperAdmin role not found.');
            }
        } else {
            $this->error('User with email support@stawitech.com not found.');
        }
    }
}
