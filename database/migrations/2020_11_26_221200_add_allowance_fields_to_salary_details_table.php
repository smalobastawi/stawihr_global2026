<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAllowanceFieldsToSalaryDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('salary_details', function (Blueprint $table) {
            $table->integer('house_allowance')->nullable();
            $table->integer('transport_allowance')->nullable();
            $table->integer('banking_allowance')->nullable();
            $table->integer('deductible_advance')->nullable();
            $table->integer('payroll_claim')->nullable();
            $table->integer('pro_rata')->nullable();

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
