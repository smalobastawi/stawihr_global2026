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
        Schema::create('salary_deductions', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('salary_details_id')->default(0);
            $table->bigInteger('employee_id')->default(0);
            $table->string('payroll_number')->default(0);
            $table->string('deduction_month')->default(0);
            $table->integer('deduction_type')->default(0);
            $table->integer('deduction_name')->default(0);
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
        Schema::dropIfExists('salary_deductions');
    }
};
