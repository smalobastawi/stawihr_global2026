<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Surveys\SurveyController;
use App\Http\Controllers\Surveys\GoogleFormController;

Route::group(['middleware' => ['prevent-back-history', 'auth', 'permission']], function () {
    Route::group(['module' => 'Survey', 'section' => 'survey', 'prefix' => 'survey', 'as' => 'survey.'], function () {

        Route::get('/', [GoogleFormController::class, 'index'])->name('index');
        // Route to initiate the Google OAuth flow
        Route::get('/create', [GoogleFormController::class, 'showCreateForm'])->name('create');
        // Route to initiate the Google OAuth flow
        Route::get('/auth/google', [GoogleFormController::class, 'redirectToGoogle'])->name('google.auth');
        // Route Google redirects back to after authentication
        Route::get('/auth/google/callback', [GoogleFormController::class, 'handleGoogleCallback'])->name('auth.google.callback');
        // Example route to trigger creating a form (requires authentication first)
        Route::post('/forms/create', [GoogleFormController::class, 'createFormAction'])->name('forms.create');
        Route::post('/get-locations-by-regions', [GoogleFormController::class, 'getLocationsByRegions'])->name('getLocationsByRegions');

        // Edit forms 
        Route::get('/{survey}/edit', [GoogleFormController::class, 'showUpdateForm'])->name('edit');
        // Edit forms 
        Route::put('/{survey}/forms/update', [GoogleFormController::class, 'updateSurvey'])->name('forms.update');

        Route::get('/{survey}/targeted-employees', [GoogleFormController::class, 'showTargetedEmployees'])->name('targeted-employees');

        // Delete Survey
        Route::delete('/{id}/delete', [GoogleFormController::class, 'destroy'])->name('delete');

        Route::get('/{survey}/export-employees', [GoogleFormController::class, 'exportTargetedEmployees'])->name('export-employees');
    });
});
