<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Payroll\TaxSetupController;
use App\Http\Controllers\Payroll\SalaryDeductionRuleController;
use App\Http\Controllers\Payroll\AllowanceController;
use App\Http\Controllers\Payroll\DeductionController;
use App\Http\Controllers\Payroll\HourlyWagesPayrollController;
use App\Http\Controllers\Payroll\GenerateSalarySheet;
use App\Http\Controllers\Payroll\WorkHourApprovalController;
use App\Http\Controllers\Payroll\BonusSettingController;
use App\Http\Controllers\Payroll\GenerateBonusController;
use App\Http\Controllers\DataImportController;
use App\Http\Controllers\Employee\EmployeeController;
use App\Http\Controllers\Employee\LocationController as EmployeeLocationController;
use App\Http\Controllers\Payroll\Payroll9Controller;
use App\Http\Controllers\Payroll\NHIFController;
use App\Http\Controllers\Payroll\AllowanceTypeController;
use App\Http\Controllers\Payroll\DeductionTypeController;
use App\Http\Controllers\Payroll\SalaryBonusController;
use App\Http\Controllers\Payroll\SalaryBonusTypesController;
use App\Http\Controllers\Payroll\GeneratePayroll;
use App\Http\Controllers\Payroll\PayoutChannelController;
use \App\Http\Controllers\Payroll\PayrollReportsController;
use App\Http\Controllers\Payroll\PayeTaxBandController;
use App\Http\Controllers\PayrollEarningTypesController;
use App\Http\Controllers\Payroll\EmployeeEarningsController;
use App\Http\Controllers\Payroll\EmployeeDeductionsController;
use App\Http\Controllers\Payroll\EmployeePayrollController;
use App\Http\Controllers\Payroll\PayrollController;
use App\Http\Controllers\Payroll\PayrollPeriodController;
use App\Http\Controllers\Payroll\PensionSchemeController;
use App\Http\Controllers\Payroll\ReportsController;
use App\Http\Controllers\Payroll\PayrollClaimController;
use App\Models\Payroll\DeductionType;
use App\Models\PayrollEarningTypes;
use App\Http\Controllers\Payroll\BankController;
use App\Http\Controllers\Payroll\BranchesController;
use App\Http\Controllers\Payroll\LocationController;
use App\Http\Controllers\Payroll\SalaryHistoryController;
use App\Http\Controllers\Payroll\LoanController;
use App\Http\Controllers\Payroll\LoanTypeController;
use App\Http\Controllers\Payroll\LoanApplicationController;
use App\Http\Controllers\Payroll\ManualLoanDeductionController;
use App\Http\Controllers\Payroll\LoanReportController;


