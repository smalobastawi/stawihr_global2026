<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('payroll_periods', function (Blueprint $table) {
            // Add new columns
            $table->unsignedTinyInteger('month_number')->nullable()->after('end_date');
            $table->unsignedTinyInteger('week_number')->nullable()->after('month_number');
            $table->unsignedTinyInteger('biweekly_number')->nullable()->after('week_number');
            
            // Add unique constraint
            $table->unique(['start_date', 'end_date'], 'payroll_periods_dates_unique');
        });

        // Update existing records with calculated values
        DB::statement("
            UPDATE payroll_periods 
            SET 
                month_number = MONTH(start_date),
                week_number = WEEK(start_date, 3),
                biweekly_number = FLOOR((DAYOFYEAR(start_date) - 1) / 14) + 1
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('payroll_periods', function (Blueprint $table) {
            // Remove unique constraint
            $table->dropUnique('payroll_periods_dates_unique');
            
            // Remove columns
            $table->dropColumn(['month_number', 'week_number', 'biweekly_number']);
        });
    }
};