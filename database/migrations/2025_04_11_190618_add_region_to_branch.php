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
        Schema::table('location', function (Blueprint $table) {

            $table->unsignedBigInteger('region_id')->nullable()->after('location_id'); // Replace 'some_existing_column' with the actual column name after which you want to add region_id

            $table->foreign('region_id')->references('id')->on('regions')->onDelete('cascade');
        });

        Schema::table('employee', function (Blueprint $table) {
            $table->unsignedBigInteger('region_id')->nullable()->after('location_id'); // Replace 'some_existing_column' with the actual column name after which you want to add region_id

            $table->foreign('region_id')->references('id')->on('regions')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('location', function (Blueprint $table) {

            $table->dropForeign(['region_id']);


            $table->dropColumn('region_id');
        });
        Schema::table('region', function (Blueprint $table) {

            $table->dropForeign(['region_id']);


            $table->dropColumn('region_id');
        });
    }
};