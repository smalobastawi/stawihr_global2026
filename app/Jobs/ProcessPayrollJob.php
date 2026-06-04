<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Services\Payroll\PayrollCalculationServiceResolver;
use App\Models\Payroll\PayrollPeriod;
use App\Models\Employee;
use App\Lib\Enumerations\GeneralStatus;
use App\Helpers\ProgressHelper;
use App\Mail\Payroll\PayrollErrorNotification;
use App\Mail\Payroll\PayrollCompletionNotification;
use Illuminate\Support\Facades\Auth;

class ProcessPayrollJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $periodId;
    protected $batchId;
    protected $userId;
    protected $userEmail;
    protected $recalculateExisting;
    protected $companyIds;

    public function __construct($periodId, $batchId, $userId, $userEmail, $recalculateExisting = false, $companyIds = null)
    {
        $this->periodId = $periodId;
        $this->batchId = $batchId;
        $this->userId = $userId;
        $this->userEmail = $userEmail;
        $this->recalculateExisting = $recalculateExisting;
        $this->companyIds = $companyIds;
    }

    public function handle(PayrollCalculationServiceResolver $payrollCalculationResolver)
    {
        try {
            $period = PayrollPeriod::findOrFail($this->periodId);
            $errors = [];
            $successCount = 0;
            $errorCount = 0;
            $processed = 0;

            // Get active employees with payroll, optionally filtered by company
            $employeesQuery = Employee::where('status', GeneralStatus::ACTIVE)
                ->whereHas('employeePayroll', function ($query) {
                    $query->where('status', GeneralStatus::ACTIVE);
                })
                ->with('employeePayroll');

            if (!empty($this->companyIds)) {
                $employeesQuery->whereIn('company_id', $this->companyIds);
            }

            $employees = $employeesQuery->get();

            $totalEmployees = $employees->count();

            // Initialize progress
            ProgressHelper::initializeProgress($this->batchId, $totalEmployees);

            foreach ($employees as $employee) {
                try {
                    $existingRecord = \App\Models\Payroll\PayrollRecord::where('employee_id', $employee->employee_id)
                        ->where('payroll_period_id', $period->id)
                        ->where('payroll_record_status', '!=', \App\Lib\Enumerations\PayrollStatus::PAID)
                        ->withTrashed()
                        ->first();

                    // Check if payroll already approved or paid
                    if ($existingRecord && in_array($existingRecord->payroll_record_status, [\App\Lib\Enumerations\PayrollStatus::APPROVED, \App\Lib\Enumerations\PayrollStatus::PAID])) {
                        $errors[] = "Payroll for {$employee->fullName()} has already been approved or paid.";
                        $errorCount++;
                        $processed++;
                        ProgressHelper::updateProgress($this->batchId, $processed);
                        continue;
                    }

                    // Skip if not recalculating existing and record exists
                    if (!$this->recalculateExisting && $existingRecord) {
                        $processed++;
                        ProgressHelper::updateProgress($this->batchId, $processed);
                        continue;
                    }

                    // Calculate payroll using the company's payroll country rules
                    $payrollService = $payrollCalculationResolver->resolveForEmployee($employee);
                    $payrollRecord = $payrollService->calculateEmployeePayroll($employee->employeePayroll, $period);
                    $successCount++;
                    $processed++;

                    // Update progress
                    ProgressHelper::updateProgress($this->batchId, $processed);
                } catch (\Exception $e) {
                    Log::error("Payroll calculation failed for Employee ID {$employee->id}: " . $e->getMessage());
                    $errors[] = "Employee {$employee->fullName()}: " . $e->getMessage();
                    $errorCount++;
                    $processed++;
                    ProgressHelper::updateProgress($this->batchId, $processed);
                }
            }

            // Complete progress
            ProgressHelper::completeProgress($this->batchId);

            // Send completion notification
            $this->sendCompletionNotification($successCount, $errorCount, $errors);

            // Send error notification if there were errors
            if (!empty($errors)) {
                $this->sendErrorNotification($errors);
            }
        } catch (\Exception $e) {
            Log::error('Payroll processing job failed: ' . $e->getMessage());

            // Send error notification
            $this->sendErrorNotification(['System Error: ' . $e->getMessage()]);

            // Mark progress as failed
            $progress = ProgressHelper::getProgress($this->batchId);
            if ($progress) {
                $progress['status'] = 'failed';
                $progress['error'] = $e->getMessage();
                \Illuminate\Support\Facades\Cache::put("progress_{$this->batchId}", $progress, now()->addHours(2));
            }
        }
    }

    private function sendCompletionNotification($successCount, $errorCount, $errors)
    {
        try {
            $message = "Payroll processing completed. {$successCount} employees processed successfully.";
            if ($errorCount > 0) {
                $message .= " {$errorCount} errors occurred.";
            }

            Mail::to($this->userEmail)->send(new PayrollCompletionNotification(
                $message,
                $successCount,
                $errorCount,
                $errors
            ));
        } catch (\Exception $e) {
            Log::error('Failed to send completion notification: ' . $e->getMessage());
        }
    }

    private function sendErrorNotification($errors)
    {
        try {
            Mail::to($this->userEmail)->send(new PayrollErrorNotification($errors));
        } catch (\Exception $e) {
            Log::error('Failed to send error notification: ' . $e->getMessage());
        }
    }
}