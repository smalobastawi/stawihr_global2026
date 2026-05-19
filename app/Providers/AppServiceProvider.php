<?php
/*
 * Copyright (c) 2023/9/6 sw@stawitech
 */

namespace App\Providers;

use App\Models\FinancialYear;
use App\Models\Payroll\PayrollRecord;
use App\Observers\PayrollRecordObserver;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Redirect;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */


    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);

        View::composer('*', function ($view) {
            $view->with('activeFinancialYear', FinancialYear::active()->first());
        });
        // PayrollRecord::observe(PayrollRecordObserver::class);
        $models = [
            \App\Models\Payroll\PayrollRecord::class,
            \App\Models\EmployeeDeductions::class,
            // Add other approvable models here
        ];

        foreach ($models as $model) {
            $model::observe(\App\Observers\ApprovableModelObserver::class);
        }
    }
    public function register()
    {
        //
    }
}
