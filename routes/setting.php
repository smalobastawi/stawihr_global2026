<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Setting\GeneralSettingController;
use App\Http\Controllers\Setting\FrontSettingController;
use App\Http\Controllers\Setting\ServicesController;
use App\Http\Controllers\Setting\ApprovalSettingController;

use App\Http\Controllers\CompanySettingsController;
use App\Http\Controllers\Setting\FinancialYearController;
use App\Http\Controllers\Setting\SystemSettingController;

Route::group(['module' => 'Settings', 'section' => 'settings', 'prefix' => 'settings', 'middleware' => ['prevent-back-history', 'auth', 'permission']], function () {

    Route::group(['sub_section' => 'General', 'prefix' => 'generalSettings'], function () {
        Route::get('/', [GeneralSettingController::class, 'index'])->name('generalSettings.index');
        Route::post('/', [GeneralSettingController::class, 'store'])->name('generalSettings.store');
        Route::get('/{generalSettings}/edit', [GeneralSettingController::class, 'edit'])->name('generalSettings.edit');
        Route::put('/{generalSettings}', [GeneralSettingController::class, 'update'])->name('generalSettings.update');

        Route::post('printHeadSettings', [GeneralSettingController::class, 'printHeadSettingsStore'])->name('printHeadSettings.store');
        Route::put('printHeadSettings/{id}', [GeneralSettingController::class, 'printHeadSettingsUpdate'])->name('printHeadSettings.update');
    });

    // front end setting
    Route::group(['sub_section' => 'Front End', 'prefix' => 'approvalSettings'], function () {
        Route::resource('service', ServicesController::class);
        // front end settings control
        Route::get('setting-front-page', [FrontSettingController::class, 'index'])->name('front.setting');
        Route::post('setting-front-page', [FrontSettingController::class, 'store'])->name('front.setting.submit');
        Route::get('company_settings', [CompanySettingsController::class, 'index'])->name('company.setting');
        Route::post('company_settings1', [CompanySettingsController::class, 'store'])->name('company.setting.post');
    });


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

});
