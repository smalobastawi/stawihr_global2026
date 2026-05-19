<?php

namespace App\Http\Controllers;

use App\Lib\Enumerations\ApprovalStatus;
use Illuminate\Http\Request;
use App\Models\ApprovalLog;
use App\Models\ApprovalWorkflow;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class NewApprovalController extends Controller
{
    /**
     * Model namespace map for approval workflow
     */
    protected const MODEL_NAMESPACE_MAP = [
        // Employee-related models
        'employee_deduction' => \App\Models\EmployeeDeductions::class,
        'employee_earnings' => \App\Models\EmployeeEarnings::class,

        // Payroll models
        'payroll_record' => \App\Models\Payroll\PayrollRecord::class,
        'employee_payroll' => \App\Models\Payroll\EmployeePayroll::class,
        'employee_allowance' => \App\Models\Payroll\EmployeeAllowance::class,
        'payroll_claim' => \App\Models\Payroll\PayrollClaim::class,
        'payroll_claim_recovery' => \App\Models\Payroll\PayrollClaimRecovery::class,
        'advances' => \App\Models\Advances::class,

        // Add other models as needed
    ];

    public function approve(Request $request, $modelType, $modelId)
    {

        $model = $this->resolveModel($modelType, $modelId);


        if (!$model->canBeActionedBy(auth()->user())) {
            return response()->json(['error' => 'Not authorized to approve this'], 403);
        }

        $validated = $request->validate([
            'comments' => 'nullable|string|max:500'
        ]);

        $model->processApproval('approved', $validated['comments']);

        return response()->json([
            'message' => 'Approved successfully',
            'success' => true
        ], 200);
    }

    public function reject(Request $request, $modelType, $modelId)
    {
        $model = $this->resolveModel($modelType, $modelId);

        if (!$model->canBeActionedBy(auth()->user())) {
            return response()->json(['error' => 'Not authorized to reject this'], 403);
        }

        $validated = $request->validate([
            'comments' => 'required|string|max:500'
        ]);

        $model->processApproval('rejected', $validated['comments']);

        return response()->json(['message' => 'Rejected successfully']);
    }

    public function status($modelType, $modelId)
    {
        $model = $this->resolveModel($modelType, $modelId);

        $logs = $model->approvalLogs()
            ->with(['user', 'step'])
            ->orderBy('created_at')
            ->get();

        $currentStep = $model->currentApprovalStep();
        $currentApprovers = $currentStep ? $currentStep->assignments()->with('user')->get() : collect();

        return response()->json([
            'status' => $model->status,
            'is_fully_approved' => $model->isFullyApproved(),
            'logs' => $logs,
            'current_step' => $currentStep,
            'current_approvers' => $currentApprovers
        ]);
    }

    public function batchApprove(Request $request, $modelType)
    {
        $validated = $request->validate([
            'model_ids' => 'required|array|min:1',
            'model_ids.*' => 'required|integer',
            'comments' => 'nullable|string|max:500'
        ]);

        // Resolve all models first
        $models = [];
        foreach ($validated['model_ids'] as $modelId) {
            try {
                $model = $this->resolveModel($modelType, $modelId);
                $models[] = $model;
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => "Failed to resolve model with ID {$modelId}: " . $e->getMessage()
                ], 400);
            }
        }

        // Use the new batch approval method from the trait
        $className = $this->resolveModelClass($modelType);
        $result = $className::processBatchApprovalAdvanced($models, 'approved', $validated['comments']);

        return response()->json([
            'success' => $result['success'],
            'message' => "Batch approval completed: {$result['summary']['success']} approved, {$result['summary']['errors']} failed",
            'batch_id' => $result['batch_id'],
            'summary' => $result['summary'],
            'results' => array_map(function ($modelId, $result) {
                return array_merge(['id' => $modelId], $result);
            }, array_keys($result['results']), $result['results'])
        ]);
    }

    public function batchReject(Request $request, $modelType)
    {
        $validated = $request->validate([
            'model_ids' => 'required|array|min:1',
            'model_ids.*' => 'required|integer',
            'comments' => 'required|string|max:500'
        ]);

        // Resolve all models first
        $models = [];
        foreach ($validated['model_ids'] as $modelId) {
            try {
                $model = $this->resolveModel($modelType, $modelId);
                $models[] = $model;
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => "Failed to resolve model with ID {$modelId}: " . $e->getMessage()
                ], 400);
            }
        }

        // Use the new batch approval method from the trait
        $className = $this->resolveModelClass($modelType);
        $result = $className::processBatchApprovalAdvanced($models, 'rejected', $validated['comments']);

        return response()->json([
            'success' => $result['success'],
            'message' => "Batch rejection completed: {$result['summary']['success']} rejected, {$result['summary']['errors']} failed",
            'batch_id' => $result['batch_id'],
            'summary' => $result['summary'],
            'results' => array_map(function ($modelId, $result) {
                return array_merge(['id' => $modelId], $result);
            }, array_keys($result['results']), $result['results'])
        ]);
    }

    /**
     * Submit multiple records for approval as a batch
     */
    public function batchSubmitForApproval(Request $request, $modelType)
    {
        $validated = $request->validate([
            'model_ids' => 'required|array|min:1',
            'model_ids.*' => 'required|integer'
        ]);

        $currentUser = auth()->user();
        $models = [];
        $userIsApproverForSome = false;
        $rejectedModels = [];

        foreach ($validated['model_ids'] as $modelId) {
            try {
                $model = $this->resolveModel($modelType, $modelId);

                if (!method_exists($model, 'submitForApproval')) {
                    $rejectedModels[$modelId] = "Model does not support approval workflow";
                    continue;
                }

                // Check if current user is an approver in the workflow
                $workflow = $model->approvalWorkflow();
                $userIsApprover = false;

                if ($workflow) {
                    foreach ($workflow->steps as $step) {
                        foreach ($step->assignments as $assignment) {
                            if ($assignment->user_id === $currentUser->id) {
                                $userIsApprover = true;
                                break 2;
                            }
                        }
                    }
                }

                if ($userIsApprover) {
                    $userIsApproverForSome = true;
                    $rejectedModels[$modelId] = "You cannot submit a record for which you are also an approver";
                    continue;
                }

                $models[] = $model;
            } catch (\Exception $e) {
                $rejectedModels[$modelId] = "Failed to resolve model: " . $e->getMessage();
            }
        }

        if ($userIsApproverForSome && empty($models)) {
            return response()->json([
                'success' => false,
                'message' => 'Batch submission failed: You cannot submit records for which you are also an approver',
                'rejected_models' => $rejectedModels
            ], 400);
        }

        // Use the new batch submission method from the trait
        $className = $this->resolveModelClass($modelType);
        $result = $className::submitForApprovalBatch($models);

        // Include rejected models in the response
        foreach ($rejectedModels as $modelId => $reason) {
            $result['results'][$modelId] = [
                'success' => false,
                'message' => $reason
            ];
            $result['summary']['errors']++;
        }

        return response()->json([
            'success' => $result['success'],
            'message' => $result['success']
                ? "Batch submission completed with some rejections: {$result['summary']['success']} submitted successfully, {$result['summary']['errors']} failed"
                : "Batch submission failed: {$result['summary']['errors']} failed, {$result['summary']['success']} succeeded",
            'batch_id' => $result['batch_id'],
            'summary' => $result['summary'],
            'results' => array_map(function ($modelId, $result) {
                return array_merge(['id' => $modelId], $result);
            }, array_keys($result['results']), $result['results'])
        ]);
    }

    /**
     * Get approval status for a specific batch
     */
    public function batchStatus(Request $request, $batchId)
    {
        try {
            // Get approval logs for this batch
            $logs = \App\Models\ApprovalLog::where('batch_id', $batchId)
                ->with(['approvable', 'user', 'step'])
                ->orderBy('created_at')
                ->get();

            if ($logs->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No records found for this batch ID'
                ], 404);
            }

            // Group logs by approvable (model instances)
            $groupedLogs = $logs->groupBy('approvable_id');

            $batchSummary = [
                'batch_id' => $batchId,
                'total_items' => $groupedLogs->count(),
                'submitted_at' => $logs->where('action', 'submitted')->first()->created_at ?? null,
                'items' => []
            ];

            foreach ($groupedLogs as $approvableId => $itemLogs) {
                $model = $itemLogs->first()->approvable;
                if ($model) {
                    $batchSummary['items'][] = [
                        'id' => $model->id,
                        'title' => $this->getApprovalTitle($model),
                        'status' => $model->status,
                        'current_step' => $model->currentApprovalStep()?->name ?? 'No active step',
                        'is_fully_approved' => $model->isFullyApproved(),
                        'logs' => $itemLogs->map(function ($log) {
                            return [
                                'action' => $log->action,
                                'user' => $log->user?->name ?? 'System',
                                'comments' => $log->comments,
                                'created_at' => $log->created_at
                            ];
                        })
                    ];
                }
            }

            return response()->json([
                'success' => true,
                'data' => $batchSummary
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving batch status: ' . $e->getMessage()
            ], 500);
        }
    }

    public function batchPreview(Request $request, $modelType)
    {
        $validated = $request->validate([
            'model_ids' => 'required|array|min:1',
            'model_ids.*' => 'required|integer'
        ]);

        $results = [];
        $canApproveCount = 0;
        $cannotApproveCount = 0;

        foreach ($validated['model_ids'] as $modelId) {
            try {
                $model = $this->resolveModel($modelType, $modelId);

                $canApprove = $model->canBeActionedBy(auth()->user());
                $currentStep = $model->currentApprovalStep();

                $results[] = [
                    'id' => $modelId,
                    'title' => $this->getApprovalTitle($model),
                    'can_approve' => $canApprove,
                    'current_step' => $currentStep ? $currentStep->name : 'No pending step',
                    'status' => $model->status,
                    'details' => method_exists($model, 'getApprovalDetails')
                        ? $model->getApprovalDetails()
                        : null
                ];

                if ($canApprove) {
                    $canApproveCount++;
                } else {
                    $cannotApproveCount++;
                }
            } catch (\Exception $e) {
                $results[] = [
                    'id' => $modelId,
                    'title' => "Record #{$modelId}",
                    'can_approve' => false,
                    'error' => $e->getMessage()
                ];
                $cannotApproveCount++;
            }
        }

        return response()->json([
            'success' => true,
            'summary' => [
                'total' => count($validated['model_ids']),
                'can_approve' => $canApproveCount,
                'cannot_approve' => $cannotApproveCount
            ],
            'records' => $results
        ]);
    }

    public function submitForApproval(Request $request, $modelType, $modelId)
    {
        try {
            $model = $this->resolveModel($modelType, $modelId);
            $currentUser = auth()->user();

            if (!method_exists($model, 'submitForApproval')) {
                throw new \Exception('This model does not support approval workflow');
            }

            // Check if workflow is properly configured
            if (!$model->approvalWorkflow()) {
                return response()->json([
                    'error' => true,
                    'message' => 'No approval workflow configured for this record type'
                ], 404);
            }

            // Check if current user is an approver in any step of the workflow
            $workflow = $model->approvalWorkflow();
            $userIsApprover = false;

            // Check each step in the workflow to see if current user is an approver
            foreach ($workflow->steps as $step) {
                foreach ($step->assignments as $assignment) {
                    if ($assignment->user_id === $currentUser->id) {
                        $userIsApprover = true;
                        break 2; // Break out of both loops
                    }
                }
            }

            if ($userIsApprover) {
                return response()->json([
                    'success' => false,
                    'message' => 'You cannot submit a record for which you are also an approver'
                ], 400);
            }

            $model->submitForApproval();

            return response()->json([
                'success' => true,
                'message' => 'Item submitted for approval successfully',
                'status' => $model->fresh()->status
            ]);
        } catch (\Exception $e) {
            Log::error("Approval submission failed: " . $e->getMessage(), [
                'modelType' => $modelType,
                'modelId' => $modelId,
                'user' => auth()->id()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to submit for approval: ' . $e->getMessage()
            ], 400);
        }
    }

    /**
     * Resolve model instance from type and ID
     */
    protected function resolveModel(string $modelType, $modelId)
    {

        $className = $this->resolveModelClass($modelType);

        if (!class_exists($className)) {
            throw new \Exception("Model class {$className} not found");
        }

        $model = $className::find($modelId);

        if (!$model) {
            throw new \Exception("Model instance not found");
        }

        return $model;
    }

    /**
     * Convert URL model type to fully qualified class name
     */
    protected function resolveModelClass(string $modelType): string
    {
        // If it's already a fully qualified class name, return it as-is
        if (class_exists($modelType)) {
            return $modelType;
        }

        // Check if we have a direct mapping
        if (array_key_exists($modelType, self::MODEL_NAMESPACE_MAP)) {
            return self::MODEL_NAMESPACE_MAP[$modelType];
        }

        // Default transformation for other models
        $studly = Str::studly(str_replace('_', '', $modelType));
        $className = $studly;

        // Handle nested namespaces
        if (!class_exists($className)) {
            $parts = explode('_', $modelType);
            $studlyParts = array_map('Str::studly', $parts);
            $className = implode('\\', $studlyParts);
        }

        return $className;
    }

    /**
     * Get pending approvals for the current user
     */
    public function pendingApprovals(Request $request)
    {
        $userId = auth()->id();

        $approvals = ApprovalLog::where('user_id', $userId)
            ->where('action', 'pending')
            ->with(['step', 'approvable', 'user'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($log) {
                $modelType = class_basename($log->approvable_type);
                $modelTypeKey = $this->getModelTypeKey($log->approvable_type);

                return [
                    'id' => $log->approvable_id,
                    'model_type' => $modelTypeKey,
                    'model_class' => $modelType,
                    'step_name' => $log->step->name ?? 'Unknown Step',
                    'step_level' => $log->step->level ?? 0,
                    'submitted_at' => $log->created_at,
                    'submitter' => $log->approvable->submitter->name ?? 'System',
                    'title' => $this->getApprovalTitle($log->approvable),
                    'details' => method_exists($log->approvable, 'getApprovalDetails')
                        ? $log->approvable->getApprovalDetails()
                        : null
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $approvals,
            'count' => $approvals->count()
        ]);
    }

    /**
     * Get pending approvals by model type for batch operations
     */
    public function pendingByModelType(Request $request, $modelType)
    {
        $userId = auth()->id();
        $modelClass = $this->resolveModelClass($modelType);

        if (!class_exists($modelClass)) {
            return response()->json(['error' => "Model class {$modelClass} not found"], 404);
        }

        // Get pending approval logs for this user and model type
        $pendingLogs = ApprovalLog::where('user_id', $userId)
            ->where('action', 'pending')
            ->where('approvable_type', $modelClass)
            ->with(['step', 'approvable'])
            ->orderBy('created_at', 'desc')
            ->get();

        $results = $pendingLogs->map(function ($log) use ($modelType) {
            if (!$log->approvable) {
                return null; // Skip if approvable is null
            }

            return [
                'id' => $log->approvable->id,
                'model_type' => $modelType,
                'step_name' => $log->step->name ?? 'Unknown Step',
                'step_level' => $log->step->level ?? 0,
                'submitted_at' => $log->created_at,
                'title' => $this->getApprovalTitle($log->approvable),
                'details' => method_exists($log->approvable, 'getApprovalDetails')
                    ? $log->approvable->getApprovalDetails()
                    : null,
                'can_approve' => $log->approvable->canBeActionedBy(auth()->user())
            ];
        })->filter(); // Remove null values

        return response()->json([
            'success' => true,
            'model_type' => $modelType,
            'data' => $results->values(),
            'count' => $results->count()
        ]);
    }

    /**
     * Get pending employee deductions specifically for batch approval
     */
    public function pendingEmployeeDeductions()
    {
        return $this->pendingByModelType(request(), 'employee_deduction');
    }

    /**
     * Get model type key from class name
     */
    protected function getModelTypeKey($className)
    {
        foreach (self::MODEL_NAMESPACE_MAP as $key => $class) {
            if ($class === $className) {
                return $key;
            }
        }

        // Fallback to snake_case of class basename
        return strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', class_basename($className)));
    }

    /**
     * Get approval title for display
     */
    protected function getApprovalTitle($model)
    {
        if (method_exists($model, 'getApprovalTitle')) {
            return $model->getApprovalTitle();
        }

        $modelType = class_basename(get_class($model));

        if (property_exists($model, 'name')) {
            return "{$modelType}: {$model->name}";
        }

        if (property_exists($model, 'title')) {
            return "{$modelType}: {$model->title}";
        }

        if (property_exists($model, 'reference_number')) {
            return "{$modelType}: {$model->reference_number}";
        }

        return "{$modelType} #{$model->id}";
    }
}