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
        Schema::create('approval_setting_approvers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('approval_setting_id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('module_id');
            $table->timestamps();
        });

        Schema::table('approval_settings', function (Blueprint $table) {
            $table->renameColumn('model_type', 'module_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('approval_setting_approvers');
        Schema::table('approval_settings', function (Blueprint $table) {
            $table->renameColumn('module_id', 'model_type');
        });
    }
};
