<?php

use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAbsenteesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::create('absentees', function (Blueprint $table) {
            $date = Carbon::now()->format('Y-m-d');
            $table->increments('id');
            $table->date('date')->default($date);
            $table->unsignedInteger('employee_id');
            $table->string('absence_description');
            $table->timestamps();
            $table->unique(["date","employee_id"]);
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('absentees');
    }
}
