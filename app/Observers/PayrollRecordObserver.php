<?php

namespace App\Observers;

use App\Models\Payroll\PayrollRecord;
use App\Notifications\ApprovalRequiredNotification;

class PayrollRecordObserver
{
    public function updated(PayrollRecord $payrollRecord)
    {
        if ($payrollRecord->isDirty('status') && $payrollRecord->status === 'pending_approval') {
            $approvers = $payrollRecord->getCurrentApprovers();
            foreach ($approvers as $approver) {
                $approver->notify(new ApprovalRequiredNotification($payrollRecord));
            }
        }
    }
}