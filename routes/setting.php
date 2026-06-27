<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Setting\ApprovalSettingController;
use App\Http\Controllers\Setting\FinancialYearController;
use App\Http\Controllers\Setting\SystemSettingController;
use App\Http\Controllers\Setting\ModuleSettingsController;
use App\Http\Controllers\Setting\DummyDataController;

Route::group(['module' => 'Settings', 'section' => 'settings', 'prefix' => 'settings', 'middleware' => ['prevent-back-history', 'auth', 'permission']], function () {

    //approval settings
    Route::group(['sub_section' => 'Approvals', 'prefix' => 'approvalSettings'], function () {

        Route::get('/', [ApprovalSettingController::class, 'index'])->name('approvalSettings.index');
        Route::post('/storeApprovalSettings', [ApprovalSettingController::class, 'store'])->name('approvalSettings.store');
        Route::get('/create', [ApprovalSettingController::class, 'create'])->name('approvalSettings.create');
        Route::get('/edit/{approval_setting}', [ApprovalSettingController::class, 'edit'])->name('approvalSettings.edit');
        Route::put('/update/{approval_setting}', [ApprovalSettingController::class, 'update'])->name('approvalSettings.update');
        Route::delete('/delete/{approvalSettings}', [ApprovalSettingController::class, 'destroy'])->name('approvalSettings.delete');
    });

    //Financial years
    Route::group(['sub_section' => 'Financial Years', 'prefix' => 'financial_years'], function () {
        Route::get('/', [FinancialYearController::class, 'index'])->name('financial_year.index');
        Route::post('/storeFinancial_year', [FinancialYearController::class, 'store'])->name('financial_year.store');
        Route::get('/create', [FinancialYearController::class, 'create'])->name('financial_year.create');
        Route::get('/edit/{id}', [FinancialYearController::class, 'edit'])->name('financial_year.edit');
        Route::put('/update/{financial_year}', [FinancialYearController::class, 'update'])->name('financial_year.update');
        Route::delete('/delete/{financial_year}', [FinancialYearController::class, 'destroy'])->name('financial_year.delete');
    });

    // System Settings
    Route::group(['sub_section' => 'System Settings', 'prefix' => 'system-settings'], function () {
        Route::get('/', [SystemSettingController::class, 'index'])->name('systemSettings.index');
        Route::put('/', [SystemSettingController::class, 'update'])->name('systemSettings.update');
        Route::post('/test-email', [SystemSettingController::class, 'testEmail'])->name('systemSettings.testEmail');
        Route::post('/test-sms', [SystemSettingController::class, 'testSms'])->name('systemSettings.testSms');
        Route::post('/test-inapp', [SystemSettingController::class, 'testInApp'])->name('systemSettings.testInApp');
    });

    // Module activation settings
    Route::group(['sub_section' => 'Module Settings', 'prefix' => 'module-settings'], function () {
        Route::get('/', [ModuleSettingsController::class, 'index'])->name('moduleSettings.index');
        Route::put('/', [ModuleSettingsController::class, 'update'])->name('moduleSettings.update');
    });

    // Dummy / test data generator
    Route::group(['sub_section' => 'Dummy Test Data', 'prefix' => 'dummy-data'], function () {
        Route::get('/', [DummyDataController::class, 'index'])->name('dummyData.index');
        Route::post('/generate', [DummyDataController::class, 'generate'])->name('dummyData.generate');
        Route::delete('/', [DummyDataController::class, 'destroy'])->name('dummyData.destroy');
    });

});
