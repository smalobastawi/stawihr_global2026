<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('surveys', function (Blueprint $table) {
            $table->unsignedInteger('department_id')->nullable()->after('end_date');
            $table->unsignedBigInteger('location_id')->nullable()->after('department_id');
            $table->unsignedBigInteger('region_id')->nullable()->after('location_id');
            $table->unsignedBigInteger('gender_id')->nullable()->after('region_id');

            // Foreign key constraints
            $table->foreign('department_id')->references('department_id')->on('department')->onDelete('cascade');
            $table->foreign('location_id')->references('location_id')->on('location')->onDelete('cascade');
            $table->foreign('region_id')->references('id')->on('regions')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('surveys', function (Blueprint $table) {
            $table->dropForeign(['department_id']);
            $table->dropForeign(['location_id']);
            $table->dropForeign(['region_id']);
            $table->dropForeign(['gender_id']);

            $table->dropColumn(['department_id', 'location_id', 'region_id', 'gender_id']);
        });
    }
};