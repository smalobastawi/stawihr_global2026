<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Pip\PipPlanController;
use App\Http\Controllers\Pip\PipGoalController;
use App\Http\Controllers\Pip\PipSupportResourceController;
use App\Http\Controllers\Pip\PipReviewScheduleController;
use App\Http\Controllers\Pip\PipReportController;

Route::group(['module' => 'PIP Management', 'prefix' => 'pipManagement', 'middleware' => ['prevent-back-history', 'auth', 'permission']], function () {

    // PIP Plans
    Route::group(['section' => 'pip', 'sub_section' => 'pip_plan', 'prefix' => 'plan'], function () {
        Route::get('/', [PipPlanController::class, 'index'])->name('pip.plan.index');
        Route::get('/create', [PipPlanController::class, 'create'])->name('pip.plan.create');
        Route::get('/createFromAppraisal/{appraisal}', [PipPlanController::class, 'createFromAppraisal'])->name('pip.plan.createFromAppraisal');
        Route::post('/store', [PipPlanController::class, 'store'])->name('pip.plan.store');
        Route::get('/{plan}/show', [PipPlanController::class, 'show'])->name('pip.plan.show');
        Route::get('/{plan}/edit', [PipPlanController::class, 'edit'])->name('pip.plan.edit');
        Route::put('/{plan}', [PipPlanController::class, 'update'])->name('pip.plan.update');
        Route::delete('/{plan}/delete', [PipPlanController::class, 'destroy'])->name('pip.plan.delete');

        // Workflow actions
        Route::post('/{plan}/activate', [PipPlanController::class, 'activate'])->name('pip.plan.activate');
        Route::post('/{plan}/employeeAcknowledge', [PipPlanController::class, 'employeeAcknowledge'])->name('pip.plan.employeeAcknowledge');
        Route::post('/{plan}/supervisorSign', [PipPlanController::class, 'supervisorSign'])->name('pip.plan.supervisorSign');
        Route::post('/{plan}/hrValidate', [PipPlanController::class, 'hrValidate'])->name('pip.plan.hrValidate');
        Route::post('/{plan}/finalizeOutcome', [PipPlanController::class, 'finalizeOutcome'])->name('pip.plan.finalizeOutcome');
        Route::post('/{plan}/lock', [PipPlanController::class, 'lock'])->name('pip.plan.lock');
        Route::get('/employeeDetails', [PipPlanController::class, 'employeeDetails'])->name('pip.plan.employeeDetails');
    });

    // PIP Goals
    Route::group(['section' => 'pip', 'sub_section' => 'pip_goal', 'prefix' => 'goal'], function () {
        Route::get('/{plan}', [PipGoalController::class, 'index'])->name('pip.goal.index');
        Route::post('/{plan}/store', [PipGoalController::class, 'store'])->name('pip.goal.store');
        Route::get('/{goal}/edit', [PipGoalController::class, 'edit'])->name('pip.goal.edit');
        Route::put('/{goal}', [PipGoalController::class, 'update'])->name('pip.goal.update');
        Route::delete('/{goal}/delete', [PipGoalController::class, 'destroy'])->name('pip.goal.delete');
        Route::post('/{goal}/updateStatus', [PipGoalController::class, 'updateStatus'])->name('pip.goal.updateStatus');
    });

    // PIP Support Resources
    Route::group(['section' => 'pip', 'sub_section' => 'pip_support', 'prefix' => 'support'], function () {
        Route::get('/{plan}', [PipSupportResourceController::class, 'index'])->name('pip.support.index');
        Route::post('/{plan}/store', [PipSupportResourceController::class, 'store'])->name('pip.support.store');
        Route::get('/{resource}/edit', [PipSupportResourceController::class, 'edit'])->name('pip.support.edit');
        Route::put('/{resource}', [PipSupportResourceController::class, 'update'])->name('pip.support.update');
        Route::delete('/{resource}/delete', [PipSupportResourceController::class, 'destroy'])->name('pip.support.delete');
        Route::post('/{resource}/updateStatus', [PipSupportResourceController::class, 'updateStatus'])->name('pip.support.updateStatus');
    });

    // PIP Review Schedules
    Route::group(['section' => 'pip', 'sub_section' => 'pip_schedule', 'prefix' => 'schedule'], function () {
        Route::get('/{plan}', [PipReviewScheduleController::class, 'index'])->name('pip.schedule.index');
        Route::post('/{schedule}/conduct', [PipReviewScheduleController::class, 'conduct'])->name('pip.schedule.conduct');
        Route::post('/{schedule}/reschedule', [PipReviewScheduleController::class, 'reschedule'])->name('pip.schedule.reschedule');
    });

    // PIP Reports
    Route::group(['section' => 'reports', 'sub_section' => 'pip_reports', 'prefix' => 'report'], function () {
        Route::get('/dashboard', [PipReportController::class, 'dashboard'])->name('pip.report.dashboard');
        Route::get('/byDepartment', [PipReportController::class, 'byDepartment'])->name('pip.report.byDepartment');
        Route::get('/byOutcome', [PipReportController::class, 'byOutcome'])->name('pip.report.byOutcome');
    });
});
