<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Helpers\ProgressHelper;
use App\Models\Payroll\EmployeePayroll;

class ProcessBulkPayrollSubmission implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $ids;
    protected $batchId;

    public function __construct($ids, $batchId)
    {
        $this->ids = $ids;
        $this->batchId = $batchId;
    }

    public function handle()
    {
        $processed = 0;

        foreach ($this->ids as $id) {
            try {
                $payroll = EmployeePayroll::find($id);
                if ($payroll) {
                    // Your submission logic here
                    $payroll->submitForApprovalWithBatch($this->batchId);
                }

                $processed++;
                ProgressHelper::updateProgress($this->batchId, $processed);

                // Small delay to simulate processing (remove in production)
                usleep(500000); // 0.5 seconds

            } catch (\Exception $e) {
                \Log::error("Error processing payroll {$id}: " . $e->getMessage());
            }
        }

        ProgressHelper::completeProgress($this->batchId);
    }
}
