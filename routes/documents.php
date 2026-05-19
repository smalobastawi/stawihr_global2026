<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Setting\GeneralSettingController;
use App\Http\Controllers\Setting\FrontSettingController;
use App\Http\Controllers\Setting\ServicesController;
use App\Http\Controllers\Setting\ApprovalSettingController;

use App\Http\Controllers\CompanySettingsController;
use App\Http\Controllers\Hr\DocumentsUploadController;
use App\Http\Controllers\Hr\DocumentCategoriesController;

    //approval settings
    Route::group(['module'=>'HR Uploads','section'=>'documents','prefix' => 'HR', 'middleware' => ['prevent-back-history', 'auth', 'permission']], function () {
        
        Route::group(['prefix' => 'document-categories','sub_section'=>'document_categories'], function () {
            Route::get('/', [DocumentCategoriesController::class, 'index'])->name('document-categories.index');
            Route::get('/create', [DocumentCategoriesController::class, 'create'])->name('document-categories.create');
            Route::post('/store', [DocumentCategoriesController::class, 'store'])->name('document-categories.store');
            Route::get('/{documentCategory}/edit', [DocumentCategoriesController::class, 'edit'])->name('document-categories.edit');
            Route::put('/{documentCategory}', [DocumentCategoriesController::class, 'updateDocumentCategory'])->name('document-categories.update');
            Route::delete('/{documentCategory}/delete', [DocumentCategoriesController::class, 'destroyDocumentCategory'])->name('document-categories.delete');
        });

        Route::group(['prefix' => 'documents-upload','sub_section'=>'deleted-documents'], function () {

        Route::get('deleted-documents', [DocumentsUploadController::class, 'listTrashed'])->name('documents-upload.deleted-docs');
        Route::post('/{document}/restore-deleted-document', [DocumentsUploadController::class, 'restoreDocument'])->name('documents-upload.restore-document');
        Route::get('/{document}/deleted-document', [DocumentsUploadController::class, 'viewDeletedDocument'])->name('documents-upload.show-deleted-document');

        });

        Route::group(['prefix' => 'documents-upload','sub_section'=>'document_uploads'], function () {
            Route::get('/', [DocumentsUploadController::class, 'index'])->name('documents-upload.index');
            Route::get('/create', [DocumentsUploadController::class, 'create'])->name('documents-upload.create');
            //show method in the routing
            Route::get('/{document}', [DocumentsUploadController::class, 'show'])->name('documents-upload.show');
            Route::post('/store', [DocumentsUploadController::class, 'store'])->name('documents-upload.store');
            Route::get('/{document}/edit', [DocumentsUploadController::class, 'edit'])->name('documents-upload.edit');
            Route::put('/{document}', [DocumentsUploadController::class, 'update'])->name('documents-upload.update');
            Route::delete('/{document}/delete', [DocumentsUploadController::class, 'destroy'])->name('documents-upload.delete');
            Route::get('/{document}/review', [DocumentsUploadController::class, 'review'])->name('documents-upload.review');
            Route::put('/{document}/update-review', [DocumentsUploadController::class, 'updateReview'])->name('documents-upload.update-review');
            Route::get('/{document}/render-document', [DocumentsUploadController::class, 'renderDocument'])->name('documents-upload.show-document');

            // File serving routes
            Route::get('/{document}/serve', [DocumentsUploadController::class, 'serveDocument'])->name('documents-upload.serve');
            Route::get('/{document}/download', [DocumentsUploadController::class, 'downloadDocument'])->name('documents-upload.download');

            // Document Consent Routes
            Route::get('/{document}/consents', [DocumentsUploadController::class, 'showConsents'])->name('documents-upload.consents');
            Route::get('/{document}/consents/download', [DocumentsUploadController::class, 'downloadConsentReport'])->name('documents-upload.consents.download');
        });

        // Consent Summary Route
        Route::get('/documents-consent-summary', [DocumentsUploadController::class, 'consentSummary'])->name('documents-upload.consent-summary');

        //add sub-section of approve ,delete
    });