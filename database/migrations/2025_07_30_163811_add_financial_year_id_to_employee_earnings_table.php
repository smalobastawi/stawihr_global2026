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
        Schema::table('employee_earnings', function (Blueprint $table) {
            $table->unsignedBigInteger('financial_year_id')->nullable()->after('payroll_month');
            $table->foreign('financial_year_id')->references('id')->on('financial_years')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('employee_earnings', function (Blueprint $table) {
            $table->dropForeign(['financial_year_id']);
            $table->dropColumn('financial_year_id');
        });
    }
};
