<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddMoreFieldsToSalaryDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('salary_details', function (Blueprint $table) {
            $table ->string('payroll_no')->nullable();
            $table ->string('gross_pay')->nullable();
            $table ->string('nssf_no')->nullable();
            $table ->string('nhif_no')->nullable();
            $table ->string('PAYE_tax')->nullable();
            $table ->string('public_holidays_pay')->nullable();
            $table ->string('employee_id_no')->nullable();
            $table ->string('kra_pin')->nullable();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('salary_details', function (Blueprint $table) {
            //
        });
    }
}
