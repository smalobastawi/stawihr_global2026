<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNHIFsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('nhif_rates', function (Blueprint $table) {
            $table->increments('id');
            $table->float('range_start')->nullable;
            $table->float('range_end') ->nullable;
            $table ->float('amount_deductable')->nullable;
            $table->integer('percentage')->nullable;
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('nhif_rates');
    }
}
