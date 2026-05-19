<?php

namespace App\Traits;

use App\Lib\Enumerations\ApprovalStatus;
use App\Lib\Enumerations\GeneralStatus;
use App\Lib\Enumerations\PayrollStatus;
use App\Models\Approval;
use App\Models\ApprovalLog;
use App\Models\ApprovalWorkflow;
use App\Models\ApprovalStep;
use App\Models\ApprovalAssignment;
use App\Models\User;
use App\Notifications\ApprovalActionNotification;
use App\Notifications\ApprovalRequiredNotification;
use Exception;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\ApprovalDelegation;

trait HasApprovalWorkflow
{
    /**
     * Get all approval logs for this model
     */
    public function approvalLogs(): MorphMany
    {
        return $this->morphMany(ApprovalLog::class, 'approvable');
    }

    /**
     * Get the approval workflow for this model
     */
    public function approvalWorkflow()
    {
        return ApprovalWorkflow::where('model_type', get_class($this))->first();
    }

    /**
     * Check if the model requires approval
     */
    public function requiresApproval(): bool
    {
        return $this->approvalWorkflow() !== null;
    }

    /**
     * Get the current approval step
     */
    public function currentApprovalStep()
    {
        if (!$this->requiresApproval()) {
            return null;
        }

        $completedSteps = $this->approvalLogs()
            ->where('action_date', '<>', null)
            ->whereIn('action', ['approved', 'reviewed'])
            ->pluck('approval_step_id')
            ->unique();

        // Get the first step that is not completed and has pending logs
        $pendingSteps = $this->approvalLogs()
            ->where('action', 'pending')
            ->orWhere('action', 'queued')
            ->where('action_date', null)
            ->pluck('approval_step_id')
            ->unique();

        return $this->approvalWorkflow()
            ->steps()
            ->whereIn('id', $pendingSteps)
            ->orderBy('type') // reviewers first
            ->orderBy('level')
            ->first();
    }

    /**
     * Check if the model is fully approved
     */
    public function isFullyApproved(): bool
    {
        if (!$this->requiresApproval()) {
            return true;
        }

        $requiredSteps = $this->approvalWorkflow()
            ->steps()
            ->where('is_required', true)
            ->count();

        $completedSteps = $this->approvalLogs()
            ->whereIn('action', ['approved', 'reviewed'])
            ->count();

        return $completedSteps >= $requiredSteps;
    }

    /**
     * Submit for approval
     */
    public function submitForApproval()
    {
        // Check if approval workflow exists, if not auto-approve
        if (!$this->requiresApproval()) {
            return $this->autoApprove();
        }

        $this->update(['status' => ApprovalStatus::PENDING, 'approval_status' => ApprovalStatus::PENDING, 'date_approved' => null]);

        // Get all approval steps ordered by type and level
        $allSteps = $this->approvalWorkflow()
            ->steps()
            ->orderBy('type')
            ->orderBy('level')
            ->get();

        if ($allSteps->isEmpty()) {
            throw new \Exception('No approval steps found for this workflow');
        }

        // Get the user who submitted (or system if not available)
        $submitter = auth()->user() ?? optional($this->submitter);

        // Check for existing submission log
        $existingLog = ApprovalLog::where('approvable_type', get_class($this))
            ->where('approvable_id', $this->id)
            ->where('action', 'submitted')
            ->orWhere('action', 'pending')
            ->first();

        if ($existingLog) {
            $existingLog->delete();
            //Delete the existing then create a new entry
            ApprovalLog::create([
                'approvable_type' => get_class($this),
                'approvable_id' => $this->id,
                'approval_step_id' => $allSteps->first()->id,
                'user_id' => $submitter->id ?? null,
                'action' => 'submitted',
                'comments' => 'Submitted for approval',
                'created_by' => Auth::id()
            ]);
        }

        if (!$existingLog) {
            // Create submission log for the first step
            ApprovalLog::create([
                'approvable_type' => get_class($this),
                'approvable_id' => $this->id,
                'approval_step_id' => $allSteps->first()->id,
                'user_id' => $submitter->id ?? null,
                'action' => 'submitted',
                'comments' => 'Submitted for approval',
                'created_by' => $submitter->id ?? null
            ]);
        }

        // Create logs for all approval steps
        $this->createLogsForAllSteps($allSteps);

        // Notify the first set of approvers
        try {
            $this->notifyCurrentApprovers();
        } catch (Exception $e) {
            \Log::error("Failed to send approval notifications during submission: " . $e->getMessage(), [
                'model_id' => $this->id,
                'model_type' => get_class($this)
            ]);
            // Continue without throwing - the submission was successful
        }
    }

