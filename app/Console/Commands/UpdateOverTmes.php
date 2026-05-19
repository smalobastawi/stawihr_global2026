<?php

namespace App\Console\Commands;

use App\Http\Controllers\Api\ApiAttendanceController;
use Illuminate\Console\Command;
use Carbon\Carbon;

class UpdateOverTmes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update_over_times';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update Overtimes';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $daysToSynch = env('LOCAL_PER_DAYS_TO_SYNCH');

        for ($i = 0; $i <= $daysToSynch; $i++)
        {
        $date = Carbon::now()->subDays($i)->format('Y-m-d');
        $updateOverTimes = ApiAttendanceController::update_overtimes_for_date($date);
        $this->info('Overtime update response: date: '.$date .' '.$updateOverTimes);

        }
        return Command::SUCCESS;
    }
}
