<?php

namespace App\Observers;

use Illuminate\Database\Eloquent\Model;
use App\Notifications\ApprovalRequiredNotification;

class ApprovableModelObserver
{
    /**
     * Handle the Model "updated" event.
     */
    public function updated(Model $model)
    {
        if ($this->requiresApprovalNotification($model)) {
            $this->notifyApprovers($model);
        }
    }

    /**
     * Handle the Model "created" event.
     */
    public function created(Model $model)
    {
        if ($this->requiresApprovalNotification($model)) {
            $this->notifyApprovers($model);
        }
    }

    /**
     * Check if the model requires approval notification
     */
    protected function requiresApprovalNotification(Model $model): bool
    {
        return method_exists($model, 'getCurrentApprovers') && 
               $model->isDirty('status') && 
               $model->status === 'pending_approval';
    }

    /**
     * Notify all approvers for the model
     */
    protected function notifyApprovers(Model $model)
    {
        $approvers = $model->getCurrentApprovers();
        
        foreach ($approvers as $approver) {
            $approver->notify(new ApprovalRequiredNotification($model, [
                'action' => 'pending_approval',
                'submitter' => $model->submitter->employeeDetails->fullName() ?? 'System'
            ]));
        }
    }


    /**
     * Handle the Model "deleted" event.
     */
    public function deleted(Model $model)
    {
        // Optionally handle deletion logic if needed
        $model->status = 'deleted';
        $model->save();
    }

}