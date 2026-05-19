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
        Schema::table('payroll_record_details', function (Blueprint $table) {
            $table->unsignedBigInteger('payroll_period_id')->nullable()->after('payroll_record_id');
            $table->foreign('payroll_period_id')->references('id')->on('payroll_periods')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('payroll_record_details', function (Blueprint $table) {
            $table->dropForeign(['payroll_period_id']);
            $table->dropColumn('payroll_period_id');
        });
    }
};
