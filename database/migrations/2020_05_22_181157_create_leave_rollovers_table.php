<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLeaveRolloversTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('leave_rollovers', function (Blueprint $table) {
            $table->increments('id');
            $table->foreignId('employee_id')->constrained('employee', 'employee_id')->onUpdate('cascade')->restrictOnDelete();

            $table->string('default_rollover')->nullable();
            $table->string('days_requested')->nullable();
            $table->string('supervisor_approval')->default('1');
            $table->string('hr_approval')->default('1');
            $table->string('ceo_approval')->default('1');
            $table->string('final_status')->default('1');
            $table->date('date_approved')->nullable;
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
        Schema::dropIfExists('leave_rollovers');
    }
}
