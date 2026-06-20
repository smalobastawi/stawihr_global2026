<?php

use Illuminate\Support\Facades\Route;
use \App\Http\Controllers\LicenseController;
use App\Http\Controllers\AccountsController;
use App\Http\Controllers\Front\WebController;
use App\Http\Controllers\User\HomeController;
use App\Http\Controllers\User\RoleController;
use App\Http\Controllers\User\UserController;
use App\Http\Controllers\User\LoginController;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\NewApprovalController;
use App\Http\Controllers\Recruitment\JobPostController;
use App\Http\Controllers\User\ChangePasswordController;
use App\Http\Controllers\User\RolePermissionController;
use App\Http\Controllers\Payroll\EmployeeDeductionsController;
use App\Http\Controllers\Payroll\ProgramAllocationController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\EthnicityController;
use App\Http\Controllers\SubscriptionSuspensionController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

//Route::get('/', function () {
//    return view('welcome');
//});

////Uncomment to activate the license check
//Route::group(['middleware' => ['license']], function() {
//    Route::get('login', [LoginController::class, 'index'])->name('login');
//    Route::post('login', [LoginController::class, 'Auth']);
//});

// front page route

Route::group(['middleware' => ['guest']], function () {

    Route::get('login', [LoginController::class, 'index'])->name('login');
    Route::post('login', [LoginController::class, 'Auth']);
    Route::get('verify', [LoginController::class, 'verify'])->name('verify');
    Route::post('verify-otp', [LoginController::class, 'verifyOTP'])->name('verify-otp');
    Route::post('resend-otp', [LoginController::class, 'resendOtp'])->name('resend-otp');

    Route::get('/auth/google', [GoogleController::class, 'redirectToGoogle'])->name('auth.google');
    Route::get('/auth/google/callback', [GoogleController::class, 'handleGoogleCallback'])->name('auth.google.callback');

    Route::get('/', [WebController::class, 'index']);
    Route::get('job/{id}/{slug?}', [WebController::class, 'jobDetails'])->name('job.details');
    Route::get('job/{id}/{slug?}/apply', [WebController::class, 'jobApplyForm'])->name('job.apply.form');
    Route::post('external/apply', [WebController::class, 'jobApply'])->name('job.external.apply');

    Route::get('/test-notification', function () {
        $leave = App\Models\LeaveApplication::first();
        $user = App\Models\User::first();

        $user->notify(new App\Notifications\LeaveApplicationSubmitted($leave));
        return 'Notification sent!';
    });
});



Route::get('subscription/suspended', [SubscriptionSuspensionController::class, 'show'])
    ->middleware(['auth'])
    ->name('subscription.suspended');

Route::group(['middleware' => ['web']], function () {
    Route::get('/job/{job_id}/view/description', [JobPostController::class, 'viewJdFile'])->name('jobPost.viewDescription');
    Route::get('/job/{job_id}/download/description', [JobPostController::class, 'downloadJdFile'])->name('jobPost.downloadDescription');
});

