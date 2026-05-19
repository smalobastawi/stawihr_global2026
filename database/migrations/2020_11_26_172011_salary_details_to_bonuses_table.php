<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SalaryDetailsToBonusesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('salary_details_to_bonuses', function (Blueprint $table) {
            $table->increments('salary_details_to_bonuses_id');
            $table->integer('salary_details_id');
            $table->integer('salary_bonus_id');
            $table->integer('amount_of_bonus');
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
        Schema::dropIfExists('salary_details_to_bonuses');
    }
}
