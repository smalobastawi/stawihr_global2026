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
        Schema::table('termination', function (Blueprint $table) {
              //change the id on termination to big int
        Schema::table('termination', function (Blueprint $table) {
            $table->bigIncrements('termination_id')->change();
        });

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('termination', function (Blueprint $table) {
            //
        });
    }
};
