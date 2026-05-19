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
        if (!Schema::hasTable('employee_deductions')) {
            Schema::create('employee_deductions', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('employee_id');
                $table->unsignedInteger('payroll_deduction_type_id');
                $table->string('deduction_category')->nullable();
                $table->decimal('percentage', 8, 2)->nullable();
                $table->decimal('rate', 8, 2)->nullable();
                $table->integer('units')->nullable();
                $table->decimal('amount', 8, 2)->nullable();
                $table->text('description')->nullable();
                $table->integer('status')->default(1);
                $table->unsignedBigInteger('created_by')->nullable();
                $table->string('reference_number')->nullable()->unique();
                $table->timestamps();

                $table->foreign('employee_id')->references('employee_id')->on('employee')->onDelete('cascade');
                $table->foreign('payroll_deduction_type_id')->references('id')->on('deduction_types')->onDelete('cascade');
                $table->foreign('created_by')->references('id')->on('user')->onDelete('set null');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('employee_deductions');
    }
};