    /**
     * Process approval action
     */
    public function processApproval($action, $comments = null)
    {
        $currentStep = $this->currentApprovalStep();

        if (!$currentStep) {
            return false;
        }

        // Remove any pending log entries for this step and user
        // Update any pending or queued log entries for this step and user
        $approvalLog = ApprovalLog::where('approvable_type', get_class($this))
            ->where('approvable_id', $this->id)
            ->where('approval_step_id', $currentStep->id)
            ->where(function ($query) {
                $query->where('action', 'pending')
                    ->orWhere('action', 'queued');
            })
            ->where('user_id', Auth::id())
            ->first();

        $updateLog = ApprovalLog::where('approvable_type', get_class($this))
            ->where('approvable_id', $this->id)
            ->where('approval_step_id', $currentStep->id)
            ->where(function ($query) {
                $query->where('action', 'pending')
                    ->orWhere('action', 'queued');
            })
            ->where('user_id', Auth::id())
            ->update([
                'action' => $action,
                'comments' => $comments,
                'action_date' => now(),
                'updated_at' => now(),
            ]);

        // Check if any records were updated
        $updatedCount = ApprovalLog::where('approvable_type', get_class($this))
            ->where('approvable_id', $this->id)
            ->where('approval_step_id', $currentStep->id)
            ->where('user_id', Auth::id())
            ->where('action', $action)
            ->count();

        // Only create a new log entry if no existing records were updated
        if ($updatedCount === 0) {
            ApprovalLog::create([
                'approvable_type' => get_class($this),
                'approvable_id' => $this->id,
                'approval_step_id' => $currentStep->id,
                'user_id' => Auth::id(),
                'action' => $action,
                'comments' => $comments,
                'created_by' => Auth::id(),
                'action_date' => now(),
            ]);
        }

        if ($action === 'rejected') {
            $this->update(['status' => GeneralStatus::INACTIVE]);
            // Notify original submitter
            $submitterID = $approvalLog->created_by;
            try {
                $this->notifySubmitter('rejected', $comments, $submitterID);
            } catch (Exception $e) {
                \Log::error("Failed to send rejection notification: " . $e->getMessage());
                // Continue - rejection was processed successfully
            }
            return true;
        }

        // Check if fully approved after this action
        if ($this->isFullyApproved()) {
            if (get_class($this) === \App\Models\Payroll\PayrollRecord::class) {
                $this->update([
                    'status' => GeneralStatus::ACTIVE,
                    'approval_status' => ApprovalStatus::APPROVED,
                    'date_approved' => now(),
                    'payroll_record_status' => PayrollStatus::APPROVED,
                ]);
            } else {
                $this->update([
                    'status' => GeneralStatus::ACTIVE,
                    'approval_status' => ApprovalStatus::APPROVED,
                    'date_approved' => now()
                ]);
            }
            $submitterID = $approvalLog->created_by;
            try {
                $this->notifySubmitter('approved', $comments, $submitterID);
                $this->cleanupPendingLogsForFullyApproved();
            } catch (Exception $e) {
                \Log::error("Failed to send approval notification: " . $e->getMessage());
                // Continue - approval was processed successfully
            }
        } else {
            // Get next step and convert queued logs to pending
            $nextStep = $this->currentApprovalStep();
            if ($nextStep) {
                $this->convertQueuedLogsToPending($nextStep);
                $this->notifyCurrentApprovers();
            }
        }

        return true;
    }

    /**
     * Create pending log entries for a step so it appears in approval lists
     */
    protected function createPendingLogForStep($step)
    {
        // Get all users assigned to this step
        $approvers = $step->assignments()->with('user')->get();

        foreach ($approvers as $assignment) {
            if ($assignment->user) {
                // Check if pending log already exists for this user and step
                $existingPending = ApprovalLog::where('approvable_type', get_class($this))
                    ->where('approvable_id', $this->id)
                    ->where('approval_step_id', $step->id)
                    ->where('user_id', $assignment->user->id)
                    ->where('action', 'pending')
                    ->first();

                if (!$existingPending) {
                    ApprovalLog::create([
                        'approvable_type' => get_class($this),
                        'approvable_id' => $this->id,
                        'approval_step_id' => $step->id,
                        'user_id' => $assignment->user->id,
                        'action' => 'pending',
                        'comments' => 'Awaiting approval action',
                        'created_by' => Auth::id()
                    ]);
                }
            }
        }
    }

    /**
     * Create logs for all approval steps - pending for first, queued for others
     */
    protected function createLogsForAllSteps($allSteps)
    {
        $isFirstStep = true;

        foreach ($allSteps as $step) {
            // Get all users assigned to this step
            $approvers = $step->assignments()->with('user')->get();

            foreach ($approvers as $assignment) {
                if ($assignment->user) {
                    // Check if log already exists for this user and step
                    $existingLog = ApprovalLog::where('approvable_type', get_class($this))
                        ->where('approvable_id', $this->id)
                        ->where('approval_step_id', $step->id)
                        ->where('user_id', $assignment->user->id)
                        ->whereIn('action', ['pending', 'queued'])
                        ->first();

                    if (!$existingLog) {
                        ApprovalLog::create([
                            'approvable_type' => get_class($this),
                            'approvable_id' => $this->id,
                            'approval_step_id' => $step->id,
                            'user_id' => $assignment->user->id,
                            'action' => $isFirstStep ? 'pending' : 'queued',
                            'comments' => $isFirstStep ? 'Awaiting approval action' : 'Queued for approval - awaiting previous level completion',
                            'created_by' => Auth::id()
                        ]);
                    }
                }
            }
            $isFirstStep = false;
        }
    }