// User guide entry — redirects once to static HTML docs; all other guide pages are static under /docs/user-guide/
Route::get('/user-guide', function () {
    return redirect('/docs/user-guide/index.html');
})->middleware('auth')->name('user.guide');
Route::group(['module' => 'Administration', 'middleware' => ['prevent-back-history', 'auth', 'permission']], function () {

    Route::get('internalJob/{id}/{slug?}', [WebController::class, 'internalJobDetails'])->name('job.internal_details');

    Route::post('internal/apply', [WebController::class, 'jobApply'])->name('job.internal.apply');;

    Route::get('dashboard', [HomeController::class, 'index'])->name('home.dashboard');
    Route::get('profile', [HomeController::class, 'profile'])->name('home.profile');
    Route::post('send-password-change-otp', [LoginController::class, 'sendPasswordChangeOtp'])->name('send-password-change-otp-web');

    Route::get('logout', [LoginController::class, 'logout'])->name('home.logout');

    // Program Allocation Routes
    //Route::get('employee/{employee}/program-allocation/create', [ProgramAllocationController::class, 'create'])->name('program-allocation.create');
    // Route::post('employee/{employee}/program-allocation', [ProgramAllocationController::class, 'store'])->name('program-allocation.store');
    //Route::get('program-allocation/{id}/edit', [ProgramAllocationController::class, 'edit'])->name('program-allocation.edit');
    // Route::put('program-allocation/{id}', [ProgramAllocationController::class, 'update'])->name('program-allocation.update');
    //Route::delete('program-allocation/{id}', [ProgramAllocationController::class, 'destroy'])->name('program-allocation.delete');

    //   require __DIR__.'/project.php';

    // Employee Earnings and Benefits
    Route::post('employee/{employee}/update-earnings-benefits', [App\Http\Controllers\Employee\EmployeeController::class, 'updateEarningsAndBenefits'])->name('employee.updateEarningsAndBenefits');

    Route::post('employee/{employee}/deductions', [App\Http\Controllers\Employee\EmployeeController::class, 'addDeduction'])->name('employee.addDeduction.web');
    Route::put('employee/deductions/{id}', [App\Http\Controllers\Employee\EmployeeController::class, 'updateDeduction'])->name('employee.updateDeduction.web');
    Route::delete('employee/deductions/{id}', [App\Http\Controllers\Employee\EmployeeController::class, 'deleteDeduction'])->name('employee.deleteDeduction.web');

    Route::group(['section' => 'user', 'sub_section' => 'user'], function () {
        Route::resource('user', UserController::class)->parameters(['user' => 'id']);
        Route::post('user/{id}/restore', [UserController::class, 'restore'])->name('user.restore');
        Route::get('users/inactive', [UserController::class, 'indexInactive'])->name('user.inactive');
        Route::get('users/active', [UserController::class, 'indexActive'])->name('user.active');
    });

    Route::group(['section' => 'role_permissions', 'sub_section' => 'roles'], function () {
        Route::resource('userRole', RoleController::class)->parameters(['userRole' => 'role_id']);
        Route::resource('rolePermission', RolePermissionController::class)->parameters(['rolePermission' => 'id']);
        Route::post('rolePermission/get_all_menu', [RolePermissionController::class, 'getAllMenu'])->name('roles.permission.menus');
    });

    Route::group(['section' => 'company', 'sub_section' => 'company'], function () {
        Route::post('company/switch', [CompanyController::class, 'switch'])->name('company.switch');
        Route::resource('company', CompanyController::class);
    });



    //commented routes to avoid duslocation


    // // Batch approval routes



});

Route::get('local/{language}', function ($language) {

    session(['my_locale' => $language]);

    return redirect()->back();
});

Route::group(['section' => 'password_managent', 'sub_section' => 'passwords'], function () {
    Route::post('reset_password_without_token', [AccountsController::class, 'validatePasswordRequest'])->name('reset_password_without_token');
    Route::post('reset_password_with_token', [AccountsController::class, 'validatePasswordRequest'])->name('reset_password_with_token');
    Route::get('password/reset/{token}', [AccountsController::class, 'resetForm'])->name('password.reset.token');
    Route::post('resetPassword', [AccountsController::class, 'resetPassword'])->name('resetPassword');
    Route::resource('changePassword', ChangePasswordController::class)->parameters(['changePassword' => 'id']);
    Route::post('sendPasswordReset/{id}', [AccountsController::class, 'sendPasswordReset'])->name('sendPasswordReset');

    Route::get('app_license', [LicenseController::class, 'openLicenses'])->name('licenses');
    Route::get('invalid_license', [LicenseController::class, 'invalidLicense'])->name('invalidLicense');

    Route::get('/login/azure', [LoginController::class, 'redirectToAzure'])->name('azure.login');
    Route::get('/login/azure/callback', [LoginController::class, 'handleAzureCallback'])->name('azure.login.callback');
});
