<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('employee_payrolls', function (Blueprint $table) {
            // Overtime rate multipliers
            $table->decimal('overtime_rate_normal', 3, 2)->default(1.50)
                  ->comment('Overtime rate multiplier for normal working days (e.g., 1.5 = 150%)');
            $table->decimal('overtime_rate_weekend', 3, 2)->default(2.00)
                  ->comment('Overtime rate multiplier for weekends (e.g., 2.0 = 200%)');
            $table->decimal('overtime_rate_holiday', 3, 2)->default(2.00)
                  ->comment('Overtime rate multiplier for public holidays (e.g., 2.0 = 200%)');
            
            // Index for performance
            $table->index(['overtime_rate_normal', 'overtime_rate_weekend', 'overtime_rate_holiday'], 'idx_overtime_rates');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('employee_payrolls', function (Blueprint $table) {
            // Drop index first
            $table->dropIndex('idx_overtime_rates');
            
            // Drop columns
            $table->dropColumn([
                'overtime_rate_holiday',
                'overtime_rate_weekend', 
                'overtime_rate_normal'
            ]);
        });
    }
};