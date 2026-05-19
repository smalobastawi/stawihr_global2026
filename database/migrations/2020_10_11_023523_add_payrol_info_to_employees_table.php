<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPayrolInfoToEmployeesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('employee', function (Blueprint $table) {
            $table->unsignedInteger('job_category')->nullable();
            $table->unsignedInteger('pay_group')->nullable()->comment('job pay-group Daily or Monthly');
            $table->string('KRA_Pin')->nullable();
            $table->string('NSSF_no')->nullable();
            $table->string('NHIF_no')->nullable();
            $table->string('payroll_number')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('employee', function (Blueprint $table) {
            //
        });
    }
}
