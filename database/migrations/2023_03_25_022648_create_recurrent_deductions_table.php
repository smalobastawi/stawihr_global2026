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
        Schema::create('recurrent_deductions', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('description');
            $table->string('start_month');
            $table->string('end_month');
            $table->string('frequency');
            $table->float('amount');
            $table->integer('created_by');
            $table->integer('updated_by');
            $table->integer('approved_by');
            $table->integer('approval_status')->comment('1-approved, 0-not-approved');
            $table->integer('status')->comment('1-active, 0-inactive');

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
        Schema::dropIfExists('recurrent_deductions');
    }
};
