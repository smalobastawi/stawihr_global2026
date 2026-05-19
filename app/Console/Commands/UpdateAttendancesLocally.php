<?php

namespace App\Console\Commands;
use App\Http\Controllers\Api\RemoteLogController;
use Illuminate\Console\Command;
use Carbon\Carbon;

class UpdateAttendancesLocally extends Command
{
    protected $signature = 'app:update-attendances-locally';
    protected $description = 'Command description';

    public function handle()
    {
        $daysToSynch = env('LOCAL_PER_DAYS_TO_SYNCH');
       
        for ($i = 0; $i <= $daysToSynch; $i++)
        {
            $date = Carbon::now()->subDays($i)->format('Y-m-d');
            $uploadRecords = RemoteLogController::fetchLocalRecordsFor($date);
            $this->info('attendance record response code for date: '.  $date . ':  '.$uploadRecords);

        }
    }
}
