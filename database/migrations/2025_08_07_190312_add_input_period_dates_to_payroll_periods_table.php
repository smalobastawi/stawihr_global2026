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
      Schema::table('payroll_periods', function (Blueprint $table) {
            $table->date('input_period_start')->after('end_date');
            $table->date('input_period_end')->after('input_period_start');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('payroll_periods', function (Blueprint $table) {
            Schema::table('payroll_periods', function (Blueprint $table) {
            $table->dropColumn(['input_period_start', 'input_period_end']);
        });
        });
    }
};
