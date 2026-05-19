<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\LeaveApplication;
use App\Models\Employee;
use App\Lib\Enumerations\LeaveStatus;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AdjustStateMourningLeave extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'leave:adjust-state-mourning {--date=2025-10-17} {--dry-run}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add back one leave day for employees who were on approved leave during State Mourning Holiday (17/10/2025)';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $stateMourningDate = $this->option('date');
        $dryRun = $this->option('dry-run');

        $this->info("Checking for employees on approved leave on {$stateMourningDate}...");
        
        if ($dryRun) {
            $this->warn("DRY RUN MODE - No changes will be made");
        }

        // Check if adjustments have already been made for this date
        $existingAdjustments = DB::table('leave_adjustments')
            ->where('reason', 'State Mourning Holiday - ' . $stateMourningDate)
            ->pluck('leave_application_id')
            ->toArray();

        if (!empty($existingAdjustments) && !$dryRun) {
            $this->warn("Note: " . count($existingAdjustments) . " leave application(s) have already been adjusted for this date and will be skipped.");
        }

        // Find all approved leave applications that include the state mourning date
        // Exclude those that have already been adjusted
        $affectedLeaves = LeaveApplication::where('final_status', LeaveStatus::APPROVE)
            ->where('application_from_date', '<=', $stateMourningDate)
            ->where('application_to_date', '>=', $stateMourningDate)
            ->whereNotIn('leave_application_id', $existingAdjustments)
            ->with('employee', 'leaveType')
            ->get();

        if ($affectedLeaves->isEmpty()) {
            $this->info("No employees found on approved leave on {$stateMourningDate}");
            return 0;
        }

        $this->info("Found {$affectedLeaves->count()} leave application(s) affected");
        $this->newLine();

        $adjustedCount = 0;
        $errors = [];

        foreach ($affectedLeaves as $leave) {
            $employee = $leave->employee;
            $leaveType = $leave->leaveType;
            
            $this->line("Employee: {$employee->first_name} {$employee->last_name} ({$employee->employee_code})");
            $this->line("Leave Type: {$leaveType->leave_type_name}");
            $this->line("Leave Period: {$leave->application_from_date} to {$leave->application_to_date}");
            $this->line("Original Days: {$leave->number_of_day}");
            
            if (!$dryRun) {
                try {
                    DB::beginTransaction();
                    
                    // Reduce the leave days by 1
                    $originalDays = $leave->number_of_day;
                    $newDays = max(0, $leave->number_of_day - 1);
                    $leave->number_of_day = $newDays;
                    $leave->save();
                    
                    // Record the adjustment in leave_adjustments table to prevent duplicate adjustments
                    DB::table('leave_adjustments')->insert([
                        'employee_id' => $employee->employee_id,
                        'leave_type_id' => $leave->leave_type_id,
                        'leave_application_id' => $leave->leave_application_id,
                        'adjustment_days' => 1,
                        'reason' => 'State Mourning Holiday - ' . $stateMourningDate,
                        'adjustment_date' => $stateMourningDate,
                        'adjusted_by' => 1, // System adjustment
                        'notes' => "Adjusted from {$originalDays} to {$newDays} days. Public holiday declared during approved leave period.",
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                    
                    // Log the adjustment
                    Log::info("State Mourning Leave Adjustment", [
                        'employee_id' => $employee->employee_id,
                        'employee_name' => $employee->first_name . ' ' . $employee->last_name,
                        'leave_application_id' => $leave->leave_application_id,
                        'original_days' => $originalDays,
                        'adjusted_days' => $newDays,
                        'state_mourning_date' => $stateMourningDate,
                        'adjusted_at' => now()
                    ]);
                    
                    DB::commit();
                    
                    $this->info("✓ Adjusted to: {$newDays} days");
                    $adjustedCount++;
                    
                } catch (\Exception $e) {
                    DB::rollBack();
                    $error = "Error adjusting leave for {$employee->employee_code}: " . $e->getMessage();
                    $this->error($error);
                    $errors[] = $error;
                    Log::error($error);
                }
            } else {
                $newDays = max(0, $leave->number_of_day - 1);
                $this->comment("Would adjust to: {$newDays} days");
                $adjustedCount++;
            }
            
            $this->newLine();
        }

        $this->newLine();
        if ($dryRun) {
            $this->info("DRY RUN COMPLETE: {$adjustedCount} leave application(s) would be adjusted");
            $this->comment("Run without --dry-run to apply changes");
        } else {
            $this->info("ADJUSTMENT COMPLETE: {$adjustedCount} leave application(s) adjusted successfully");
            
            if (!empty($errors)) {
                $this->error("Errors encountered: " . count($errors));
                foreach ($errors as $error) {
                    $this->line("  - {$error}");
                }
            }
        }

        return 0;
    }
}