    /**
     * Convert queued logs to pending for a specific step
     */
    protected function convertQueuedLogsToPending($step)
    {
        ApprovalLog::where('approvable_type', get_class($this))
            ->where('approvable_id', $this->id)
            ->where('approval_step_id', $step->id)
            ->where('action', 'queued')
            ->update([
                'action' => 'pending',
                'comments' => 'Awaiting approval action',
                'updated_at' => now()
            ]);
    }

    /**
     * Notify current step approvers
     */
    protected function notifyCurrentApprovers()
    {
        $currentStep = $this->currentApprovalStep();
        if ($currentStep) {
            $approvers = $currentStep->assignments()->with('user')->get()->pluck('user');

            foreach ($approvers as $approver) {
                if ($approver) {
                    $this->safeNotify(
                        $approver,
                        new ApprovalRequiredNotification($this, [
                            'submitter' => $this->submitter->name ?? 'System',
                            'current_step' => $currentStep->name,
                            'is_delegate' => false,
                        ]),
                        'notifyCurrentApprovers'
                    );
                }
            }

            // Also notify delegates of current approvers
            $this->notifyDelegatesOfCurrentApprovers($approvers, $currentStep);
        }
    }

    protected function notifySubmitter($action, $comments = null, $submitterID = null)
    {
        $submitter = User::find($submitterID);
        if ($submitter) {
            $this->safeNotify(
                $submitter,
                new ApprovalActionNotification($this, $action, $comments),
                'notifySubmitter - ' . $action
            );
        }
    }

    /**
     * Get the users who can take action on this model at the current step
     */
    public function getCurrentApprovers()
    {
        $currentStep = $this->currentApprovalStep();

        if (!$currentStep) {
            return collect();
        }

        return $currentStep->assignments()->with('user')->get()->pluck('user');
    }

    /**
     * Check if the current user can approve/review this model
     */
    public function canBeActionedBy($user): bool
    {
        return $this->getCurrentApprovers()->contains('id', $user->id);
    }

    /**
     * Get approval items pending for a user
     */
    public static function getPendingApprovalsForUser($userId, $modelType = null)
    {
        $query = ApprovalLog::where('user_id', $userId)
            ->where('action', 'pending')
            ->with(['step', 'approvable']);

        if ($modelType) {
            $query->where('approvable_type', $modelType);
        }

        return $query->get();
    }

    /**
     * Get all pending approvals for a specific model type and user
     */
    public static function getPendingApprovalsByModelType($modelType, $userId)
    {
        return ApprovalLog::where('user_id', $userId)
            ->where('action', 'pending')
            ->where('approvable_type', $modelType)
            ->with(['step', 'approvable'])
            ->get()
            ->pluck('approvable')
            ->filter(); // Remove null values
    }

    /**
     * Check if multiple models can be actioned by a user
     */
    public static function canBatchActionBy($models, $user)
    {
        $results = [];
        foreach ($models as $model) {
            $results[$model->id] = $model->canBeActionedBy($user);
        }
        return $results;
    }

    /**
     * Process batch approval for multiple models - FIXED VERSION
     */
    public static function processBatchApproval($models, $action, $comments = null, $userId = null)
    {
        $results = [];
        $userId = $userId ?? auth()->id();
        $batchId = self::generateBatchId();

        foreach ($models as $model) {
            try {
                if (!$model->canBeActionedBy(auth()->user())) {
                    $results[$model->id] = [
                        'success' => false,
                        'error' => 'Not authorized to ' . $action . ' this record'
                    ];
                    continue;
                }

                // Use processApprovalSilent to avoid individual notifications
                $model->processApprovalSilent($action, $comments);
                $results[$model->id] = [
                    'success' => true,
                    'message' => ucfirst($action) . ' successfully'
                ];
            } catch (\Exception $e) {
                $results[$model->id] = [
                    'success' => false,
                    'error' => $e->getMessage()
                ];
            }
        }

        // Send SINGLE batch notification instead of individual ones
        $successCount = count(array_filter($results, function ($result) {
            return $result['success'];
        }));

        if ($successCount > 0) {
            self::sendBatchActionNotification($models, $action, $batchId, $successCount, $comments);

            // Only send step notification if approved and there are next steps
            if ($action === 'approved') {
                self::sendBatchApprovalStepNotification($models, $batchId, $successCount);
            }
        }

        return $results;
    }

