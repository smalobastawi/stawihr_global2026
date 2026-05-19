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
        Schema::create('payroll_adjustments', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('salary_details_id');
            $table->integer('employee_id');
            $table->integer('adjustment_type');
            $table->integer('payroll_number');
            $table->integer('department_id');
            $table->string('month_being_adjusted',20);
            $table->string('month_to_be_applied_adjustment',20);
            $table->integer('adjusted_basic_salary')->default('0');
            $table->integer('adjusted_total_allowance')->default('0');
            $table->integer('adjusted_total_deduction')->default('0');
            $table->integer('adjusted_total_late')->default('0');
            $table->integer('adjusted_late_amount')->default('0');
            $table->integer('adjusted_total_absence')->default('0');
            $table->integer('adjusted__absence_amount')->default('0');
            $table->integer('adjusted_overtime_rate')->default('0');
            $table->integer('adjusted_per_day_salary')->default('0');
            $table->string('adjusted__over_time_hour',50)->default('00:00');
            $table->integer('adjusted_total_overtime_amount')->default('0');
            $table->integer('adjusted_net_salary')->default('0');
            $table->integer('adjusted_tax')->default('0');
            $table->integer('adjusted_taxable_salary')->default('0');
            $table->integer('adjusted_gross_salary')->default('0');
            $table->integer('created_by');
            $table->integer('updated_by');
            $table->tinyInteger('status')->default('0');
            $table->text('comment')->nullable();
            $table->string('payment_method',50)->nullable();
            $table->string('action',50)->nullable();
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
        Schema::dropIfExists('payroll_adjustments');
    }
};
