<?php

use App\Http\Controllers\CompanyPermissionsController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PermissionsController;
use App\Http\Controllers\RolesController;

Route::group(['module' => 'Administration', 'section' => 'role_permissions', 'prefix' => 'permissions', 'middleware' => ['prevent-back-history', 'auth', 'permission']], function () {

    Route::group(['sub_section' => 'permissions'], function () {
        Route::resource('permissions', PermissionsController::class);
    });
    Route::group(['sub_section' => 'roles'], function () {
        Route::resource('roles', RolesController::class);
    });
});

Route::group(['sub_section' => 'company_permissions', 'prefix' => 'company-permissions', 'middleware' => ['prevent-back-history', 'auth', 'permission']], function () {

    Route::get('index', [CompanyPermissionsController::class, 'index'])->name('company.permissions.index');
    Route::get('create', [CompanyPermissionsController::class, 'create'])->name('company.permissions.create');
    Route::get('view/{id}', [CompanyPermissionsController::class, 'view'])->name('company.permissions.view');
    Route::get('edit/{id}', [CompanyPermissionsController::class, 'edit'])->name('company.permissions.edit');
    Route::post('store', [CompanyPermissionsController::class, 'store'])->name('company.permissions.store');
    Route::put('update/{id}', [CompanyPermissionsController::class, 'update'])->name('company.permissions.update');
    Route::delete('delete/{id}', [CompanyPermissionsController::class, 'delete'])->name('company.permissions.delete');
    Route::post('get-permissions', [CompanyPermissionsController::class, 'getPermissions'])->name('company.permissions.get');
});