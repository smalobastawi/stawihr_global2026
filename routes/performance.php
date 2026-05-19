<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Performance\RatingScaleController;
use App\Http\Controllers\Performance\FocusAreaController;
use App\Http\Controllers\Performance\GoalController;
use App\Http\Controllers\Performance\BehavioralItemController;
use App\Http\Controllers\Performance\AppraisalController;
use App\Http\Controllers\Performance\AppraisalReportController;
use App\Http\Controllers\Performance\ReviewPeriodController;

Route::group(['module' => 'Performance Management', 'prefix' => 'performanceManagement', 'middleware' => ['prevent-back-history', 'auth', 'permission']], function () {

    // ============================================
    // HR / ADMIN FUNCTIONS - Setup & Configuration
    // ============================================
    Route::group(['section' => 'setup'], function () {

        // Rating Guidelines
        Route::group(['sub_section' => 'rating_scale', 'prefix' => 'ratingScale'], function () {
            Route::get('/', [RatingScaleController::class, 'index'])->name('performance.ratingScale.index');
            Route::get('/create', [RatingScaleController::class, 'create'])->name('performance.ratingScale.create');
            Route::post('/store', [RatingScaleController::class, 'store'])->name('performance.ratingScale.store');
            Route::get('/{ratingScale}/edit', [RatingScaleController::class, 'edit'])->name('performance.ratingScale.edit');
            Route::put('/{ratingScale}', [RatingScaleController::class, 'update'])->name('performance.ratingScale.update');
            Route::delete('/{ratingScale}/delete', [RatingScaleController::class, 'destroy'])->name('performance.ratingScale.delete');
        });

        // Review Periods (Performance Periods with Date Ranges)
        Route::group(['sub_section' => 'review_period', 'prefix' => 'reviewPeriod'], function () {
            Route::get('/', [ReviewPeriodController::class, 'index'])->name('performance.reviewPeriod.index');
            Route::get('/create', [ReviewPeriodController::class, 'create'])->name('performance.reviewPeriod.create');
            Route::post('/store', [ReviewPeriodController::class, 'store'])->name('performance.reviewPeriod.store');
            Route::get('/{reviewPeriod}/edit', [ReviewPeriodController::class, 'edit'])->name('performance.reviewPeriod.edit');
            Route::put('/{reviewPeriod}', [ReviewPeriodController::class, 'update'])->name('performance.reviewPeriod.update');
            Route::delete('/{reviewPeriod}/delete', [ReviewPeriodController::class, 'destroy'])->name('performance.reviewPeriod.delete');
        });

        // Focus Areas (Categories)
        Route::group(['sub_section' => 'focus_area', 'prefix' => 'focusArea'], function () {
            Route::get('/', [FocusAreaController::class, 'index'])->name('performance.focusArea.index');
            Route::get('/create', [FocusAreaController::class, 'create'])->name('performance.focusArea.create');
            Route::post('/store', [FocusAreaController::class, 'store'])->name('performance.focusArea.store');
            Route::get('/{focusArea}/edit', [FocusAreaController::class, 'edit'])->name('performance.focusArea.edit');
            Route::put('/{focusArea}', [FocusAreaController::class, 'update'])->name('performance.focusArea.update');
            Route::delete('/{focusArea}/delete', [FocusAreaController::class, 'destroy'])->name('performance.focusArea.delete');
        });

        // Goals (KPIs under Focus Areas)
        Route::group(['sub_section' => 'performance_goal', 'prefix' => 'goal'], function () {
            Route::get('/{focusArea}', [GoalController::class, 'index'])->name('performance.goal.index');
            Route::get('/{focusArea}/create', [GoalController::class, 'create'])->name('performance.goal.create');
            Route::post('/{focusArea}/store', [GoalController::class, 'store'])->name('performance.goal.store');
            Route::get('/{goal}/edit', [GoalController::class, 'edit'])->name('performance.goal.edit');
            Route::put('/{goal}', [GoalController::class, 'update'])->name('performance.goal.update');
            Route::delete('/{goal}/delete', [GoalController::class, 'destroy'])->name('performance.goal.delete');
        });

        // Behavioral Items
        Route::group(['sub_section' => 'behavioral_item', 'prefix' => 'behavioralItem'], function () {
            Route::get('/', [BehavioralItemController::class, 'index'])->name('performance.behavioralItem.index');
            Route::get('/create', [BehavioralItemController::class, 'create'])->name('performance.behavioralItem.create');
            Route::post('/store', [BehavioralItemController::class, 'store'])->name('performance.behavioralItem.store');
            Route::get('/{behavioralItem}/edit', [BehavioralItemController::class, 'edit'])->name('performance.behavioralItem.edit');
            Route::put('/{behavioralItem}', [BehavioralItemController::class, 'update'])->name('performance.behavioralItem.update');
            Route::delete('/{behavioralItem}/delete', [BehavioralItemController::class, 'destroy'])->name('performance.behavioralItem.delete');
        });
    });

    // ============================================
    // HR / ADMIN FUNCTIONS - Appraisal Management
    // ============================================
    Route::group(['section' => 'appraisals', 'sub_section' => 'performance_appraisal', 'prefix' => 'appraisal'], function () {
        // Management functions
        Route::get('/manage', [AppraisalController::class, 'index'])->name('performance.appraisal.index');
        Route::get('/create', [AppraisalController::class, 'create'])->name('performance.appraisal.create');
        Route::post('/store', [AppraisalController::class, 'store'])->name('performance.appraisal.store');
        Route::get('/template/download', [AppraisalController::class, 'downloadTemplate'])->name('performance.appraisal.template.download');
        Route::post('/bulk-upload', [AppraisalController::class, 'bulkUpload'])->name('performance.appraisal.bulkUpload');
        Route::get('/{appraisal}/show', [AppraisalController::class, 'show'])->name('performance.appraisal.show');
        Route::get('/{appraisal}/edit', [AppraisalController::class, 'edit'])->name('performance.appraisal.edit');
        Route::put('/{appraisal}', [AppraisalController::class, 'update'])->name('performance.appraisal.update');
        Route::delete('/{appraisal}/delete', [AppraisalController::class, 'destroy'])->name('performance.appraisal.delete');

        // Self Review (for admin to view/assist employee self-review)
        Route::get('/{appraisal}/selfReview', [AppraisalController::class, 'selfReview'])->name('performance.appraisal.selfReview');
        Route::post('/{appraisal}/saveSelfReview', [AppraisalController::class, 'saveSelfReview'])->name('performance.appraisal.saveSelfReview');

        // HOD Review workflow (typically done by HR/Admin or HOD)
        Route::get('/{appraisal}/hodReview', [AppraisalController::class, 'hodReview'])->name('performance.appraisal.hodReview');
        Route::post('/{appraisal}/saveHodReview', [AppraisalController::class, 'saveHodReview'])->name('performance.appraisal.saveHodReview');
        Route::post('/{appraisal}/finalize', [AppraisalController::class, 'finalize'])->name('performance.appraisal.finalize');

        // Sign-offs
        Route::post('/{appraisal}/employeeSign', [AppraisalController::class, 'employeeSign'])->name('performance.appraisal.employeeSign');
        Route::post('/{appraisal}/supervisorSign', [AppraisalController::class, 'supervisorSign'])->name('performance.appraisal.supervisorSign');
        Route::post('/{appraisal}/hodSign', [AppraisalController::class, 'hodSign'])->name('performance.appraisal.hodSign');
    });

    // ============================================
    // SUPERVISOR FUNCTIONS - Evaluation Workflows
    // ============================================
    Route::group(['section' => 'supervisor_eval', 'sub_section' => 'supervisor_evaluation', 'prefix' => 'supervisor'], function () {
        // Supervisor dashboard - appraisals awaiting review
        Route::get('/evaluations', [AppraisalController::class, 'supervisorEvaluations'])->name('performance.supervisor.evaluations');

        // Supervisor Review workflow
        Route::get('/review/{appraisal}', [AppraisalController::class, 'supervisorReview'])->name('performance.supervisor.review');
        Route::post('/review/{appraisal}/save', [AppraisalController::class, 'saveSupervisorReview'])->name('performance.supervisor.saveReview');
    });

    // ============================================
    // HOD FUNCTIONS - HOD Review Dashboard
    // ============================================
    Route::group(['section' => 'hod_eval', 'sub_section' => 'hod_evaluation', 'prefix' => 'hod'], function () {
        // HOD dashboard - appraisals awaiting HOD review
        Route::get('/evaluations', [AppraisalController::class, 'hodEvaluations'])->name('performance.hod.evaluations');
    });

    // ============================================
    // AJAX & Utilities
    // ============================================
    Route::get('/ajax/focus-areas-for-employee/{employee}', [AppraisalController::class, 'focusAreasForEmployee'])->name('performance.ajax.focusAreasForEmployee');

    // ============================================
    // REPORTS
    // ============================================
    Route::group(['section' => 'reports', 'sub_section' => 'performance_reports', 'prefix' => 'report'], function () {
        Route::get('/department', [AppraisalReportController::class, 'departmentReport'])->name('performance.report.department');
        Route::post('/department', [AppraisalReportController::class, 'departmentReport'])->name('performance.report.department.download');
        Route::get('/employee', [AppraisalReportController::class, 'employeeReport'])->name('performance.report.employee');
        Route::post('/employee', [AppraisalReportController::class, 'employeeReport'])->name('performance.report.employee.download');
        Route::get('/summary', [AppraisalReportController::class, 'summaryReport'])->name('performance.report.summary');
    });
});
