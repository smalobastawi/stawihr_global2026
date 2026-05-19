<?php
/*
 * Copyright (c) 2023/9/6 sw@stawitech
 */

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to the "home" route for your application.
     *
     * This is used by Laravel authentication to redirect users after login.
     *
     * @var string
     */
    //public const HOME = '/home';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        $this->configureRateLimiting();

        $this->routes(function () {
            Route::prefix('api')->middleware('api')->group(base_path('routes/api.php'));

            Route::middleware('web')->group(base_path('routes/web.php'));

        });
        Route::middleware('web')->group(base_path('routes/attendance.php'));
        Route::middleware('web')->group(base_path('routes/awardNoticeAndTraining.php'));
        Route::middleware('web')->group(base_path('routes/employee.php'));
        Route::middleware('web')->group(base_path('routes/leave.php'));
            Route::middleware('web')->group(base_path('routes/recruitment.php'));
        Route::middleware('web')->group(base_path('routes/setting.php'));
        Route::middleware('web')->group(base_path('routes/payroll.php'));
        Route::middleware('web')->group(base_path('routes/permissions.php'));
        Route::middleware('web')->group(base_path('routes/payroll_calculator.php'));
        Route::middleware('web')->group(base_path('routes/payroll_overtime.php'));
        Route::middleware('web')->group(base_path('routes/reports.php'));
        Route::middleware('web')->group(base_path('routes/approvals.php'));
        Route::middleware('web')->group(base_path('routes/feedback.php'));
        Route::middleware('web')->group(base_path('routes/disciplinary.php'));
        Route::middleware('web')->group(base_path('routes/ess.php'));
        Route::middleware('web')->group(base_path('routes/documents.php'));
        Route::middleware('web')->group(base_path('routes/offboarding.php'));
        Route::middleware('web')->group(base_path('routes/survey.php'));
        Route::middleware('web')->group(base_path('routes/project.php'));
        Route::middleware('web')->group(base_path('routes/performance.php'));
        Route::middleware('web')->group(base_path('routes/pip.php'));
        Route::middleware('web')->group(base_path('routes/vehicle.php'));

    }

    /**
     * Configure the rate limiters for the application.
     *
     * @return void
     */
    protected function configureRateLimiting()
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });
    }

    public function map()
    {
        $this->mapApiRoutes();

        $this->mapWebRoutes();
        Route::middleware('web')->namespace($this->namespace)->group(base_path('routes/employee.php'));
        Route::middleware('web')->namespace($this->namespace)->group(base_path('routes/leave.php'));
        Route::middleware('web')->namespace($this->namespace)->group(base_path('routes/attendance.php'));
        Route::middleware('web')->namespace($this->namespace)->group(base_path('routes/payroll.php'));
            Route::middleware('web')->namespace($this->namespace)->group(base_path('routes/setting.php'));
        Route::middleware('web')->namespace($this->namespace)->group(base_path('routes/awardNoticeAndTraining.php'));
        Route::middleware('web')->namespace($this->namespace)->group(base_path('routes/recruitment.php'));
        Route::middleware('web')->namespace($this->namespace)->group(base_path('routes/permissions.php'));
        Route::middleware('web')->namespace($this->namespace)->group(base_path('routes/payroll_calculator.php'));
        Route::middleware('web')->namespace($this->namespace)->group(base_path('routes/vehicle.php'));


        //
    }
}
