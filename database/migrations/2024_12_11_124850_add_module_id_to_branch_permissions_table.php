<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up()
    {
        Schema::table('location_permissions', function (Blueprint $table) {
            $table->unsignedBigInteger('module_id')->nullable();
        });
    }

    public function down()
    {
        Schema::table('branch_permissions', function (Blueprint $table) {
            // $table->dropForeign(['module_id']);
            $table->dropColumn(['module_id']);
        });
    }
};