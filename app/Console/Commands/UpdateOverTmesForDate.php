<?php

namespace App\Console\Commands;

use App\Http\Controllers\Api\ApiAttendanceController;
use Illuminate\Console\Command;

class UpdateOverTmesForDate extends Command
{
    //@todo add the date parameters
    protected $signature = 'update_over_times_for_date {date}';
    protected $description = 'Update Overtimes';

    public function handle()
    {
        $date = $this->argument('date');
        $updateOverTimes = ApiAttendanceController::update_overtimes_for_date($date);
        return $updateOverTimes;
       // return Command::SUCCESS;
    }
}
