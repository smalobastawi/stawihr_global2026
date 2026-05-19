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
                $table->unsignedBigInteger('deduction_type_id');
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

                // Foreign keys - deduction_type_id FK removed because deduction_types table is created later
                $table->foreign('employee_id')->references('employee_id')->on('employee')->onDelete('cascade');
                // deduction_type_id FK will be added by 2025_09_21_232139_drop_relationship_on_deduction_type.php
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