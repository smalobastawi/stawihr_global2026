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
        Schema::table('salary_details', function (Blueprint $table) {
            $table->unsignedBigInteger('financial_year_id')->nullable();
            $table->foreign('financial_year_id')->references('id')->on('financial_years');
        });
        Schema::table('leave_application', function (Blueprint $table) {
            $table->unsignedBigInteger('financial_year_id')->nullable();
            $table->foreign('financial_year_id')->references('id')->on('financial_years');
        });

        Schema::table('financial_years', function (Blueprint $table) {
            $table->unique(['start_date', 'end_date', 'status']);
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
            $table->dropForeign(['financial_year_id']);
            $table->dropColumn('financial_year_id');
        });
        Schema::table('leave_application', function (Blueprint $table) {
            $table->dropForeign(['financial_year_id']);
            $table->dropColumn('financial_year_id');
        });

        Schema::table('financial_years', function (Blueprint $table) {
            $table->dropUnique(['start_date', 'end_date', 'status']);
        });
    }
};
