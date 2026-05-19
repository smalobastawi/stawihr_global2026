<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateParyroll9sTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('paryroll9s', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('employee_id');
            $table->string('payroll_number');
            $table->string('NHIF_number');
            $table->string('NSSF_number');
            $table->string('KRA_pin');
            $table->string('national_id')->nullable();
            $table->timestamps();
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
        Schema::dropIfExists('paryroll9s');
    }
}
