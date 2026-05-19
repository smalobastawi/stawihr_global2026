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
            //
            $table->string('application_source')->nullable();
            $table->text('cover_letter')->nullable()->change();
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
            //
            $table->dropColumn('application_source');
            $table->text('cover_letter')->nullable(false)->change();
        });
    }
};