Route::group(['module' => 'Payroll', 'prefix' => 'payroll', 'middleware' => ['auth', 'permission']], function () {

    Route::get('/', [GeneratePayroll::class, 'payrollIndex'])->name('payrollIndex');
    Route::group(['sub_section' => 'taxes', 'prefix' => 'taxSetup'], function () {
        Route::get('/', [TaxSetupController::class, 'index'])->name('taxSetup.index');
        Route::post('updateTaxRule', [TaxSetupController::class, 'updateTaxRule'])->name('update.taxRule');
    });

    Route::group(['section' => 'setup', 'sub_section' => 'deduction', 'prefix' => 'salaryDeductionRuleForLateAttendance'], function () {
        Route::get('/', [SalaryDeductionRuleController::class, 'index'])->name('salaryDeductionRule.index');
        Route::post('updateSalaryDeductionRule', [SalaryDeductionRuleController::class, 'updateSalaryDeductionRule'])->name('salary.deduction_typesrule.update');
    });

    Route::group(['section', 'setup', 'sub_section' => 'allowance', 'prefix' => 'allowance'], function () {
        Route::get('/', [AllowanceController::class, 'index'])->name('allowance.index');
        Route::get('/create', [AllowanceController::class, 'create'])->name('allowance.create');
        Route::post('/store', [AllowanceController::class, 'store'])->name('allowance.store');
        Route::get('/{allowance}/edit', [AllowanceController::class, 'edit'])->name('allowance.edit');
        Route::put('/{allowance}', [AllowanceController::class, 'update'])->name('allowance.update');
        Route::delete('/{allowance}/delete', [AllowanceController::class, 'destroy'])->name('allowance.delete');
    });

    Route::group(['section' => 'setup', 'sub_section' => 'deduction_types', 'prefix' => 'deduction_types'], function () {
        Route::get('/', [DeductionTypeController::class, 'index'])->name('deduction_types.index');
        Route::get('/create', [DeductionTypeController::class, 'create'])->name('deduction_types.create');
        Route::post('/store', [DeductionTypeController::class, 'store'])->name('deduction_types.store');
        Route::get('/{deduction}/edit', [DeductionTypeController::class, 'edit'])->name('deduction_types.edit');
        Route::put('/{deduction}', [DeductionTypeController::class, 'update'])->name('deduction_types.update');
        Route::delete('/{deduction}/delete', [DeductionTypeController::class, 'destroy'])->name('deduction_types.delete');
    });

    Route::group(['section' => 'setup', 'sub_section' => 'hourly_wages', 'prefix' => 'hourlyWages'], function () {

        Route::resource('hourlyWages', HourlyWagesPayrollController::class);

        //        Route::get('/',['as' => 'hourlyWages.index', HourlyWagesPayrollController::class, 'index']);
        //        Route::get('/create',['as' => 'hourlyWages.create', HourlyWagesPayrollController::class, 'create']);
        //        Route::post('/store',['as' => 'hourlyWages.store', HourlyWagesPayrollController::class, 'store']);
        //        Route::get('/{hourlyWages}/edit',['as'=>'hourlyWages.edit',HourlyWagesPayrollController::class, 'edit']);
        //        Route::put('/{hourlyWages}',['as' => 'hourlyWages.update', HourlyWagesPayrollController::class, 'update']);
        //        Route::delete('/{hourlyWages}/delete',['as'=>'hourlyWages.delete',HourlyWagesPayrollController::class, 'destroy']);
    });

    Route::group(['section' => 'salaries', 'sub_section' => 'salary_generation'], function () {
        Route::get('generateSalarySheet', [GenerateSalarySheet::class, 'index'])->name('generateSalarySheet.index');
        Route::get('generateSalarySheet/create', [GenerateSalarySheet::class, 'create'])->name('generateSalarySheet.create');
        Route::get('generateSalarySheet/calculateEmployeeSalary', [GenerateSalarySheet::class, 'calculateEmployeeSalary'])->name('generateSalarySheet.calculateEmployeeSalary');
        Route::post('/store', [GenerateSalarySheet::class, 'store'])->name('saveEmployeeSalaryDetails.store');
        Route::post('generateSalarySheet/makePayment', [GenerateSalarySheet::class, 'makePayment'])->name('makePayment');
        Route::get('generateSalarySheet/generatePayslip/{id}', [GenerateSalarySheet::class, 'generatePayslip'])->name('generatePayslip');
        Route::get('generateSalarySheet/generateSelfPayslip/{id}', [GenerateSalarySheet::class, 'generatePayslip'])->name('generatePayslip.self');
        Route::get('generateSalarySheet/monthSalary', [GenerateSalarySheet::class, 'monthSalary'])->name('generateSalarySheet.monthSalary');

        Route::get('paymentHistory', [GenerateSalarySheet::class, 'paymentHistory'])->name('paymentHistory.paymentHistory.view');
        Route::post('paymentHistory', [GenerateSalarySheet::class, 'paymentHistory'])->name('paymentHistory.paymentHistory.post');
        Route::get('paymentHistory/generatePayslip/{id}', [GenerateSalarySheet::class, 'generatePayslip'])->name('payroll.paymenthistory.generate');


        Route::get('downloadPayslip/{id}', [GenerateSalarySheet::class, 'downloadPayslip'])->name('downloadPayslip');
        Route::get('downloadSelfPayslip/{id}', [GenerateSalarySheet::class, 'downloadPayslip'])->name('downloadPayslip.self');
        Route::get('downloadMyPayroll', [GenerateSalarySheet::class, 'downloadMyPayroll'])->name('payroll.download');
        Route::get('downloadFullPayroll', ['name' => 'downloadFullPayroll', GenerateSalarySheet::class, 'downloadFullPayroll'])->name('payroll.download.full');
        Route::get('downloadMgntPayslips', ['name' => 'downloadMgntPayslips', GenerateSalarySheet::class, 'downloadMgntPayslips'])->name('payroll.download.payslip');

        Route::get('workHourApproval', [WorkHourApprovalController::class, 'create'])->name('workHourApproval.create');
        Route::get('workHourApproval/filter', [WorkHourApprovalController::class, 'filter'])->name('workHourApproval.filter');
        Route::post('workHourApproval', [WorkHourApprovalController::class, 'store'])->name('workHourApproval.store');


        /*Routes for the new generating payroll
    *Do ot remove, please please
    */
        Route::get('generatePayroll', [GenerateSalarySheet::class, 'calculateEmployeeSalary1'])->name('generateSalary.massGenerate');
    });
    Route::group(['section' => 'setup'], function () {
        Route::group(['sub_section' => 'bonus', 'prefix' => 'bonusSetting'], function () {
            Route::get('/', [BonusSettingController::class, 'index'])->name('bonusSetting.index');
            Route::get('/create', [BonusSettingController::class, 'create'])->name('bonusSetting.create');
            Route::post('/store', [BonusSettingController::class, 'store'])->name('bonusSetting.store');
            Route::get('/{bonusSetting}/edit', [BonusSettingController::class, 'edit'])->name('bonusSetting.edit');
            Route::put('/{bonusSetting}', [BonusSettingController::class, 'update'])->name('bonusSetting.update');
            Route::delete('/{bonusSetting}/delete', [BonusSettingController::class, 'destroy'])->name('bonusSetting.delete');
        });

        Route::group(['sub_section' => 'generate_bonus', 'prefix' => 'generateBonus'], function () {
            Route::get('/', [GenerateBonusController::class, 'index'])->name('generateBonus.index');
            Route::get('/create', [GenerateBonusController::class, 'create'])->name('generateBonus.create');
            Route::post('/store', [GenerateBonusController::class, 'store'])->name('saveEmployeeBonus.store');
            Route::get('/filter', [GenerateBonusController::class, 'filter'])->name('generateBonus.filter');
        });
    });
    Route::group(['section' => 'setup', 'sub_section' => 'pay_group_job_category'], function () {
        Route::resource('bonus_types', SalaryBonusTypesController::class);

        Route::resource('nhif', NHIFController::class);

        Route::resource('bonuses', SalaryBonusController::class);
    });


    Route::group(['section' => 'salaries', 'sub_section' => 'payroll9'], function () {
        Route::get('generatePayrollExcel', [GenerateSalarySheet::class, 'generatePayrollExcel'])->name('generatePayrollExcel');
        //Management pay here
        Route::get('managementPay', [GenerateSalarySheet::class, 'managementPay'])->name('managementPay');
        Route::get('managementPayIndex', [GeneratePayroll::class, 'managementPayIndex'])->name('managementPay.index');
        Route::get('calculateManagementPay', [GenerateSalarySheet::class, 'calculateManagementPay'])->name('calculateManagementPay');
        //    //General route for generating PAYE b
        Route::get('calculatePaye', [GenerateSalarySheet::class, 'calculatePaye'])->name('calculatePaye');

        Route::get('deleteSalaryEntry/{salaryDetailsId}', [GenerateSalarySheet::class, 'deletePayrollDetails'])->name('delete_salary_entry');

        Route::get('PayrollIndex', [GeneratePayroll::class, 'payrollIndex'])->name('payrollIndex3');
        Route::get('genManagementPayroll', [GenerateSalarySheet::class, 'managementPay'])->name('geneMgtPayroll');
        Route::get('PayrollIndex1', [GeneratePayroll::class, 'payrollRequest'])->name('generate_payroll_request');
        Route::post('PayrollIndex2', [GeneratePayroll::class, 'managementPayrollRequest'])->name('generate_payroll_request_mgmt');


        Route::resource('payroll9', Payroll9Controller::class);
        Route::get('/payroll9.preview', [Payroll9Controller::class, 'preview'])->name('payroll9.preview');
        Route::post('/payroll9.preview1', [Payroll9Controller::class, 'newGeneratePreview'])->name('payroll9.preview1');
        Route::post('/payroll9.preview2', [Payroll9Controller::class, 'newGenerate'])->name('payroll9.preview2');
        Route::post('/payroll9.pdfExport', [Payroll9Controller::class, 'exportP9PDF'])->name('payroll9.generate');
        Route::get('/payroll9.massMail', [Payroll9Controller::class, 'massMailP9'])->name('payroll9.massMail');

        Route::get('/paye-report', [Payroll9Controller::class, 'payeReportIndex'])->name('paye.report.index');

        //New excel export here
        Route::get('/payrollView', [GeneratePayroll::class, 'newPayrollExport'])->name('payroll.view');

        Route::post('newSalaryCalculator', [GenerateSalarySheet::class, 'newEmployeeSalaryCalculator'])->name('newSalaryCalculate');
        Route::post('newManagementSalaryCalculator', [GenerateSalarySheet::class, 'newcalculateManagementPay'])->name('newManagementSalaryCalculate');
        Route::get('PayrollIndex', [GeneratePayroll::class, 'payrollIndex'])->name('payrollIndex2');
        Route::post('/payrollDataExport', [GeneratePayroll::class, 'payrollDataExport'])->name('payrollDataExport');
        Route::post('/managementPayrollDataExport', [GeneratePayroll::class, 'managementPayrollDataExport'])->name('managementPayrollDataExport');
    });
    Route::group(['section' => 'reports', 'sub_section' => 'payroll_reports', 'prefix' => 'reports'], function () {

        Route::get('/nhif_home', [PayrollReportsController::class, 'nhifReportIndex'])->name('nhifReportsIndex');
        // Route::get('/nhifExport', [PayrollReportsController::class, 'nhifReportExport'])->name('nhifReportExport');
        Route::get('/shif_home', [PayrollReportsController::class, 'shifReportIndex'])->name('shifReportsIndex');

        Route::get('/nssf_home', [PayrollReportsController::class, 'nssfReportIndex'])->name('nssfReportsIndex');

        Route::get('/housingLevy_home', [PayrollReportsController::class, 'ahlReportIndex'])->name('ahlReportIndex');
        Route::get('deductions-report', [PayrollReportsController::class, 'deductionsReport'])->name('payroll.reports.deductions');
        Route::post('deductions-report/export', [PayrollReportsController::class, 'exportDeductionsReport'])->name('payroll.reports.deductions.export');

        // Earnings Report
        Route::get('earnings-report', [PayrollReportsController::class, 'earningsReport'])->name('payroll.reports.earnings');
        Route::post('earnings-report/export', [PayrollReportsController::class, 'exportEarningsReport'])->name('payroll.reports.earnings.export');

        // Variance Reports
        Route::get('variance-report', [PayrollReportsController::class, 'varianceReport'])->name('payroll.reports.variance');
        Route::post('variance-report/export', [PayrollReportsController::class, 'exportVarianceReport'])->name('payroll.reports.variance.export');
    });


    Route::group(['section' => 'setup', 'sub_section' => 'payout_channels', 'prefix' => 'payoutChannels'], function () {
        Route::get('/', [PayoutChannelController::class, 'index'])->name('payoutChannel.index');
        Route::get('/create', [PayoutChannelController::class, 'create'])->name('payoutChannel.create');
        Route::post('/store', [PayoutChannelController::class, 'store'])->name('payoutChannel.store');
        Route::get('/show', [PayoutChannelController::class, 'show'])->name('payoutChannel.show');
        Route::put('/update{id}', [PayoutChannelController::class, 'update'])->name('payoutChannel.update');
        Route::get('/{payoutChannel}/edit', [PayoutChannelController::class, 'edit'])->name('payoutChannel.edit');
        Route::delete('/{id}/delete', [PayoutChannelController::class, 'destroyEmpliyeePayoutChannel'])->name('payoutChannel.delete');
        Route::post('/updateStaff/{id}', [EmployeeController::class, 'storeOrUpdatePayoutChannel'])->name('payoutChannel.updateStaff');
        Route::delete('/removeFromStaff/{id}', [EmployeeController::class, 'deleteFromStaff'])->name('payoutChannel.deleteFromStaff');
    });
    Route::group(['section' => 'setup', 'sub_section' => 'paye_tax', 'prefix' => 'paye_tax'], function () {
        Route::get('/', [PayeTaxBandController::class, 'index'])->name('tax-bands.index');
        Route::get('/create', [PayeTaxBandController::class, 'create'])->name('tax-bands.create');
        Route::post('/store', [PayeTaxBandController::class, 'store'])->name('tax-bands.store');
        Route::get('/show/{countryID}', [PayeTaxBandController::class, 'show'])->name('tax-bands.show');
        Route::get('/{countryID}/edit', [PayeTaxBandController::class, 'edit'])->name('tax-bands.edit');
        Route::put('/update/{countryID}', [PayeTaxBandController::class, 'update'])->name('tax-bands.update');
        Route::delete('delete//{countryID}', [PayeTaxBandController::class, 'destroy'])->name('tax-bands.destroy');
        Route::get('/tax-bands/{countryId}/get-tax-bands', [PayeTaxBandController::class, 'getTaxBandsAjax'])->name('tax-bands.get-tax-bands');
    });
    Route::group(['section' => 'setup', 'sub_section' => 'earning_types', 'prefix' => 'earning_types'], function () {
        Route::get('/', [PayrollEarningTypesController::class, 'index'])->name('earning_types.index');
        Route::get('/create', [PayrollEarningTypesController::class, 'create'])->name('earning_types.create');
        Route::post('/store', [PayrollEarningTypesController::class, 'store'])->name('earning_types.store');
        Route::get('/{id}/edit', [PayrollEarningTypesController::class, 'edit'])->name('earning_types.edit');
        Route::put('/update/{id}', [PayrollEarningTypesController::class, 'update'])->name('earning_types.update');
        Route::delete('delete/{id}', [PayrollEarningTypesController::class, 'destroy'])->name('earning_types.delete');
        Route::delete('destroy/{id}', [PayrollEarningTypesController::class, 'destroy'])->name('earning_types.destroy');
        Route::get('/{id}/details', [PayrollEarningTypesController::class, 'getDetails'])->name('earning_types.details');
    });

    Route::group(['section' => 'setup', 'sub_section' => 'employee_earnings', 'prefix' => 'employee_earnings'], function () {
        Route::get('/', [EmployeeEarningsController::class, 'index'])->name('employee_earnings.index');
        Route::get('/create', [EmployeeEarningsController::class, 'create'])->name('employee_earnings.create');
        Route::post('/store', [EmployeeEarningsController::class, 'store'])->name('employee_earnings.store');

        Route::get('/import/{type}', [DataImportController::class, 'index'])->name('employee_earnings.import.form');
        Route::post('/import', [DataImportController::class, 'employeeEarningsImport'])->name('employee_earnings.import');
        Route::get('/download-sample', [DataImportController::class, 'downloadSampleCsv'])->name('employee_earnings.download_sample');
        Route::get('/show/{id}', [EmployeeEarningsController::class, 'show'])->name('employee_earnings.show');
        Route::get('/{id}/edit', [EmployeeEarningsController::class, 'edit'])->name('employee_earnings.edit');
        Route::put('/update/{id}', [EmployeeEarningsController::class, 'update'])->name('employee_earnings.update');
        Route::delete('/{id}/delete', [EmployeeEarningsController::class, 'destroy'])->name('employee_earnings.delete');

        // Additional actions
        Route::post('/{id}/approve', [EmployeeEarningsController::class, 'approve'])->name('employee_earnings.approve');
        Route::post('/{id}/reject', [EmployeeEarningsController::class, 'reject'])->name('employee_earnings.reject');
        Route::post('/{id}/suspend', [EmployeeEarningsController::class, 'suspend'])->name('employee_earnings.suspend');

        // AJAX routes
        Route::get('/employee/{employeeId}/earnings', [EmployeeEarningsController::class, 'getEmployeeEarnings'])->name('employee_earnings.get_employee_earnings');
        Route::get('/employee/{employeeId}/total', [EmployeeEarningsController::class, 'calculateTotalEarnings'])->name('employee_earnings.calculate_total');
    });

    Route::group(['section' => 'setup', 'sub_section' => 'employee_deductions', 'prefix' => 'employee_deductions'], function () {
        Route::get('/', [EmployeeDeductionsController::class, 'index'])->name('employee_deductions.index');
        Route::get('/create', [EmployeeDeductionsController::class, 'create'])->name('employee_deductions.create');
        Route::get('/deduction-types/{id}/details', [DeductionTypeController::class, 'getDeductionTypeDetails'])->name('deduction_types.details');

        Route::post('/store', [EmployeeDeductionsController::class, 'store'])->name('employee_deductions.store');
        Route::post('/import', [EmployeeDeductionsController::class, 'import'])->name('employee_deductions.import');
        Route::get('/download-template', ['as' => 'employee_deductions.download_template', 'uses' => 'App\Http\Controllers\Payroll\PayrollBulkUploadController@downloadDeductionsTemplate']);
        Route::get('/download-sample', ['as' => 'employee_deductions.download_sample', 'uses' => 'App\Http\Controllers\Payroll\PayrollBulkUploadController@downloadDeductionsTemplate']);
        Route::get('/show/{id}', [EmployeeDeductionsController::class, 'show'])->name('employee_deductions.show');
        Route::get('/{id}/edit', [EmployeeDeductionsController::class, 'edit'])->name('employee_deductions.edit');
        Route::put('/update/{id}', [EmployeeDeductionsController::class, 'update'])->name('employee_deductions.update');
        Route::delete('/{id}/delete', [EmployeeDeductionsController::class, 'destroy'])->name('employee_deductions.delete');

        // Additional actions
        Route::post('/{id}/approve', [EmployeeDeductionsController::class, 'approve'])->name('employee_deductions.approve');
        Route::post('/{id}/reject', [EmployeeDeductionsController::class, 'reject'])->name('employee_deductions.reject');
        Route::post('/{id}/suspend', [EmployeeDeductionsController::class, 'suspend'])->name('employee_deductions.suspend');

        // AJAX routes
        Route::get('/employee/{employeeId}/deductions', [EmployeeDeductionsController::class, 'getEmployeeDeductions'])->name('employee_deductions.get_employee_deductions');
        Route::get('/employee/{employeeId}/total', [EmployeeDeductionsController::class, 'calculateTotalDeductions'])->name('employee_deductions.calculate_total');
        Route::post('/employee-deductions/calculate-daily-rate', [EmployeeDeductionsController::class, 'calculateDailyRate'])
            ->name('employee_deductions.calculate_daily_rate');
    });

    Route::group(['section' => 'processing', 'sub_section' => 'dashboard', 'prefix' => 'dashboard'], function () {
        Route::get('/', [PayrollController::class, 'dashboard'])->name('payroll.dashboard');
        Route::get('/charts-data', [PayrollController::class, 'getChartsData'])->name('payroll.dashboard.charts-data');
    });

    Route::group(['section' => 'processing', 'sub_section' => 'payroll_records', 'prefix' => 'payroll'], function () {
        Route::get('/', [PayrollController::class, 'index'])->name('payroll.index');
        Route::get('/show/{payrollRecord}', [PayrollController::class, 'show'])->name('payroll.show');
        Route::get('/process', [PayrollController::class, 'showProcessForm'])->name('payroll.process.form');
        Route::post('/process', [PayrollController::class, 'processPayroll'])->name('payroll.process');
        Route::get('/process/{period}/{employeeID}/', [PayrollController::class, 'processSinglePayroll'])->name('payroll.process.single');
        Route::post('/approve', [PayrollController::class, 'approvePayroll'])->name('payroll.approve');
        Route::post('/mark-paid', [PayrollController::class, 'markAsPaid'])->name('payroll.mark-paid');
        Route::post('/export', [PayrollController::class, 'export'])->name('payroll.export');
        Route::get('/payslip/{payrollRecord}', [PayrollController::class, 'generatePayslip'])->name('payroll.payslip');

        // Email functionality routes
        Route::post('/email-payslip/{payrollRecord}', [PayrollController::class, 'emailPayslip'])->name('payroll.email.single');
        Route::post('/email-payslips/mass', [PayrollController::class, 'emailPayslipsMass'])->name('payroll.email.mass');
    });

    // Payroll Claims Management
    Route::group(['section' => 'processing', 'sub_section' => 'payroll_claims', 'prefix' => 'claims'], function () {
        Route::get('/', [PayrollClaimController::class, 'index'])->name('payroll.claims.index');
        Route::get('/create', [PayrollClaimController::class, 'create'])->name('payroll.claims.create');
        Route::post('/store', [PayrollClaimController::class, 'store'])->name('payroll.claims.store');
        Route::get('/show/{id}', [PayrollClaimController::class, 'show'])->name('payroll.claims.show');
        Route::get('/{id}/edit', [PayrollClaimController::class, 'edit'])->name('payroll.claims.edit');
        Route::put('/{id}', [PayrollClaimController::class, 'update'])->name('payroll.claims.update');
        Route::delete('/{id}', [PayrollClaimController::class, 'destroy'])->name('payroll.claims.destroy');

        // Claim workflow actions
        Route::post('/{id}/submit', [PayrollClaimController::class, 'submitForApproval'])->name('payroll.claims.submit');
        Route::post('/{id}/approve', [PayrollClaimController::class, 'approve'])->name('payroll.claims.approve');
        Route::post('/{id}/reject', [PayrollClaimController::class, 'reject'])->name('payroll.claims.reject');
        Route::post('/{id}/activate', [PayrollClaimController::class, 'activateRecovery'])->name('payroll.claims.activate');
        Route::post('/{id}/cancel', [PayrollClaimController::class, 'cancel'])->name('payroll.claims.cancel');

        // Recovery management
        Route::get('/recoveries', [PayrollClaimController::class, 'recoveries'])->name('payroll.claims.recoveries');
        Route::post('/recoveries/{recoveryId}/process', [PayrollClaimController::class, 'processRecovery'])->name('payroll.claims.processRecovery');
        Route::post('/recoveries/{recoveryId}/skip', function ($recoveryId) {
            return app(PayrollClaimController::class)->processRecovery(request(), $recoveryId);
        })->name('payroll.claims.skipRecovery');

        // API endpoints for payroll integration
        Route::get('/api/employee/{employee_id}/claims', [PayrollClaimController::class, 'apiGetClaimsForPayroll'])->name('payroll.claims.api.employee');
        Route::get('/api/stats', [PayrollClaimController::class, 'apiStats'])->name('payroll.claims.api.stats');
    });

    // Employee Setup Section
    Route::group(['section' => 'setup', 'sub_section' => 'employee_payroll', 'prefix' => 'employees'], function () {
        Route::get('/', [EmployeePayrollController::class, 'index'])->name('payroll.employees.index');
        Route::get('/create', [EmployeePayrollController::class, 'create'])->name('payroll.employees.create');
        Route::post('/store', [EmployeePayrollController::class, 'store'])->name('payroll.employees.store');
        Route::post('/schemes', [EmployeePayrollController::class, 'storeScheme'])->name('payroll.employees.schemes');
        Route::get('/show/{employeePayroll}', [EmployeePayrollController::class, 'show'])->name('payroll.employees.show');
        Route::get('/{employeePayroll}/edit', [EmployeePayrollController::class, 'edit'])->name('payroll.employees.edit');
        Route::put('/{employeePayroll}', [EmployeePayrollController::class, 'update'])->name('payroll.employees.update');
        Route::get('/{employeePayroll}/delete', [EmployeePayrollController::class, 'destroy'])->name('payroll.employees.delete');
        Route::get('/{employeePayroll}/toggle-status', [EmployeePayrollController::class, 'toggleStatus'])->name('payroll.employees.toggle-status');

        // Import/Export routes
        Route::get('/template/download', [EmployeePayrollController::class, 'downloadTemplate'])->name('payroll.employees.template.download');
        Route::get('/import/form', [EmployeePayrollController::class, 'showImportForm'])->name('payroll.employees.import.form');
        Route::post('/import', [EmployeePayrollController::class, 'import'])->name('payroll.employees.import');
        Route::get('/export', [EmployeePayrollController::class, 'export'])->name('payroll.employees.export');
        Route::get('/locations/{bank}', [EmployeePayrollController::class, 'getLocations'])->name('payroll.employees.locations');
        Route::get('/payroll/employees/{employee}/salary-history', [EmployeePayrollController::class, 'salaryHistory'])
            ->name('payroll.employees.salary-history');
        Route::get('/payroll/employees/all-salary-history', [EmployeePayrollController::class, 'salaryHistoryAll'])
            ->name('payroll.employees.all-salary-history');

        Route::get('/salary-history', [SalaryHistoryController::class, 'index'])->name('payroll.salary.history.index');
        Route::get('/salary-history/employee/{employeeId}', [SalaryHistoryController::class, 'showEmployee'])->name('payroll.salary.history.employee');
        Route::get('/salary-history/export', [SalaryHistoryController::class, 'export'])->name('payroll.salary.history.export');
    });

    Route::group(['section' => 'setup', 'sub_section' => 'employee_allowances', 'prefix' => 'employees/{employeePayroll}/allowances'], function () {
        Route::get('/', [AllowanceController::class, 'index'])->name('payroll.employees.allowances.index');
        Route::get('/create', [AllowanceController::class, 'create'])->name('payroll.employees.allowances.create');
        Route::post('/store', [AllowanceController::class, 'store'])->name('payroll.employees.allowances.store');
        Route::get('/{allowance}/edit', [AllowanceController::class, 'edit'])->name('payroll.employees.allowances.edit');
        Route::put('/{allowance}', [AllowanceController::class, 'update'])->name('payroll.employees.allowances.update');
        Route::delete('/{allowance}/delete', [AllowanceController::class, 'destroy'])->name('payroll.employees.allowances.delete');
    });

    Route::group(['section' => 'setup', 'sub_section' => 'employee_deductions', 'prefix' => 'employees/{employeePayroll}/deductions'], function () {
        Route::get('/', [DeductionController::class, 'index'])->name('payroll.employees.deductions.index');
        Route::get('/create', [DeductionController::class, 'create'])->name('payroll.employees.deductions.create');
        Route::post('/store', [DeductionController::class, 'store'])->name('payroll.employees.deductions.store');
        Route::get('/{deduction}/edit', [DeductionController::class, 'edit'])->name('payroll.employees.deductions.edit');
        Route::put('/{deduction}', [DeductionController::class, 'update'])->name('payroll.employees.deductions.update');
        Route::delete('/{deduction}/delete', [DeductionController::class, 'destroy'])->name('payroll.employees.deductions.delete');
    });

    // Reports Section
    Route::group(['section' => 'reports', 'sub_section' => 'statutory_reports', 'prefix' => 'reports'], function () {
        Route::get('/', [ReportsController::class, 'index'])->name('payroll.reports.index');

        // PAYE Reports
        Route::get('/paye', [ReportsController::class, 'payeIndex'])->name('payroll.reports.paye');
        Route::post('/paye/generate', [ReportsController::class, 'generatePayeReport'])->name('payroll.reports.paye.generate');
        Route::get('/paye/p9/{employee}/{year}', [ReportsController::class, 'generateP9'])->name('payroll.reports.paye.p9');
        Route::get('/paye/p10/{period}', [ReportsController::class, 'generateP10'])->name('payroll.reports.paye.p10');

        // NSSF Reports
        Route::get('/nssf', [ReportsController::class, 'nssfIndex'])->name('payroll.reports.nssf');
        Route::post('/nssf/generate', [ReportsController::class, 'generateNssfReport'])->name('payroll.reports.nssf.generate');

        // SHIF Reports
        Route::get('/shif', [ReportsController::class, 'shifIndex'])->name('payroll.reports.shif');
        Route::post('/shif/generate', [ReportsController::class, 'generateShifReport'])->name('payroll.reports.shif.generate');

        // Housing Levy Reports
        Route::get('/housing-levy', [ReportsController::class, 'housingLevyIndex'])->name('payroll.reports.housing-levy');
        Route::post('/housing-levy/generate', [ReportsController::class, 'generateHousingLevyReport'])->name('payroll.reports.housing-levy.generate');

        // Summary Reports
        Route::get('/summary', [ReportsController::class, 'summaryIndex'])->name('payroll.reports.summary');
        Route::post('/summary/generate', [ReportsController::class, 'generateSummaryReport'])->name('payroll.reports.summary.generate');

        // Bank Transfer Reports
        Route::get('/bank-transfer', [ReportsController::class, 'bankTransferIndex'])->name('payroll.reports.bank-transfer');
        Route::post('/bank-transfer/generate', [ReportsController::class, 'generateBankTransferReport'])->name('payroll.reports.bank-transfer.generate');
    });

    // Settings Section
    Route::group(['section' => 'settings', 'sub_section' => 'allowance_types', 'prefix' => 'settings/allowance-types'], function () {
        Route::get('/', [AllowanceTypeController::class, 'index'])->name('payroll.settings.allowance-types.index');
        Route::get('/create', [AllowanceTypeController::class, 'create'])->name('payroll.settings.allowance-types.create');
        Route::post('/store', [AllowanceTypeController::class, 'store'])->name('payroll.settings.allowance-types.store');
        Route::get('/{allowanceType}', [AllowanceTypeController::class, 'show'])->name('payroll.settings.allowance-types.show');
        Route::get('/{allowanceType}/edit', [AllowanceTypeController::class, 'edit'])->name('payroll.settings.allowance-types.edit');
        Route::put('/{allowanceType}', [AllowanceTypeController::class, 'update'])->name('payroll.settings.allowance-types.update');
        Route::delete('/{allowanceType}/delete', [AllowanceTypeController::class, 'destroy'])->name('payroll.settings.allowance-types.delete');
        Route::get('/{allowanceType}/toggle-status', [AllowanceTypeController::class, 'toggleStatus'])->name('payroll.settings.allowance-types.toggle-status');
        Route::post('/create-defaults', [AllowanceTypeController::class, 'createDefaults'])->name('payroll.settings.allowance-types.create-defaults');
    });

    Route::group(['section' => 'settings', 'sub_section' => 'pension_schemes', 'prefix' => 'settings/pension-schemes'], function () {
        Route::get('/', [PensionSchemeController::class, 'index'])->name('payroll.settings.pension-schemes.index');
        Route::get('/create', [PensionSchemeController::class, 'create'])->name('payroll.settings.pension-schemes.create');
        Route::post('/store', [PensionSchemeController::class, 'store'])->name('payroll.settings.pension-schemes.store');
        Route::get('/{pensionScheme}', [PensionSchemeController::class, 'show'])->name('payroll.settings.pension-schemes.show');
        Route::get('/{pensionScheme}/edit', [PensionSchemeController::class, 'edit'])->name('payroll.settings.pension-schemes.edit');
        Route::put('/{pensionScheme}', [PensionSchemeController::class, 'update'])->name('payroll.settings.pension-schemes.update');
        Route::delete('/{pensionScheme}/delete', [PensionSchemeController::class, 'destroy'])->name('payroll.settings.pension-schemes.delete');
        Route::get('/{pensionScheme}/toggle-status', [PensionSchemeController::class, 'toggleStatus'])->name('payroll.settings.pension-schemes.toggle-status');
        Route::post('/{pensionScheme}/calculate-contribution', [PensionSchemeController::class, 'calculateContribution'])->name('payroll.settings.pension-schemes.calculate-contribution');
        Route::get('/{pensionScheme}/generate-report', [PensionSchemeController::class, 'generateReport'])->name('payroll.settings.pension-schemes.generate-report');
        Route::post('/create-defaults', [PensionSchemeController::class, 'createDefaults'])->name('payroll.settings.pension-schemes.create-defaults');
        Route::get('/{pensionScheme}/download-template', [PensionSchemeController::class, 'downloadTemplate'])->name('payroll.settings.pension-schemes.download-template');
        Route::post('/{pensionScheme}/upload-assignments', [PensionSchemeController::class, 'uploadAssignments'])->name('payroll.settings.pension-schemes.upload-assignments');
    });

    Route::group(['section' => 'settings', 'sub_section' => 'payroll_periods', 'prefix' => 'settings/periods'], function () {
        Route::get('/', [PayrollPeriodController::class, 'index'])->name('payroll.settings.periods.index');
        Route::get('/create', [PayrollPeriodController::class, 'create'])->name('payroll.settings.periods.create');
        Route::post('/store', [PayrollPeriodController::class, 'store'])->name('payroll.settings.periods.store');
        Route::get('/{period}', [PayrollPeriodController::class, 'show'])->name('payroll.settings.periods.show');
        Route::get('/{period}/edit', [PayrollPeriodController::class, 'edit'])->name('payroll.settings.periods.edit');
        Route::put('/{period}', [PayrollPeriodController::class, 'update'])->name('payroll.settings.periods.update');
        Route::delete('/{period}/delete', [PayrollPeriodController::class, 'destroy'])->name('payroll.settings.periods.delete');
        Route::get('/{period}/set-current', [PayrollPeriodController::class, 'setAsCurrent'])->name('payroll.settings.periods.set-current');
        Route::get('/{period}/close', [PayrollPeriodController::class, 'close'])->name('payroll.settings.periods.close');
        Route::get('/{period}/reopen', [PayrollPeriodController::class, 'reopen'])->name('payroll.settings.periods.reopen');
        Route::get('/{period}/bank-upload-report', [PayrollPeriodController::class, 'bankUploadReport'])->name('payroll.settings.periods.bank-upload-report');
        Route::post('/generate-periods', [PayrollPeriodController::class, 'generatePeriods'])->name('payroll.settings.periods.generate-periods');
    });

    // Reports Section
    Route::group(['section' => 'reports', 'sub_section' => 'statutory_reports', 'prefix' => 'reports'], function () {
        Route::get('/', [ReportsController::class, 'index'])->name('reports.index');

        // PAYE Reports
        Route::get('/paye', [ReportsController::class, 'payeIndex'])->name('reports.paye');
        Route::post('/paye/generate', [ReportsController::class, 'generatePayeReport'])->name('reports.paye.generate');
        Route::get('/paye/p9/{employee}/{year}', [ReportsController::class, 'generateP9'])->name('reports.paye.p9');
        Route::get('/paye/p10/{period}', [ReportsController::class, 'generateP10'])->name('reports.paye.p10');

        // NSSF Reports
        Route::get('/nssf', [ReportsController::class, 'nssfIndex'])->name('reports.nssf');
        Route::post('/nssf/generate', [ReportsController::class, 'generateNssfReport'])->name('reports.nssf.generate');

        // SHIF Reports
        Route::get('/shif', [ReportsController::class, 'shifIndex'])->name('reports.shif');
        Route::post('/shif/generate', [ReportsController::class, 'generateShifReport'])->name('reports.shif.generate');

        // Housing Levy Reports
        Route::get('/housing-levy', [ReportsController::class, 'housingLevyIndex'])->name('reports.housing-levy');
        Route::post('/housing-levy/generate', [ReportsController::class, 'generateHousingLevyReport'])->name('reports.housing-levy.generate');

        // Summary Reports
        Route::get('/summary', [ReportsController::class, 'summaryIndex'])->name('reports.summary');
        Route::post('/summary/generate', [ReportsController::class, 'generateSummaryReport'])->name('reports.summary.generate');

        // Bank Transfer Reports
        Route::get('/bank-transfer', [ReportsController::class, 'bankTransferIndex'])->name('reports.bank-transfer');
        Route::post('/bank-transfer/generate', [ReportsController::class, 'generateBankTransferReport'])->name('reports.bank-transfer.generate');
    });
    Route::group(['section' => 'reports', 'sub_section' => 'general_reports', 'prefix' => 'payroll-reports'], function () {
        Route::get('/', [PayrollReportsController::class, 'index'])->name('payrollReportsIndex');
        Route::get('/charts-data', [PayrollReportsController::class, 'getChartsData'])->name('payrollReportsChartsData');

        Route::post('/paysumm-raw/export/{id}', [PayrollReportsController::class, 'exportPayrollSummaryReport'])->name('reports.rawpaysumm');

        Route::get('/inputs-report', [PayrollReportsController::class, 'payrollInputsReport'])->name('payroll.reports.inputs');
        Route::post('/inputs-report/export', [PayrollReportsController::class, 'exportPayrollInputsReport'])->name('payroll.reports.inputs.export');
        Route::post('/inputs-report/upload', [PayrollReportsController::class, 'uploadApprovedInputsTemplate'])->name('payroll.reports.inputs.upload');
    });

    // Settings Section
    Route::group(['section' => 'settings', 'sub_section' => 'allowance_types', 'prefix' => 'settings/allowance-types'], function () {
        Route::get('/', [AllowanceTypeController::class, 'index'])->name('payroll.settings.allowance-types.index');
        Route::get('/create', [AllowanceTypeController::class, 'create'])->name('payroll.settings.allowance-types.create');
        Route::post('/store', [AllowanceTypeController::class, 'store'])->name('payroll.settings.allowance-types.store');
        Route::get('/{allowanceType}', [AllowanceTypeController::class, 'show'])->name('payroll.settings.allowance-types.show');
        Route::get('/{allowanceType}/edit', [AllowanceTypeController::class, 'edit'])->name('payroll.settings.allowance-types.edit');
        Route::put('/{allowanceType}', [AllowanceTypeController::class, 'update'])->name('payroll.settings.allowance-types.update');
        Route::delete('/{allowanceType}/delete', [AllowanceTypeController::class, 'destroy'])->name('payroll.settings.allowance-types.delete');
        Route::get('/{allowanceType}/toggle-status', [AllowanceTypeController::class, 'toggleStatus'])->name('payroll.settings.allowance-types.toggle-status');
        Route::post('/create-defaults', [AllowanceTypeController::class, 'createDefaults'])->name('payroll.settings.allowance-types.create-defaults');
    });

    Route::group(['section' => 'settings', 'sub_section' => 'deduction_types', 'prefix' => 'settings/deduction-types'], function () {
        Route::get('/', [DeductionTypeController::class, 'index'])->name('payroll.settings.deduction-types.index');
        Route::get('/create', [DeductionTypeController::class, 'create'])->name('payroll.settings.deduction-types.create');
        Route::post('/store', [DeductionTypeController::class, 'store'])->name('payroll.settings.deduction-types.store');
        Route::get('/{deductionType}', [DeductionTypeController::class, 'show'])->name('payroll.settings.deduction-types.show');
        Route::get('/{deductionType}/edit', [DeductionTypeController::class, 'edit'])->name('payroll.settings.deduction-types.edit');
        Route::put('/{deductionType}', [DeductionTypeController::class, 'update'])->name('payroll.settings.deduction-types.update');
        Route::delete('/{deductionType}/delete', [DeductionTypeController::class, 'destroy'])->name('payroll.settings.deduction-types.delete');
        Route::get('/{deductionType}/toggle-status', [DeductionTypeController::class, 'toggleStatus'])->name('payroll.settings.deduction-types.toggle-status');
        Route::post('/create-defaults', [DeductionTypeController::class, 'createDefaults'])->name('payroll.settings.deduction-types.create-defaults');
    });

    Route::group(['section' => 'setup', 'sub_section' => 'employee_allowances', 'prefix' => 'employees/{employeePayroll}/allowances'], function () {
        Route::get('/', [AllowanceController::class, 'index'])->name('payroll.employees.allowances.index');
        Route::get('/create', [AllowanceController::class, 'create'])->name('payroll.employees.allowances.create');
        Route::post('/store', [AllowanceController::class, 'store'])->name('payroll.employees.allowances.store');
        Route::get('/{allowance}/edit', [AllowanceController::class, 'edit'])->name('payroll.employees.allowances.edit');
        Route::put('/{allowance}', [AllowanceController::class, 'update'])->name('payroll.employees.allowances.update');
        Route::delete('/{allowance}/delete', [AllowanceController::class, 'destroy'])->name('payroll.employees.allowances.delete');
    });

    Route::group(['section' => 'setup', 'sub_section' => 'bulk_upload', 'prefix' => 'bulk-upload'], function () {
        Route::get('earnings', ['as' => 'payroll.bulk_upload.earnings.index', 'uses' => 'App\Http\Controllers\Payroll\PayrollBulkUploadController@earningsIndex']);
        Route::get('earnings/download-template', ['as' => 'payroll.bulk_upload.earnings.download_template', 'uses' => 'App\Http\Controllers\Payroll\PayrollBulkUploadController@downloadEarningsTemplate']);
        Route::post('earnings', ['as' => 'payroll.bulk_upload.earnings', 'uses' => 'App\Http\Controllers\DataImportController@employeeEarningsImport']);

        Route::get('deductions', ['as' => 'payroll.bulk_upload.deductions.index', 'uses' => 'App\Http\Controllers\Payroll\PayrollBulkUploadController@deductionsIndex']);
        Route::get('deductions/download-template', ['as' => 'payroll.bulk_upload.deductions.download_template', 'uses' => 'App\Http\Controllers\Payroll\PayrollBulkUploadController@downloadDeductionsTemplate']);

        Route::get('advances', ['as' => 'payroll.bulk_upload.advances.index', 'uses' => 'App\Http\Controllers\DataImportController@advancesIndex']);
        Route::get('advances/download-template', ['as' => 'payroll.bulk_upload.advances.download_template', 'uses' => 'App\Http\Controllers\DataImportController@downloadAdvancesTemplate']);
        Route::post('advances', ['as' => 'payroll.bulk_upload.advances', 'uses' => 'App\Http\Controllers\DataImportController@advancesImport']);
    });
    Route::group(['section' => 'banks', 'sub_section' => 'banks', 'prefix' => 'banks'], function () {
        Route::get('/', [BankController::class, 'index'])->name('banks.index');
        Route::get('/create', [BankController::class, 'create'])->name('banks.create');
        Route::post('/store', [BankController::class, 'store'])->name('banks.store');
        Route::get('/show/{bank}', [BankController::class, 'show'])->name('banks.show');
        Route::get('/{bank}/edit', [BankController::class, 'edit'])->name('banks.edit');
        Route::put('/update/{bank}', [BankController::class, 'update'])->name('banks.update');
        Route::delete('/destroy/{bank}', [BankController::class, 'destroy'])->name('banks.destroy');
        Route::get('/import', [BankController::class, 'import'])->name('banks.import');
        Route::post('/import/process', [BankController::class, 'processImport'])->name('banks.import.process');
        Route::get('/template/download', [BankController::class, 'downloadTemplate'])->name('banks.template.download');
    });

    Route::group(['section' => 'banks', 'sub_section' => 'branches', 'prefix' => 'banks/branches'], function () {
        Route::get('/', [BranchesController::class, 'index'])->name('bank-branches.index');
        Route::get('/create', [BranchesController::class, 'create'])->name('bank-branches.create');
        Route::post('/store', [BranchesController::class, 'store'])->name('bank-branches.store');
        Route::get('/show/{branch}', [BranchesController::class, 'show'])->name('bank-branches.show');
        Route::get('/{branch}/edit', [BranchesController::class, 'edit'])->name('bank-branches.edit');
        Route::put('/update/{branch}', [BranchesController::class, 'update'])->name('bank-branches.update');
        Route::delete('/destroy/{branch}', [BranchesController::class, 'destroy'])->name('bank-branches.destroy');
        Route::get('/import', [BranchesController::class, 'import'])->name('bank-branches.import');
        Route::post('/import/process', [BranchesController::class, 'processImport'])->name('bank-branches.import.process');
        Route::get('/template/download', [BranchesController::class, 'downloadTemplate'])->name('bank-branches.template.download');
    });
    Route::get('/payroll/progress_check', [PayrollController::class, 'downloadTemplate'])->name('payroll.progress.check1');
    Route::get('/payroll/progress/check', [PayrollController::class, 'checkProgress'])->name('payroll.progress.check');
    Route::get('/payroll/progress', [PayrollController::class, 'getPayrollProgress'])->name('payroll.progress');

    Route::post('/payroll/bulk/submit', [PayrollController::class, 'bulkSubmitWithProgress'])->name('payroll.bulk.submit');

    // Loan Management Routes
    Route::group(['section' => 'loans', 'sub_section' => 'loan_management', 'prefix' => 'loans'], function () {
        Route::get('dashboard', [LoanController::class, 'dashboard'])->name('loans.dashboard');
        Route::get('/', [LoanController::class, 'index'])->name('loans.index');
        Route::get('/create', [LoanController::class, 'create'])->name('loans.create');
        Route::post('/store', [LoanController::class, 'store'])->name('loans.store');
        Route::get('/show/{id}', [LoanController::class, 'show'])->name('loans.show');
        Route::get('/{id}/edit', [LoanController::class, 'edit'])->name('loans.edit');
        Route::put('/{id}', [LoanController::class, 'update'])->name('loans.update');
        Route::delete('/{id}/delete', [LoanController::class, 'destroy'])->name('loans.delete');
        Route::post('/{id}/approve', [LoanController::class, 'approve'])->name('loans.approve');
        Route::post('/{id}/reject', [LoanController::class, 'reject'])->name('loans.reject');
        Route::post('/{id}/suspend', [LoanController::class, 'suspend'])->name('loans.suspend');

        // Loan Types
        Route::group(['prefix' => 'types'], function () {
            Route::get('/', [LoanTypeController::class, 'index'])->name('loans.types.index');
            Route::get('/create', [LoanTypeController::class, 'create'])->name('loans.types.create');
            Route::post('/store', [LoanTypeController::class, 'store'])->name('loans.types.store');
            Route::get('/{id}/edit', [LoanTypeController::class, 'edit'])->name('loans.types.edit');
            Route::put('/{id}', [LoanTypeController::class, 'update'])->name('loans.types.update');
            Route::delete('/{id}/delete', [LoanTypeController::class, 'destroy'])->name('loans.types.delete');
        });

        // Loan Applications
        Route::group(['prefix' => 'applications'], function () {
            Route::get('/', [LoanApplicationController::class, 'index'])->name('loans.applications.index');
            Route::get('/pending', [LoanApplicationController::class, 'pending'])->name('loans.applications.pending');
            Route::post('/{id}/approve', [LoanApplicationController::class, 'approve'])->name('loans.applications.approve');
            Route::post('/{id}/reject', [LoanApplicationController::class, 'reject'])->name('loans.applications.reject');
        });

        // Manual Deductions
        Route::group(['prefix' => 'manual-deductions'], function () {
            Route::get('/', [ManualLoanDeductionController::class, 'index'])->name('loans.manual-deductions.index');
            Route::post('/store', [ManualLoanDeductionController::class, 'store'])->name('loans.manual-deductions.store');
            Route::delete('/{id}/delete', [ManualLoanDeductionController::class, 'destroy'])->name('loans.manual-deductions.delete');
        });

        // Reports
        Route::group(['prefix' => 'reports'], function () {
            Route::get('/summary', [LoanReportController::class, 'summary'])->name('loans.reports.summary');
        });
    });
});