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
        Schema::table('job_applicant', function (Blueprint $table) {
            // Add the new columns to the applications table
            $table->integer('years_of_experience')->default(0);
            $table->string('highest_qualification')->default('None');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('job_applicant', function (Blueprint $table) {
            // Drop the columns if we need to rollback the migration
            $table->dropColumn(['years_of_experience', 'highest_qualification']);
        });
    }
};