    public function processApprovalSilent($action, $comments = null)
    {
        $currentStep = $this->currentApprovalStep();

        if (!$currentStep) {
            return false;
        }

        // Update any pending or queued log entries for this step and user
        $approvalLog = ApprovalLog::where('approvable_type', get_class($this))
            ->where('approvable_id', $this->id)
            ->where('approval_step_id', $currentStep->id)
            ->where(function ($query) {
                $query->where('action', 'pending')
                    ->orWhere('action', 'queued');
            })
            ->where('user_id', Auth::id())
            ->first();

        $updateLog = ApprovalLog::where('approvable_type', get_class($this))
            ->where('approvable_id', $this->id)
            ->where('approval_step_id', $currentStep->id)
            ->where(function ($query) {
                $query->where('action', 'pending')
                    ->orWhere('action', 'queued');
            })
            ->where('user_id', Auth::id())
            ->update([
                'action' => $action,
                'comments' => $comments,
                'action_date' => now(),
                'updated_at' => now(),
            ]);

        // Check if any records were updated
        $updatedCount = ApprovalLog::where('approvable_type', get_class($this))
            ->where('approvable_id', $this->id)
            ->where('approval_step_id', $currentStep->id)
            ->where('user_id', Auth::id())
            ->where('action', $action)
            ->count();

        // Only create a new log entry if no existing records were updated
        if ($updatedCount === 0) {
            ApprovalLog::create([
                'approvable_type' => get_class($this),
                'approvable_id' => $this->id,
                'approval_step_id' => $currentStep->id,
                'user_id' => Auth::id(),
                'action' => $action,
                'comments' => $comments,
                'created_by' => Auth::id(),
                'action_date' => now(),
            ]);
        }

        if ($action === 'rejected') {
            $this->update(['status' => GeneralStatus::INACTIVE]);
            return true;
        }

        // Check if fully approved after this action
        if ($this->isFullyApproved()) {
            if (get_class($this) === \App\Models\Payroll\PayrollRecord::class) {
                $this->update([
                    'status' => GeneralStatus::ACTIVE,
                    'approval_status' => ApprovalStatus::APPROVED,
                    'date_approved' => now(),
                    'payroll_record_status' => PayrollStatus::APPROVED,
                ]);
            } else {
                $this->update([
                    'status' => GeneralStatus::ACTIVE,
                    'approval_status' => ApprovalStatus::APPROVED,
                    'date_approved' => now()
                ]);
            }
            $this->cleanupPendingLogsForFullyApproved();
        } else {
            // Get next step and convert queued logs to pending
            $nextStep = $this->currentApprovalStep();
            if ($nextStep) {
                $this->convertQueuedLogsToPending($nextStep);
                // Don't send notifications here - they'll be sent in batch
            }
        }

        return true;
    }

