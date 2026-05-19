<?php

namespace App\Jobs;

use App\Models\Employee;
use App\Models\Payroll\PayrollRecord;
use App\Models\PayrollRecordDetail;
use App\Models\Payroll\PayrollPeriod;
use App\Lib\Enumerations\GeneralStatus;
use App\Lib\Enumerations\PayrollStatus;

use App\Models\Payroll\PayrollRecordDetail as PayrollPayrollRecordDetail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class RecalculatePayrollJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $period;
    protected $recalculateExisting;

    /**
     * Create a new job instance.
     */
    public function __construct(PayrollPeriod $period, bool $recalculateExisting = false)
    {
        $this->period = $period;
        $this->recalculateExisting = $recalculateExisting;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info("RecalculatePayrollJob started for period ID: {$this->period->id}");

        $employeesWithPayroll = Employee::where('status', GeneralStatus::ACTIVE)
            ->whereHas('employeePayroll', function ($query) {
                $query->where('status', GeneralStatus::ACTIVE);
            })
            ->with('employeePayroll')
            ->get();

        $results = [];

        // Recalculate existing payrolls if requested
        if ($this->recalculateExisting) {
            $recordIds = PayrollRecord::where('payroll_record_status', '!=', PayrollStatus::PAID)
                ->where('payroll_period_id', $this->period->id)
                ->withTrashed()
                ->pluck('id');

            if ($recordIds->isNotEmpty()) {
                PayrollPayrollRecordDetail::whereIn('payroll_record_id', $recordIds)->delete();
                PayrollRecord::whereIn('id', $recordIds)->forceDelete();
                Log::info("Deleted existing payroll records for period ID {$this->period->id}");
            }
        }

        // Process each employee
        foreach ($employeesWithPayroll as $employee) {
            try {
                $existingRecord = PayrollRecord::where('employee_id', $employee->employee_id)
                    ->where('payroll_record_status', '!=', PayrollStatus::PAID)
                    ->where('payroll_period_id', $this->period->id)
                    ->withTrashed()
                    ->first();

                // Skip approved/paid records
                if ($existingRecord && in_array($existingRecord->payroll_record_status, [PayrollStatus::APPROVED, PayrollStatus::PAID])) {
                    $results[] = [
                        'employee_id' => $employee->id,
                        'status' => 'skipped',
                        'message' => 'Payroll already approved or paid for this period.',
                    ];
                    continue;
                }

                if (!$this->recalculateExisting && $existingRecord) {
                    continue;
                }

                // Reuse your existing payroll calculation logic
                $payrollRecord = app('App\Http\Controllers\YourPayrollController')
                    ->calculateEmployeePayroll($employee->employeePayroll, $this->period);

                $results[] = [
                    'employee_id' => $employee->id,
                    'status' => 'success',
                    'payroll_record_id' => $payrollRecord->id
                ];
            } catch (\Exception $e) {
                Log::error("Payroll calculation failed for Employee ID {$employee->id}: {$e->getMessage()}");
                $results[] = [
                    'employee_id' => $employee->id,
                    'status' => 'error',
                    'message' => $e->getMessage(),
                ];
            }
        }

        Log::info("RecalculatePayrollJob finished for period ID: {$this->period->id}");
    }
}
