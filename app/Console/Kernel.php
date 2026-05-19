<?php
/*
 * Copyright (c) 2023/9/6 sw@stawitech
 */

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */

    protected $commands = [
        Commands\sendAnomaliesReportMail::class,
    ];
    protected function schedule(Schedule $schedule)
    {

        // $schedule->command('approvals:update-approvals-requests')->everyMinute();
        //$schedule->command('app:update-attendances-locally')->everyTwoHours(); //up
        //$schedule->command('command:api-update-biometric-registration-status')->everySixHours(); //up
        // $schedule->command('command:api-synch-upload-employees-biotime')->everyTwoHours(); //up
        $schedule->command('backup:run')->dailyAt('02:00');
        //$schedule->command('update_over_times')->dailyAt('01:00'); //up
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
