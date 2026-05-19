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
        Schema::table('employee_deductions', function (Blueprint $table) {
      if (!Schema::hasColumn('employee_deductions', 'calculation_type')) {
                       $table->enum('calculation_type', ['fixed_amount', 'percentage_of_basic', 'percentage_of_gross', 'hourly_rate', 'daily_rate'])->default('fixed_amount');
        }

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('employee_deductions', function (Blueprint $table) {
             if (Schema::hasColumn('employee_deductions', 'calculation_type')) {
            $table->dropColumn('calculation_type');  // or $table->dropColumn('deleted_at');
        }
        });
    }
};