    /**
     * Get submitter relationship
     */
    public function submitter()
    {
        return $this->belongsTo(User::class, 'created_by') ??
            $this->belongsTo(User::class, 'submitted_by') ??
            $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Generate a unique batch ID for batch operations
     */
    public static function generateBatchId(): string
    {
        return 'batch_' . uniqid() . '_' . time();
    }

    /**
     * Submit multiple models for approval as a batch - FIXED VERSION
     */
    public static function submitForApprovalBatch($models, $batchId = null)
    {
        if (empty($models)) {
            return ['success' => false, 'message' => 'No models provided for batch submission'];
        }

        $batchId = $batchId ?? self::generateBatchId();
        $results = [];
        $successCount = 0;
        $errorCount = 0;

        foreach ($models as $model) {
            try {
                if (!method_exists($model, 'submitForApproval')) {
                    throw new \Exception('Model does not support approval workflow');
                }

                if (!$model->requiresApproval()) {
                    // Auto-approve if no approval workflow exists
                    $model->autoApprove();
                    $results[$model->id] = [
                        'success' => true,
                        'message' => 'Auto-approved (no workflow required)'
                    ];
                    $successCount++;
                    continue;
                }

                // Add batch submission ID to the model
                $model->update(['batch_submission_id' => $batchId]);

                // Submit for approval with batch tracking (silent - no individual notifications)
                $model->submitForApprovalWithBatch($batchId);

                $results[$model->id] = [
                    'success' => true,
                    'message' => 'Submitted successfully'
                ];
                $successCount++;
            } catch (\Exception $e) {
                $results[$model->id] = [
                    'success' => false,
                    'error' => $e->getMessage()
                ];
                $errorCount++;
            }
        }

        // Send SINGLE batch notification instead of individual notifications
        if ($successCount > 0) {
            self::sendBatchSubmissionNotification($models, $batchId, $successCount);
        }

        return [
            'success' => $errorCount === 0,
            'batch_id' => $batchId,
            'summary' => [
                'total' => count($models),
                'success' => $successCount,
                'errors' => $errorCount
            ],
            'results' => $results
        ];
    }

    /**
     * Submit for approval with batch tracking (silent version)
     */
    public function submitForApprovalWithBatch($batchId = null)
    {
        $this->update(['status' => ApprovalStatus::PENDING, 'approval_status' => ApprovalStatus::PENDING, 'date_approved' => null]);

        // Get all approval steps ordered by type and level
        $allSteps = $this->approvalWorkflow()
            ->steps()
            ->orderBy('type')
            ->orderBy('level')
            ->get();

        if ($allSteps->isEmpty()) {
            throw new \Exception('No approval steps found for this workflow');
        }

        // Get the user who submitted (or system if not available)
        $submitter = auth()->user() ?? optional($this->submitter);

        // Check for existing submission log
        $existingLog = ApprovalLog::where('approvable_type', get_class($this))
            ->where('approvable_id', $this->id)
            ->where('action', 'submitted')
            ->orWhere('action', 'pending')
            ->first();

        if (!$existingLog) {
            // Create submission log with batch ID for the first step
            ApprovalLog::create([
                'approvable_type' => get_class($this),
                'approvable_id' => $this->id,
                'approval_step_id' => $allSteps->first()->id,
                'user_id' => $submitter->id ?? null,
                'action' => 'submitted',
                'comments' => 'Submitted for approval' . ($batchId ? " (Batch: {$batchId})" : ''),
                'batch_id' => $batchId,
                'created_by' => Auth::id()
            ]);
        }

        // Create logs for all approval steps with batch tracking
        $this->createLogsForAllStepsWithBatch($allSteps, $batchId);
    }

    /**
     * Create logs for all approval steps with batch tracking - pending for first, queued for others
     */
    protected function createLogsForAllStepsWithBatch($allSteps, $batchId = null)
    {
        $isFirstStep = true;

        foreach ($allSteps as $step) {
            // Get all users assigned to this step
            $approvers = $step->assignments()->with('user')->get();

            foreach ($approvers as $assignment) {
                if ($assignment->user) {
                    // Check if log already exists for this user and step
                    $existingLog = ApprovalLog::where('approvable_type', get_class($this))
                        ->where('approvable_id', $this->id)
                        ->where('approval_step_id', $step->id)
                        ->where('user_id', $assignment->user->id)
                        ->whereIn('action', ['pending', 'queued', 'submitted'])
                        ->first();

                    if ($existingLog) {
                        $existingLog->delete();
                        ApprovalLog::create([
                            'approvable_type' => get_class($this),
                            'approvable_id' => $this->id,
                            'approval_step_id' => $step->id,
                            'user_id' => $assignment->user->id,
                            'action' => $isFirstStep ? 'pending' : 'queued',
                            'comments' => $isFirstStep ? 'Awaiting approval action' : 'Queued for approval - awaiting previous level completion',
                            'batch_id' => $batchId,
                            'created_by' => Auth::id()
                        ]);
                    }

                    if (!$existingLog) {
                        ApprovalLog::create([
                            'approvable_type' => get_class($this),
                            'approvable_id' => $this->id,
                            'approval_step_id' => $step->id,
                            'user_id' => $assignment->user->id,
                            'action' => $isFirstStep ? 'pending' : 'queued',
                            'comments' => $isFirstStep ? 'Awaiting approval action' : 'Queued for approval - awaiting previous level completion',
                            'batch_id' => $batchId,
                            'created_by' => Auth::id()
                        ]);
                    }
                }
            }
            $isFirstStep = false;
        }
    }

    /**
     * Process approval action with batch tracking - FIXED VERSION
     */
    public function processApprovalWithBatch($action, $comments = null, $batchId = null)
    {
        $currentStep = $this->currentApprovalStep();

        if (!$currentStep) {
            return false;
        }

        $approvalLLog = ApprovalLog::where('approvable_type', get_class($this))
            ->where('approvable_id', $this->id)
            ->where('approval_step_id', $currentStep->id)
            ->where('action', 'pending')
            ->where('user_id', Auth::id())
            ->first();

        // Try to update existing pending log entries
        $updated = ApprovalLog::where('approvable_type', get_class($this))
            ->where('approvable_id', $this->id)
            ->where('approval_step_id', $currentStep->id)
            ->where('action', 'pending')
            ->where('user_id', Auth::id())
            ->update([
                'action' => $action,
                'comments' => $comments,
                'batch_id' => $batchId,
                'action_date' => now(),
                'updated_at' => now(),
            ]);

        // If no records were updated (no pending entries existed), create a new one
        if ($updated === 0) {
            ApprovalLog::create([
                'approvable_type' => get_class($this),
                'approvable_id' => $this->id,
                'approval_step_id' => $currentStep->id,
                'user_id' => Auth::id(),
                'action' => $action,
                'comments' => $comments,
                'batch_id' => $batchId,
                'created_by' => Auth::id(),
                'action_date' => now(),
            ]);
        }

        if ($action === 'rejected') {
            $this->update(['status' => GeneralStatus::INACTIVE]);
            return true;
        }

        // Check if fully approved after this action
        if ($this->isFullyApproved()) {
            if (get_class($this) === \App\Models\Payroll\PayrollRecord::class) {
                $this->update([
                    'status' => GeneralStatus::ACTIVE,
                    'approval_status' => ApprovalStatus::APPROVED,
                    'date_approved' => now(),
                    'payroll_record_status' => 2 // Update the specific column
                ]);
            } else {
                $this->update([
                    'status' => GeneralStatus::ACTIVE,
                    'approval_status' => ApprovalStatus::APPROVED,
                    'date_approved' => now()
                ]);
            }
            $this->cleanupPendingLogsForFullyApproved();
        } else {
            // Get next step and convert queued logs to pending
            $nextStep = $this->currentApprovalStep();
            if ($nextStep) {
                $this->convertQueuedLogsToPending($nextStep);
            }
        }

        return true;
    }

    /**
     * Process batch approval/rejection for multiple models - FIXED VERSION
     */
    public static function processBatchApprovalAdvanced($models, $action, $comments = null, $userId = null)
    {
        $batchId = self::generateBatchId();
        $results = [];
        $userId = $userId ?? auth()->id();
        $successCount = 0;
        $errorCount = 0;

        foreach ($models as $model) {
            try {
                if (!$model->canBeActionedBy(auth()->user())) {
                    $results[$model->id] = [
                        'success' => false,
                        'error' => 'Not authorized to ' . $action . ' this record'
                    ];
                    $errorCount++;
                    continue;
                }

                // Use silent processing to avoid individual notifications
                $model->processApprovalSilent($action, $comments);
                $results[$model->id] = [
                    'success' => true,
                    'message' => ucfirst($action) . ' successfully'
                ];
                $successCount++;
            } catch (\Exception $e) {
                $results[$model->id] = [
                    'success' => false,
                    'error' => $e->getMessage()
                ];
                $errorCount++;
            }
        }

        // Send SINGLE batch notification instead of individual notifications
        if ($successCount > 0) {
            self::sendBatchActionNotification($models, $action, $batchId, $successCount, $comments);

            // Only send step notification if approved and there are next steps
            if ($action === 'approved') {
                self::sendBatchApprovalStepNotification($models, $batchId, $successCount);
            }
        }

        return [
            'success' => $errorCount === 0,
            'batch_id' => $batchId,
            'summary' => [
                'total' => count($models),
                'success' => $successCount,
                'errors' => $errorCount
            ],
            'results' => $results
        ];
    }

    /**
     * Send batch submission notification with fail-safe handling
     */
    protected static function sendBatchSubmissionNotification($models, $batchId, $successCount)
    {
        if (empty($models)) return;

        try {
            $firstModel = $models[0];
            $currentStep = $firstModel->currentApprovalStep();

            if ($currentStep) {
                $approvers = $currentStep->assignments()->with('user')->get()->pluck('user')->filter();

                foreach ($approvers as $approver) {
                    if ($approver) {
                        $approver->notify(new \App\Notifications\BatchApprovalRequiredNotification(
                            $models,
                            $batchId,
                            [
                                'submitter' => auth()->user()->name ?? 'System',
                                'current_step' => $currentStep->name,
                                'count' => $successCount,
                                'model_type' => class_basename($firstModel)
                            ]
                        ));
                    }
                }
            }
        } catch (Exception $e) {
            \Log::error("Failed to send batch submission notification: " . $e->getMessage(), [
                'batch_id' => $batchId,
                'model_count' => count($models),
                'success_count' => $successCount
            ]);
            // Don't rethrow - continue silently
        }
    }

    protected static function sendBatchApprovalStepNotification($models, $batchId, $successCount)
    {
        if (empty($models)) return;

        try {
            $firstModel = $models[0];
            $currentStep = $firstModel->currentApprovalStep();

            if ($currentStep) {
                $approvers = $currentStep->assignments()->with('user')->get()->pluck('user')->filter();

                foreach ($approvers as $approver) {
                    if ($approver) {
                        $approver->notify(new \App\Notifications\BatchApprovalRequiredNotificationStep(
                            $models,
                            $batchId,
                            [
                                'submitter' => auth()->user()->name ?? 'System',
                                'current_step' => $currentStep->name,
                                'count' => $successCount,
                                'model_type' => class_basename($firstModel)
                            ]
                        ));
                    }
                }
            }
        } catch (Exception $e) {
            \Log::error("Failed to send batch approval step notification: " . $e->getMessage(), [
                'batch_id' => $batchId,
                'model_count' => count($models),
                'success_count' => $successCount
            ]);
            // Don't rethrow - continue silently
        }
    }

    /**
     * Send batch action notification (single email for the batch)
     */
    protected static function sendBatchActionNotification($models, $action, $batchId, $successCount, $comments = null)
    {
        if (empty($models)) return;

        try {
            // Get unique submitters from the batch
            $submitters = collect($models)->map(function ($model) {
                return $model->submitter;
            })->filter()->unique('id');

            foreach ($submitters as $submitter) {
                if ($submitter) {
                    $submitter->notify(new \App\Notifications\BatchApprovalActionNotification(
                        $models,
                        $action,
                        $batchId,
                        [
                            'count' => $successCount,
                            'comments' => $comments,
                            'actioned_by' => auth()->user()->name ?? 'System'
                        ]
                    ));
                }
            }
        } catch (Exception $e) {
            \Log::error("Failed to send batch action notification: " . $e->getMessage(), [
                'batch_id' => $batchId,
                'action' => $action,
                'model_count' => count($models),
                'success_count' => $successCount
            ]);
            // Don't rethrow - continue silently
        }
    }

    /**
     * Get models by batch submission ID
     */
    public static function getModelsByBatchSubmissionId($batchSubmissionId)
    {
        return static::where('batch_submission_id', $batchSubmissionId)->get();
    }

    /**
     * Get approval logs by batch ID
     */
    public static function getApprovalLogsByBatchId($batchId)
    {
        return ApprovalLog::where('batch_id', $batchId)
            ->with(['approvable', 'user', 'step'])
            ->orderBy('created_at')
            ->get();
    }

    /**
     * Notify delegates of current approvers
     */
    protected function notifyDelegatesOfCurrentApprovers($approvers, $currentStep)
    {
        $delegatesToNotify = collect();

        foreach ($approvers as $approver) {
            if ($approver) {
                // Get all active delegates for this approver
                $delegations = ApprovalDelegation::active()
                    ->where('user_id', $approver->id)
                    ->where(function ($query) {
                        $query->whereNull('model_type')
                            ->orWhere('model_type', get_class($this));
                    })
                    ->get();

                foreach ($delegations as $delegation) {
                    // Check if delegation applies to this model type
                    if ($delegation->appliesToModel(get_class($this))) {
                        $delegate = $delegation->delegate;
                        if ($delegate) {
                            $delegatesToNotify->push([
                                'delegate' => $delegate,
                                'delegation' => $delegation,
                                'delegator' => $approver
                            ]);
                        }
                    }
                }
            }
        }

        // Send notifications to delegates with delegation context
        foreach ($delegatesToNotify as $delegateData) {
            $delegate = $delegateData['delegate'];
            $delegation = $delegateData['delegation'];
            $delegator = $delegateData['delegator'];

            $this->safeNotify(
                $delegate,
                new ApprovalRequiredNotification($this, [
                    'submitter' => $this->submitter->name ?? 'System',
                    'current_step' => $currentStep->name,
                    'is_delegate' => true,
                    'delegation' => $delegation,
                    'delegator' => $delegator,
                    'delegator_name' => $delegator->name,
                    'delegator_email' => $delegator->email,
                    'delegation_scope' => $this->getDelegationScopeDescription($delegation),
                    'delegation_period' => $this->getDelegationPeriodDescription($delegation),
                ]),
                'notifyDelegatesOfCurrentApprovers'
            );
        }
    }

    /**
     * Get human-readable delegation scope description
     */
    protected function getDelegationScopeDescription($delegation): string
    {
        switch ($delegation->delegation_type) {
            case 'all':
                return 'All Approval Types';
            case 'specific_model':
                $modelName = class_basename($delegation->model_type);
                return "Specific Model: {$modelName}";
            case 'specific_workflow':
                return "Specific Workflow: " . ($delegation->workflow->name ?? 'Unknown Workflow');
            default:
                return 'Unknown Scope';
        }
    }

    /**
     * Get human-readable delegation period description
     */
    protected function getDelegationPeriodDescription($delegation): string
    {
        $startDate = $delegation->start_date->format('Y-m-d');
        $endDate = $delegation->end_date?->format('Y-m-d');

        if ($endDate) {
            return "From {$startDate} to {$endDate}";
        }

        return "From {$startDate} (Indefinite)";
    }

    protected function safeNotify($notifiable, $notification, $context = '')
    {
        try {
            $notifiable->notify($notification);
            return true;
        } catch (Exception $e) {
            \Log::error("Failed to send notification {$context}: " . $e->getMessage(), [
                'notifiable_id' => $notifiable->id,
                'notifiable_type' => get_class($notifiable),
                'notification' => get_class($notification)
            ]);
            return false;
        }
    }

    public function cleanupPendingLogsForFullyApproved()
    {
        if ($this->isFullyApproved()) {
            // Delete any remaining pending or queued logs
            ApprovalLog::where('approvable_type', get_class($this))
                ->where('approvable_id', $this->id)
                ->whereIn('action', ['pending', 'queued'])
                ->delete();
        }
    }

    /**
     * Auto-approve the record when no approval workflow exists
     */
    protected function autoApprove()
    {
        $modelClass = get_class($this);

        // Set status to active and approval status to approved
        $updateData = [
            'status' => GeneralStatus::ACTIVE,
            'approval_status' => ApprovalStatus::APPROVED,
            'date_approved' => now(),
            'updated_at' => now()
        ];

        // Special handling for PayrollRecord to set payroll_record_status
        if ($modelClass === \App\Models\Payroll\PayrollRecord::class) {
            $updateData['payroll_record_status'] = PayrollStatus::APPROVED;
        }

        $this->update($updateData);

        // Log the auto-approval
        \Log::info("Record auto-approved due to no approval workflow", [
            'model_type' => $modelClass,
            'model_id' => $this->id,
            'auto_approved_at' => now()
        ]);

        return true;
    }

    public static function getDelegatesForUser($userId)
    {
        return ApprovalDelegation::active()
            ->where('user_id', $userId)
            ->with('delegate')
            ->get()
            ->pluck('delegate')
            ->unique('id');
    }

    /**
     * Check if user has delegates
     */
    public static function userHasDelegates($userId)
    {
        return ApprovalDelegation::active()
            ->where('user_id', $userId)
            ->exists();
    }

    /**
     * Get users who have delegated to the current user
     */
    public static function getDelegatedFromUsers($userId)
    {
        return ApprovalDelegation::active()
            ->where('delegate_to_user_id', $userId)
            ->with('delegator')
            ->get()
            ->pluck('delegator')
            ->unique('id');
    }

    /**
     * Get approval items for user including delegated items
     */
    public static function getPendingApprovalsForUserWithDelegates($userId, $modelType = null)
    {
        $user = User::find($userId);

        // Get user's direct approvals
        $query = ApprovalLog::where('user_id', $userId)
            ->where('action', 'pending')
            ->with(['step', 'approvable']);

        if ($modelType) {
            $query->where('approvable_type', $modelType);
        }

        $directApprovals = $query->get();

        // Get approvals where user is a delegate
        $delegatedApprovals = collect();
        $delegations = ApprovalDelegation::active()
            ->where('delegate_to_user_id', $userId)
            ->get();

        foreach ($delegations as $delegation) {
            $delegatedQuery = ApprovalLog::where('user_id', $delegation->user_id)
                ->where('action', 'pending')
                ->with(['step', 'approvable']);

            if ($delegation->model_type) {
                $delegatedQuery->where('approvable_type', $delegation->model_type);
            }

            if ($modelType && $delegation->appliesToModel($modelType)) {
                $delegatedQuery->where('approvable_type', $modelType);
            }

            $items = $delegatedQuery->get();

            // Mark as delegated
            foreach ($items as $item) {
                $item->is_delegated = true;
                $item->delegated_from_user_id = $delegation->user_id;
                $item->delegation = $delegation;
            }

            $delegatedApprovals = $delegatedApprovals->merge($items);
        }

        // Combine and remove duplicates
        $allApprovals = $directApprovals->merge($delegatedApprovals)
            ->unique(function ($item) {
                return $item->approvable_type . '-' . $item->approvable_id . '-' . $item->user_id;
            });

        return $allApprovals;
    }

    /**
     * Get submissions for user including delegated submissions
     */
    public static function getSubmissionsForUserWithDelegates($userId)
    {
        // Get user's direct submissions
        $directSubmissions = ApprovalLog::with([
            'approvable',
            'step.assignments.user',
            'user'
        ])
            ->where('action', 'submitted')
            ->whereHas('approvable', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->get();

        // Get submissions from users who have delegated to this user
        $delegatedSubmissions = collect();
        $delegations = ApprovalDelegation::active()
            ->where('delegate_to_user_id', $userId)
            ->where('include_submissions', true)
            ->get();

        foreach ($delegations as $delegation) {
            $items = ApprovalLog::with([
                'approvable',
                'step.assignments.user',
                'user'
            ])
                ->where('action', 'submitted')
                ->whereHas('approvable', function ($query) use ($delegation) {
                    $query->where('user_id', $delegation->user_id);
                })
                ->get();

            // Mark as delegated
            foreach ($items as $item) {
                $item->is_delegated = true;
                $item->delegated_from_user_id = $delegation->user_id;
                $item->delegation = $delegation;
            }

            $delegatedSubmissions = $delegatedSubmissions->merge($items);
        }

        return $directSubmissions->merge($delegatedSubmissions)->unique();
    }

    /**
     * Process approval with delegation support
     */
    public function processDelegatedApproval($action, $comments = null, $delegatedFromUserId = null)
    {
        $currentStep = $this->currentApprovalStep();

        if (!$currentStep) {
            return false;
        }

        // Check if this is a delegated approval
        $isDelegated = $delegatedFromUserId !== null;

        // Update existing log entries
        $updateLog = ApprovalLog::where('approvable_type', get_class($this))
            ->where('approvable_id', $this->id)
            ->where('approval_step_id', $currentStep->id)
            ->where(function ($query) {
                $query->where('action', 'pending')
                    ->orWhere('action', 'queued');
            })
            ->where('user_id', $isDelegated ? $delegatedFromUserId : Auth::id())
            ->update([
                'action' => $action,
                'comments' => $comments,
                'delegated_from_user_id' => $isDelegated ? Auth::id() : null,
                'action_date' => now(),
                'updated_at' => now(),
            ]);

        // Create new log entry if needed
        if ($updateLog === 0) {
            ApprovalLog::create([
                'approvable_type' => get_class($this),
                'approvable_id' => $this->id,
                'approval_step_id' => $currentStep->id,
                'user_id' => $isDelegated ? $delegatedFromUserId : Auth::id(),
                'delegated_from_user_id' => $isDelegated ? Auth::id() : null,
                'action' => $action,
                'comments' => $comments . ($isDelegated ? ' (Approved on behalf of ' . User::find($delegatedFromUserId)->email . ')' : ''),
                'created_by' => Auth::id(),
                'action_date' => now(),
            ]);
        }

        // Handle rejection
        if ($action === 'rejected') {
            $this->update(['status' => GeneralStatus::INACTIVE]);
            return true;
        }

        // Handle approval
        if ($this->isFullyApproved()) {
            // Your existing approval logic here
            if (get_class($this) === \App\Models\Payroll\PayrollRecord::class) {
                $this->update([
                    'status' => GeneralStatus::ACTIVE,
                    'approval_status' => ApprovalStatus::APPROVED,
                    'date_approved' => now(),
                    'payroll_record_status' => PayrollStatus::APPROVED,
                ]);
            } else {
                $this->update([
                    'status' => GeneralStatus::ACTIVE,
                    'approval_status' => ApprovalStatus::APPROVED,
                    'date_approved' => now()
                ]);
            }
            $this->cleanupPendingLogsForFullyApproved();
        } else {
            $nextStep = $this->currentApprovalStep();
            if ($nextStep) {
                $this->convertQueuedLogsToPending($nextStep);
                $this->notifyCurrentApprovers();
            }
        }

        return true;
    }
}