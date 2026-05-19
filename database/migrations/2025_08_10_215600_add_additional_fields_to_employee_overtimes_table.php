<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('employee_overtimes', function (Blueprint $table) {
            // Weekend hours and days
            $table->decimal('weekend_hours_totals', 8, 2)->default(0);
            $table->integer('weekend_days_totals')->default(0)->after('weekend_hours_totals');
            
            // Public holiday hours and days
            $table->decimal('public_holiday_hours_totals', 8, 2)->default(0);
            $table->integer('public_holiday_days_totals')->default(0);
            
            // Weekday hours and days
            $table->decimal('weekday_hours_total', 8, 2)->default(0);
            $table->integer('weekday_days_total')->default(0);
            
            // Payroll period and month
            $table->unsignedBigInteger('payroll_period_id');
            $table->string('payroll_month', 7)->nullable()->comment('Format: YYYY-MM');

            $table->foreign('payroll_period_id')->references('id')->on('payroll_periods')->onDelete('cascade');
            //make nullable
            $table->decimal('overtime_rate')->nullable()->change();
            $table->decimal('total_amount')->nullable()->change();
            $table->decimal('hours_worked')->nullable()->change();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employee_overtimes', function (Blueprint $table) {
            $table->dropColumn([
                'weekend_hours_totals',
                'weekend_days_totals',
                'public_holiday_hours_totals',
                'public_holiday_days_totals',
                'weekday_hours_total',
                'weekday_days_total',
                'payroll_period_id',
                'payroll_month'
            ]);
        });
    }
};