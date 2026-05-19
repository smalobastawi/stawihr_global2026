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
        Schema::table('employee_education_qualification', function (Blueprint $table) {
            //
            $table->string('passing_year')->after('degree')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('employee_education_qualification', function (Blueprint $table) {
            //
            $table->dropColumn('passing_year');
        });
    }
};
