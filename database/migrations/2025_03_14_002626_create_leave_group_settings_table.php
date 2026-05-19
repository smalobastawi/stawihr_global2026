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
    Schema::create('leave_group_settings', function (Blueprint $table) {
        $table->id();
         
        $table->unsignedBigInteger('leave_type_id')->foreign('leave_type_id')->references('leave_type_id')->on('leave_type')->onDelete('no action');
        $table->unsignedBigInteger('leave_group_id')->foreign('id')->references('id')->on('leave_groups')->onDelete('no action');
        $table->unsignedInteger('annual_entitlement')->default(0);
        $table->unsignedInteger('carryover_days')->default(0);
        $table->unsignedInteger('max_carryover_days')->nullable();
        $table->float('earning_rate')->default(0);
        $table->enum('gender', ['male', 'female', 'all'])->default('all');
        $table->unsignedInteger('probation_period_days')->default(0);
        $table->unsignedInteger('notice_period_days')->default(0);
        $table->boolean('allow_half_day')->default(false);
        $table->boolean('paid')->default(false);
        $table->string('accrual_frequency')->default('once');
        $table->enum('applicable_on', ['calendar_days', 'working_days'])->default('calendar_days');
        $table->unsignedInteger('max_consecutive_days')->nullable();
        $table->timestamps();
    
    $table->unique(['leave_group_id', 'leave_type_id']);
});

 
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('leave_group_settings');
    }
};
