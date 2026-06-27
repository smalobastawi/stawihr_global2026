<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Pdp\PdpSettingController;
use App\Http\Controllers\Pdp\PdpPlanController;
use App\Http\Controllers\Pdp\PdpGoalController;
use App\Http\Controllers\Pdp\PdpProgressController;
use App\Http\Controllers\Pdp\PdpReportController;

Route::group(['module' => 'Personal Development Plans', 'prefix' => 'pdpManagement', 'middleware' => ['prevent-back-history', 'auth', 'permission']], function () {

    Route::group(['section' => 'setup', 'sub_section' => 'pdp_setting', 'prefix' => 'setting'], function () {
        Route::get('/', [PdpSettingController::class, 'index'])->name('pdp.setting.index');
        Route::put('/update', [PdpSettingController::class, 'update'])->name('pdp.setting.update');
    });

    Route::group(['section' => 'pdp', 'sub_section' => 'pdp_plan', 'prefix' => 'plan'], function () {
        Route::get('/', [PdpPlanController::class, 'index'])->name('pdp.plan.index');
        Route::get('/create', [PdpPlanController::class, 'create'])->name('pdp.plan.create');
        Route::post('/store', [PdpPlanController::class, 'store'])->name('pdp.plan.store');
        Route::get('/{plan}/show', [PdpPlanController::class, 'show'])->name('pdp.plan.show');
        Route::get('/{plan}/edit', [PdpPlanController::class, 'edit'])->name('pdp.plan.edit');
        Route::put('/{plan}', [PdpPlanController::class, 'update'])->name('pdp.plan.update');
        Route::delete('/{plan}/delete', [PdpPlanController::class, 'destroy'])->name('pdp.plan.delete');
        Route::post('/{plan}/activate', [PdpPlanController::class, 'activate'])->name('pdp.plan.activate');
        Route::post('/{plan}/complete', [PdpPlanController::class, 'complete'])->name('pdp.plan.complete');
        Route::post('/{plan}/employeeAcknowledge', [PdpPlanController::class, 'employeeAcknowledge'])->name('pdp.plan.employeeAcknowledge');
        Route::post('/{plan}/supervisorApprove', [PdpPlanController::class, 'supervisorApprove'])->name('pdp.plan.supervisorApprove');
        Route::post('/{plan}/hrReview', [PdpPlanController::class, 'hrReview'])->name('pdp.plan.hrReview');
        Route::get('/{plan}/pdf', [PdpPlanController::class, 'exportPdf'])->name('pdp.plan.pdf');
        Route::get('/employeeDetails', [PdpPlanController::class, 'employeeDetails'])->name('pdp.plan.employeeDetails');
    });

    Route::group(['section' => 'pdp', 'sub_section' => 'pdp_goal', 'prefix' => 'goal'], function () {
        Route::get('/{plan}', [PdpGoalController::class, 'index'])->name('pdp.goal.index');
        Route::post('/{plan}/store', [PdpGoalController::class, 'store'])->name('pdp.goal.store');
        Route::get('/{goal}/edit', [PdpGoalController::class, 'edit'])->name('pdp.goal.edit');
        Route::put('/{goal}', [PdpGoalController::class, 'update'])->name('pdp.goal.update');
        Route::delete('/{goal}/delete', [PdpGoalController::class, 'destroy'])->name('pdp.goal.delete');
    });

    Route::group(['section' => 'pdp', 'sub_section' => 'pdp_progress', 'prefix' => 'progress'], function () {
        Route::get('/{plan}', [PdpProgressController::class, 'index'])->name('pdp.progress.index');
        Route::get('/{plan}/create', [PdpProgressController::class, 'create'])->name('pdp.progress.create');
        Route::post('/{plan}/store', [PdpProgressController::class, 'store'])->name('pdp.progress.store');
        Route::post('/{entry}/review', [PdpProgressController::class, 'review'])->name('pdp.progress.review');
    });

    Route::group(['section' => 'reports', 'sub_section' => 'pdp_reports', 'prefix' => 'report'], function () {
        Route::get('/dashboard', [PdpReportController::class, 'dashboard'])->name('pdp.report.dashboard');
        Route::get('/byDepartment', [PdpReportController::class, 'byDepartment'])->name('pdp.report.byDepartment');
        Route::get('/byEmployee', [PdpReportController::class, 'byEmployee'])->name('pdp.report.byEmployee');
        Route::get('/progressSummary', [PdpReportController::class, 'progressSummary'])->name('pdp.report.progressSummary');
    });
});
