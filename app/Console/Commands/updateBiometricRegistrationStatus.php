<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\Api\Biotime_EmployeeController;
class updateBiometricRegistrationStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:api-update-biometric-registration-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        if (!helper_isBiometricEnabled()) {
            $this->info('Biometric integration is disabled. Skipping biometric registration update.');
            return Command::SUCCESS;
        }

        $updateBiometricStatus1 = new Biotime_EmployeeController();
        $updateBiometricStatus = $updateBiometricStatus1->updateBiometricCaptureStatus();

        if ($updateBiometricStatus) {
            $this->info('Biometric registration status updated successfully');
        } else {
            $this->error('Failed to update biometric registration status');
        }
        return Command::SUCCESS;
    }
}
