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
        Schema::table('morpho_device_logs', function (Blueprint $table) {
            $table->integer('id_no')->nullable()->change();
            $table->string('payroll_number')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('morpho_device_logs', function (Blueprint $table) {
            //
        });
    }
};
