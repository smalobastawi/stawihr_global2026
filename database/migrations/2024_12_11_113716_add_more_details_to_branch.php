<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up()
    {
        Schema::table('location', function (Blueprint $table) {
            $table->string('address')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->unsignedBigInteger('manager_id')->nullable();
            $table->foreign('manager_id')->references('employee_id')->on('employee');
        });
    }

    public function down()
    {
        Schema::table('location', function (Blueprint $table) {
            $table->dropForeign(['manager_id']);
            $table->dropColumn(['address', 'phone', 'email', 'manager_id']);
        });
    }
};