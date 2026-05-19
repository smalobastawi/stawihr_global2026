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
        // Check if the table already exists
        if (!Schema::hasTable('employee_pension_schemes')) {
            Schema::create('employee_pension_schemes', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('employee_payroll_id');
                $table->unsignedBigInteger('pension_scheme_id');
                $table->decimal('employee_rate', 5, 2)->default(0);
                $table->decimal('employer_rate', 5, 2)->default(0);
                $table->timestamps();

                $table->foreign('employee_payroll_id')->references('id')->on('employee_payrolls')->onDelete('cascade');
                $table->foreign('pension_scheme_id')->references('id')->on('pension_schemes')->onDelete('cascade');
                $table->unique(['employee_payroll_id', 'pension_scheme_id'], 'emp_payroll_scheme_unique');
            });

            // Optional: Log that the table was created
            \Illuminate\Support\Facades\Log::info('Created employee_pension_schemes table');
        } else {
            // Optional: Log that the table already exists
            \Illuminate\Support\Facades\Log::info('employee_pension_schemes table already exists');
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('employee_pension_schemes');
    }
};
