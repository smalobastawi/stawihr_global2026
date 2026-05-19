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
        Schema::create('employee_earnings_and_deductions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employee', 'employee_id')->onDelete('cascade'); // Foreign key to employee table
            $table->tinyInteger('type')->comment('1 for Earnings, 2 for Deductions'); // Type: Earnings or Deductions
            $table->string('name'); // Name of the earning or deduction (e.g., "Basic salary", "PAYE")
            $table->decimal('amount', 10, 2); // Monetary value
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
        Schema::dropIfExists('employee_earnings_and_deductions');
    }
};
