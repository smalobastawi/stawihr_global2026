<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\Api\Biotime_EmployeeController;
class BiotimeSynchEmployees extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:api-synch-upload-employees-biotime';

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
        $synchEmployees = new Biotime_EmployeeController();
        $runSycnh = $synchEmployees->uploadEmployee();
        if ($runSycnh) {
            $this->info('Employees uploaded successfully');
        } else {
            $this->error('Failed to upload employees');
        }
        $this->info('Biotime employee synchronization completed.');
        return Command::SUCCESS;
    }
}
