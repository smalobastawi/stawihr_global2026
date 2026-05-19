<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ViewStateMourningAdjustments extends Command
{
    protected $signature = 'leave:view-state-mourning-adjustments {--date=2025-10-17}';
    protected $description = 'View all State Mourning Holiday leave adjustments that have been made';

    public function handle()
    {
        $stateMourningDate = $this->option('date');
        
        $adjustments = DB::table('leave_adjustments')
            ->join('employee', 'leave_adjustments.employee_id', '=', 'employee.employee_id')
            ->join('leave_type', 'leave_adjustments.leave_type_id', '=', 'leave_type.leave_type_id')
            ->where('leave_adjustments.reason', 'State Mourning Holiday - ' . $stateMourningDate)
            ->select(
                'employee.employee_code',
                'employee.first_name',
                'employee.last_name',
                'leave_type.leave_type_name',
                'leave_adjustments.adjustment_days',
                'leave_adjustments.notes',
                'leave_adjustments.created_at'
            )
            ->get();

        if ($adjustments->isEmpty()) {
            $this->info("No adjustments found for {$stateMourningDate}");
            return 0;
        }

        $this->info("State Mourning Holiday Adjustments for {$stateMourningDate}");
        $this->info(str_repeat("=", 70));
        $this->newLine();

        foreach ($adjustments as $adjustment) {
            $this->line("Employee: {$adjustment->first_name} {$adjustment->last_name} ({$adjustment->employee_code})");
            $this->line("Leave Type: {$adjustment->leave_type_name}");
            $this->line("Adjustment: +{$adjustment->adjustment_days} day(s) credited back");
            $this->line("Notes: {$adjustment->notes}");
            $this->line("Adjusted At: {$adjustment->created_at}");
            $this->newLine();
        }

        $this->info("Total Adjustments: " . $adjustments->count());

        return 0;
    }
}
