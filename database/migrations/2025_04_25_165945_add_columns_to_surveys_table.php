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
        Schema::table('surveys', function (Blueprint $table) {
            //
            $table->string('google_form_id')->unique()->nullable()->after('slug');
            $table->string('form_url')->after('google_form_id')->nullable();
            $table->string('edit_url')->after('form_url')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('surveys', function (Blueprint $table) {
            //
            $table->dropColumn('google_form_id');
            $table->dropColumn('form_url');
            $table->dropColumn('edit_url');
        });
    }
};
