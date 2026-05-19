<?php

use App\Http\Controllers\ApprovalWorkflowController;
use App\Http\Controllers\ApprovalController;
use App\Http\Controllers\NewApprovalController;
use Illuminate\Support\Facades\Route;

Route::group(['module' => 'Settings', 'prefix' => 'settings', 'middleware' => ['auth', 'permission']], function () {

    // Route::group(['section' => 'approvals', 'sub_section' => 'approvals'], function () {
    // Route::get('/', [ApprovalController::class, 'index'])->name('approvals.index');
    // Route::get('/create', [ApprovalController::class, 'create'])->name('approvals.create');
    // Route::post('/store', [ApprovalController::class, 'store'])->name('approvals.store');
    // Route::get('/show/{approval_request}', [ApprovalController::class, 'show'])->name('approvals.show');
    // Route::put('/approve/{approval_request}', [ApprovalController::class, 'approve'])->name('approvals.approve');


    // Route::get('/{approval}/view', [ApprovalController::class, 'view'])->name('approvals.view');
    // Route::put('/{approval}', [ApprovalController::class, 'update'])->name('approvals.update');
    // Route::delete('/{approval}/delete', [ApprovalController::class, 'destroy'])->name('approvals.delete');

    // Admin Approval Workflow Configuration Routes

    Route::group(['section' => 'approvals',   'prefix' => 'workflows',     'sub_section' => 'workflows'], function () {
        Route::get('/', [ApprovalWorkflowController::class, 'index'])->name('approval-workflows.index');

        Route::get('/create', [ApprovalWorkflowController::class, 'create'])->name('approval-workflows.create');

        Route::post('/store', [ApprovalWorkflowController::class, 'store'])->name('approval-workflows.store');

        Route::get('/{workflow}/edit', [ApprovalWorkflowController::class, 'edit'])->name('approval-workflows.edit');

        Route::put('/{workflow}', [ApprovalWorkflowController::class, 'update'])->name('approval-workflows.update');

        Route::delete('/delete/{workflow}', [ApprovalWorkflowController::class, 'destroy'])->name('approval-workflows.destroy');
        Route::get('/approval-workflows/show/{workflow}', [ApprovalWorkflowController::class, 'show'])->name('approval-workflows.show');
    });
});

Route::group(['module' => 'Settings', 'prefix' => 'approvals', 'middleware' => ['auth', 'permission']], function () {
    Route::post('/{modelType}/{modelId}/approve', [NewApprovalController::class, 'approve'])->name('approvals.approve');
    Route::post('/{modelType}/{modelId}/reject', [NewApprovalController::class, 'reject'])->name('approvals.reject');
    Route::get('/{modelType}/{modelId}/status', [NewApprovalController::class, 'status'])->name('approvals.status');
});




// Approval Processing Routes (for actual approval actions)
Route::group(['module' => 'approvals',    'prefix' => 'approvals',    'middleware' => ['auth', 'permission']], function () {
    Route::group(['section' => 'approvals',        'sub_section' => 'requests'], function () {
        // Generic approval routes for any model
        //load approvals.index route
        Route::get('/', [NewApprovalController::class, 'index'])->name('approvals.index');

        Route::post('/{modelType}/{modelId}/approve', [NewApprovalController::class, 'approve'])->name('approvals.approve');

        Route::post('/{modelType}/{modelId}/reject', [NewApprovalController::class, 'reject'])->name('approvals.reject');

        Route::get('/{modelType}/{modelId}/status', [NewApprovalController::class, 'status'])->name('approvals.status');

        // Batch approval routes
        Route::post('/{modelType}/batch-approve', [NewApprovalController::class, 'batchApprove'])->name('approvals.batch-approve');

        Route::post('/{modelType}/batch-reject', [NewApprovalController::class, 'batchReject'])->name('approvals.batch-reject');

        Route::post('/{modelType}/batch-preview', [NewApprovalController::class, 'batchPreview'])->name('approvals.batch-preview');

        // Get pending approvals by model type for batch operations
        Route::get('/{modelType}/pending', [NewApprovalController::class, 'pendingByModelType'])->name('approvals.pending-by-type');

        // Additional approval request routes if needed
        Route::get('/pending', [NewApprovalController::class, 'pendingRequests'])->name('approvals.pending');

        // Get current user's pending approvals
        Route::get('/my-pending', [NewApprovalController::class, 'pendingApprovals'])->name('approvals.my-pending');

        // Specific endpoint for employee deductions batch approvals
        Route::get('/employee-deductions/pending', [NewApprovalController::class, 'pendingEmployeeDeductions'])->name('approvals.pending-employee-deductions');

        Route::get('/history', [NewApprovalController::class, 'approvalHistory'])->name('approvals.history');
        Route::post('{modelType}/batch-submit', [NewApprovalController::class, 'batchSubmitForApproval'])
            ->name('approvals.batch-submit');
        //  Batch status tracking
        Route::get('batch/{batchId}/status', [NewApprovalController::class, 'batchStatus'])
            ->name('approvals.batch-status');

        Route::post('/submit-for-approval/{modelType}/{modelId}', [NewApprovalController::class, 'submitForApproval'])
            ->name('approvals.submit')
            ->middleware('auth');

        Route::post('/approvals/{modelType}/batch-submit', [NewApprovalController::class, 'batchSubmitForApproval'])
            ->name('approvals.batch.submit')
            ->middleware('auth');

        // Batch preview (check what can be approved/rejected)
        Route::post('{modelType}/batch-preview', [NewApprovalController::class, 'batchPreview'])
            ->name('approvals.batch-preview');
    });
});