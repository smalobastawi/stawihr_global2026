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
        Schema::table('allowance', function (Blueprint $table) {
            $table->double('percentage_of_basic', 15, 4)->change();
            $table->double('limit_per_month', 15, 4)->change();
        });

        Schema::table('deduction', function (Blueprint $table) {
            $table->double('percentage_of_basic', 15, 4)->change();
            $table->double('limit_per_month', 15, 4)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('allowance', function (Blueprint $table) {
            //
        });
    }
};
