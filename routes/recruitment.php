<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Recruitment\JobPostController;
use App\Http\Controllers\Recruitment\JobCandidateController;
use App\Http\Controllers\Recruitment\JobRequisitionController;

Route::group(['module' => 'Recruitment', 'section' => 'General', 'prefix' => 'recruitment', 'middleware' => ['prevent-back-history', 'auth', 'permission']], function () {

    Route::group(['sub_section' => 'job_post', 'prefix' => 'jobPost'], function () {
        Route::get('/', [JobPostController::class, 'index'])->name('jobPost.index');
        Route::get('/create', [JobPostController::class, 'create'])->name('jobPost.create');
        Route::post('/store', [JobPostController::class, 'store'])->name('jobPost.store');
        Route::get('/{jobPostID}', [JobPostController::class, 'show'])->name('jobPost.show');
        Route::get('/{jobPostID}/edit', [JobPostController::class, 'edit'])->name('jobPost.edit');
        Route::put('/{jobPostID}', [JobPostController::class, 'update'])->name('jobPost.update');
        Route::delete('/{jobPostID}/delete', [JobPostController::class, 'destroy'])->name('jobPost.delete');

        // AJAX route to fetch requisition data
        Route::get('/ajax/job-requisition/{id}', [JobPostController::class, 'getRequisitionData'])->name('jobPost.requisition.data');
    });

    Route::group(['sub_section' => 'job_requisition', 'prefix' => 'jobRequisition'], function () {
        Route::get('/', [JobRequisitionController::class, 'index'])->name('jobRequisition.index');
        Route::get('/create', [JobRequisitionController::class, 'create'])->name('jobRequisition.create');
        Route::post('/store', [JobRequisitionController::class, 'store'])->name('jobRequisition.store');
        Route::get('/{id}', [JobRequisitionController::class, 'show'])->name('jobRequisition.show');
        Route::get('/{id}/edit', [JobRequisitionController::class, 'edit'])->name('jobRequisition.edit');
        Route::put('/{id}', [JobRequisitionController::class, 'update'])->name('jobRequisition.update');
        Route::delete('/{id}/delete', [JobRequisitionController::class, 'destroy'])->name('jobRequisition.delete');

        // Approval workflow routes
        Route::post('/{id}/submit', [JobRequisitionController::class, 'submitForApproval'])->name('jobRequisition.submit');
        Route::get('/{id}/approve', [JobRequisitionController::class, 'showApprovalForm'])->name('jobRequisition.approve.form');
        Route::post('/{id}/approve', [JobRequisitionController::class, 'approve'])->name('jobRequisition.approve');
        Route::get('/{id}/reject', [JobRequisitionController::class, 'showRejectionForm'])->name('jobRequisition.reject.form');
        Route::post('/{id}/reject', [JobRequisitionController::class, 'reject'])->name('jobRequisition.reject');

        // Conversion to job post
        Route::post('/{id}/convert', [JobRequisitionController::class, 'convertToJob'])->name('jobRequisition.convert');
    });

    Route::group(['sub_section' => 'job_candidate', 'prefix' => 'jobCandidate'], function () {
        Route::get('/', [JobCandidateController::class, 'index'])->name('jobCandidate.index');
        Route::get('applyCandidateList/{id}', [JobCandidateController::class, 'applyCandidateList'])->name('jobCandidate.applyCandidateList');
        Route::get('shortListedApplicant/{id}', [JobCandidateController::class, 'shortListedApplicant'])->name('jobCandidate.shortListedApplicant');
        Route::get('shortlist/{id}', [JobCandidateController::class, 'shortlist'])->name('applicant.shortlist');
        Route::get('reject/{id}', [JobCandidateController::class, 'reject'])->name('applicant.reject');
        Route::get('jobInterview/{id}', [JobCandidateController::class, 'jobInterview'])->name('applicant.jobInterview');
        Route::post('jobInterviewStore/{id}', [JobCandidateController::class, 'jobInterviewStore'])->name('applicant.jobInterviewStore');
        Route::get('rejectedApplicant/{id}', [JobCandidateController::class, 'rejectedApplicant'])->name('jobCandidate.rejectedApplicant');
        Route::get('jobInterviewList/{id}', [JobCandidateController::class, 'jobInterviewList'])->name('jobCandidate.jobInterviewList');
        Route::get('hireList/{id}', [JobCandidateController::class, 'jobHireList'])->name('jobCandidate.jobHireList');
        Route::get('hire/{id}', [JobCandidateController::class, 'hire'])->name('applicant.hire');
        Route::get('candidates/{job_id}', [JobCandidateController::class, 'searchCandidateList'])->name('applicants.search');
        Route::get('/{id}/view/CV', [JobCandidateController::class, 'viewResume'])->name('view.CV');
        Route::get('/{id}/download/CV', [JobCandidateController::class, 'downloadResume'])->name('download.CV');
    });
});
