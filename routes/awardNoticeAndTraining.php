<?php

use App\Http\Controllers\AwardNoticeAndTraining\TrainingFacilitatorController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AwardNoticeAndTraining\AwardController;
use App\Http\Controllers\AwardNoticeAndTraining\NoticeController;
use App\Http\Controllers\AwardNoticeAndTraining\TrainingTypeController;
use App\Http\Controllers\AwardNoticeAndTraining\EmployeeTrainingController;
use App\Http\Controllers\AwardNoticeAndTraining\TrainingReportController;
use App\Http\Controllers\AwardNoticeAndTraining\TrainingAttPartConctoller;


Route::group(['middleware' => ['prevent-back-history', 'auth', 'permission']], function () {

    Route::group(['module' => 'Award', 'section' => 'awards', 'sub_section' => 'awards', 'prefix' => 'award'], function () {
        Route::get('/', [AwardController::class, 'index'])->name('award.index');
        Route::get('/create', [AwardController::class, 'create'])->name('award.create');
        Route::post('/', [AwardController::class, 'store'])->name('award.store');
        Route::get('/{award}/edit', [AwardController::class, 'edit'])->name('award.edit');
        Route::put('/{award}', [AwardController::class, 'update'])->name('award.update');
        Route::delete('/{award}/delete', [AwardController::class, 'destroy'])->name('award.delete');
    });

    Route::group(['module' => 'Notice Board', 'section' => 'notices', 'sub_section' => 'notices', 'prefix' => 'notice'], function () {
        Route::get('/', [NoticeController::class, 'index'])->name('notice.index');
        Route::get('/create', [NoticeController::class, 'create'])->name('notice.create');
        Route::post('/', [NoticeController::class, 'store'])->name('notice.store');
        Route::get('/{notice}', [NoticeController::class, 'show'])->name('notice.show');
        Route::get('/{notice}/edit', [NoticeController::class, 'edit'])->name('notice.edit');
        Route::put('/{notice}', [NoticeController::class, 'update'])->name('notice.update');
        Route::delete('/{notice}/delete', [NoticeController::class, 'destroy'])->name('notice.delete');
    });

    Route::group(['module' => 'Training', 'section' => 'training_type', 'sub_section' => 'training_type', 'prefix' => 'trainingType'], function () {
        Route::get('/', [TrainingTypeController::class, 'index'])->name('trainingType.index');
        Route::get('/create', [TrainingTypeController::class, 'create'])->name('trainingType.create');
        Route::post('/', [TrainingTypeController::class, 'store'])->name('trainingType.store');
        Route::get('/trainings', [TrainingTypeController::class, 'listTrainings'])->name('trainingType.list.options');
        Route::get('/{trainingType}', [TrainingTypeController::class, 'show'])->name('trainingType.show');
        Route::get('/{trainingType}/edit', [TrainingTypeController::class, 'edit'])->name('trainingType.edit');
        Route::put('/{trainingType}', [TrainingTypeController::class, 'update'])->name('trainingType.update');
        Route::delete('/{trainingType}/delete', [TrainingTypeController::class, 'destroy'])->name('trainingType.delete');
    });

    Route::group(['module' => 'Training', 'section' => 'trainings', 'sub_section' => 'trainings', 'prefix' => 'trainingInfo'], function () {
        Route::get('/', [EmployeeTrainingController::class, 'index'])->name('trainingInfo.index');
        Route::get('/create', [EmployeeTrainingController::class, 'create'])->name('trainingInfo.create');
        Route::post('/save', [EmployeeTrainingController::class, 'store'])->name('trainingInfo.store');
        Route::get('/{training}', [EmployeeTrainingController::class, 'show'])->name('trainingInfo.show');
        Route::get('/{training}/edit', [EmployeeTrainingController::class, 'edit'])->name('trainingInfo.edit');
        Route::put('/{training}/update', [EmployeeTrainingController::class, 'update'])->name('trainingInfo.update');
        Route::delete('/{id}/delete', [EmployeeTrainingController::class, 'destroy'])->name('trainingInfo.delete');
    }); 

    Route::group(['module' => 'Training', 'section' => 'participants_and_invitees', 'prefix' => 'attendance_and_invites'], function () {
        // Route::get('/', [TrainingAttPartConctoller::class, 'index'])->name('trainingInfo.attendants.index');
        Route::get('/{training}/taining_details', [TrainingAttPartConctoller::class, 'index'])->name('trainingInfo.attendants.index');
        
        Route::group(['sub_section' => 'attendances', 'prefix' => 'attendants'], function () {
            
            Route::get('/{training}', [TrainingAttPartConctoller::class, 'attendances'])->name('trainingInfo.attendants');
            Route::post('/add/{training}', [TrainingAttPartConctoller::class, 'addAttendance'])->name('trainingInfo.attendants.add');
            Route::put('/approve/{training}', [TrainingAttPartConctoller::class, 'approveAttendance'])->name('trainingInfo.attendants.approve');
            Route::delete('/delete/{training}/{attendant}', [TrainingAttPartConctoller::class, 'deleteAttendance'])->name('trainingInfo.attendants.delete');
        });

        Route::group([ 'sub_section' => 'invites', 'prefix' => 'invites'], function () {
            
            Route::get('/{training}', [TrainingAttPartConctoller::class, 'invitees'])->name('trainingInfo.invitees');
            Route::post('/{training}/add', [TrainingAttPartConctoller::class, 'addInvites'])->name('trainingInfo.invitees.add');
            Route::post('/addMultiple', [TrainingAttPartConctoller::class, 'addMultipleInvites'])->name('trainingInfo.invitees.addMultiple');
            Route::put('/approve/{training}', [TrainingAttPartConctoller::class, 'approveInvites'])->name('trainingInfo.invitees.approve');
            Route::delete('/delete/{training}/{invitee}', [TrainingAttPartConctoller::class, 'deleteInvites'])->name('trainingInfo.invitees.delete');
 
        });
    });

    Route::group(['module' => 'Training', 'section' => 'training_report', 'sub_section' => 'training_report', 'prefix' => 'trainingInfo'], function () {
        Route::get('training/report', [TrainingReportController::class, 'employeeTrainingReport'])->name('training.report.form');
        Route::post('trainingReport', [TrainingReportController::class, 'employeeTrainingReport'])->name('employeeTrainingReport.employeeTrainingReport.download');
        Route::get('training/report/download', [TrainingReportController::class, 'downloadTrainingReport'])->name('training.report.download');
    });

    Route::group(['module' => 'Training', 'section' => 'training_faclitators', 'sub_section' => 'training_faclitators', 'prefix' => 'training/facilitators', 'as' => 'training.facilitator.'], function () {
        Route::get('/', [TrainingFacilitatorController::class, 'index'])->name('index');
        Route::get('create', [TrainingFacilitatorController::class, 'create'])->name('form');
        Route::post('store', [TrainingFacilitatorController::class, 'store'])->name('store');
        Route::get('edit{facilitator}', [TrainingFacilitatorController::class, 'edit'])->name('edit');
        Route::get('show/{facilitator}', [TrainingFacilitatorController::class, 'show'])->name('show');
        Route::put('update/{facilitator}', [TrainingFacilitatorController::class, 'update'])->name('update');
        Route::delete('delete/{facilitator}', [TrainingFacilitatorController::class, 'destroy'])->name('delete');
        Route::get('filter', [TrainingFacilitatorController::class, 'filter'])->name('filter'); 

    });
});

