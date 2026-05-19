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
        Schema::table('user', function (Blueprint $table) {
            //
            $table->string('google_id')->after('password_changed_at')->nullable()->unique();
            $table->text('token')->after('google_id')->nullable();
            $table->text('refresh_token')->after('token')->nullable();
            $table->integer('expires_in')->after('refresh_token')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user', function (Blueprint $table) {
            //
            $table->dropColumn('google_id');
            $table->dropColumn('token');
            $table->dropColumn('refresh_token');
            $table->dropColumn('expires_in');
        });
    }
};
