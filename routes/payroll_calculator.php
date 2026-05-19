<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Payroll\PayrollCalculatorController;
use App\Http\Controllers\Payroll\PayrollCalculatorInternalController;
use Illuminate\Support\Facades\Auth;

//check if user is authenticated

Route::group(['module'=>'Payroll','section'=>'Payroll Calculator','sub_section'=>'nssf_nhif..','prefix' => 'payroll_caculator', 'middleware' => [ 'auth']], function () {
    Route::get('/', [PayrollCalculatorInternalController::class, 'index'])->name('payrollcaculator.index');
    Route::get('/paye', [PayrollCalculatorInternalController::class, 'calculate_PAYE'])->name('payrollcaculator.paye');
    Route::get('/nssf', [PayrollCalculatorInternalController::class, 'calculateNSSF'])->name('payrollcaculator.nssf');
    Route::get('/nhif', [PayrollCalculatorInternalController::class, 'calculateNHIF'])->name('payrollcaculator.nhif');
    Route::get('/shif', [PayrollCalculatorInternalController::class, 'calculateSHIF'])->name('payrollcaculator.shif');
    Route::get('/ahl', [PayrollCalculatorInternalController::class, 'calculateAHL_employee'])->name('payrollcaculator.ahl');
    Route::get('/gross', [PayrollCalculatorInternalController::class, 'index'])->name('payrollcaculator.gross');
    Route::get('/personal_relief', [PayrollCalculatorInternalController::class, 'index'])->name('payrollcaculator.personal_relief');
    Route::get('/insurance_relief', [PayrollCalculatorInternalController::class, 'index'])->name('payrollcaculator.insurance_relief');
    Route::get('/taxable_pay', [PayrollCalculatorInternalController::class, 'index'])->name('payrollcaculator.taxable_pay');
    Route::get('/net_pay', [PayrollCalculatorInternalController::class, 'index'])->name('payrollcaculator.net_pay');

});

Route::group(['prefix' => '/guest/payroll_caculator', 'middleware' => ['guest']], function () {
    Route::get('/', [PayrollCalculatorController::class, 'index'])->name('payrollcaculator_index');
    Route::get('/paye', [PayrollCalculatorController::class, 'calculate_PAYE'])->name('payrollcaculator_paye');
    Route::get('/nssf', [PayrollCalculatorController::class, 'calculateNSSF'])->name('payrollcaculator_nssf');
    Route::get('/nhif', [PayrollCalculatorController::class, 'calculateNHIF'])->name('payrollcaculator_nhif');
    Route::get('/ahl', [PayrollCalculatorController::class, 'calculateAHL_employee'])->name('payrollcaculator_ahl');
    Route::get('/gross', [PayrollCalculatorController::class, 'index'])->name('payrollcaculator_gross');
    Route::get('/personal_relief', [PayrollCalculatorController::class, 'index'])->name('payrollcaculator_personal_relief');
    Route::get('/insurance_relief', [PayrollCalculatorController::class, 'index'])->name('payrollcaculator_insurance_relief');
    Route::get('/taxable_pay', [PayrollCalculatorController::class, 'index'])->name('payrollcaculator_taxable_pay');
    Route::get('/net_pay', [PayrollCalculatorController::class, 'index'])->name('payrollcaculator_net_pay');

});

    


