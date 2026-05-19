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
        Schema::create('employee_earnings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_id');
            $table->unsignedBigInteger('payroll_earning_type_id');
            $table->enum('calculation_type', ['fixed_amount', 'percentage_of_basic', 'percentage_of_gross', 'hourly_rate', 'daily_rate'])->default('fixed_amount');
            $table->decimal('amount', 15, 2)->default(0.00); // Fixed amount or calculated amount
            $table->decimal('percentage', 5, 2)->nullable(); // Percentage if calculation is percentage-based
            $table->decimal('rate', 10, 2)->nullable(); // Rate for hourly/daily calculations
            $table->integer('units')->nullable(); // Hours/days for rate-based calculations
            $table->decimal('limit_per_month', 15, 2)->nullable(); // Maximum limit per month
            $table->decimal('limit_per_year', 15, 2)->nullable(); // Maximum limit per year
            $table->boolean('is_taxable')->default(true); // Whether this earning is subject to tax
            $table->boolean('is_pensionable')->default(true); // Whether this earning is pensionable
            $table->boolean('is_recurring')->default(true); // Whether this is a recurring earning
            $table->enum('frequency', ['monthly', 'weekly', 'bi_weekly', 'quarterly', 'annually', 'one_time'])->default('monthly');
            $table->date('effective_from'); // When this earning becomes effective
            $table->date('effective_to')->nullable(); // When this earning expires (null for indefinite)
            $table->year('payroll_year')->default(date('Y')); // Payroll year
            $table->tinyInteger('payroll_month')->default(date('n')); // Payroll month (1-12)
            $table->text('description')->nullable(); // Description or notes

            $table->unsignedBigInteger('approved_by')->nullable(); // Who approved this earning
            $table->timestamp('approved_at')->nullable(); // When it was approved
            $table->text('approval_notes')->nullable(); // Approval notes
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Foreign key constraints
            $table->foreign('employee_id')->references('employee_id')->on('employee')->onDelete('cascade');
            $table->foreign('payroll_earning_type_id')->references('id')->on('payroll_earning_types')->onDelete('restrict');
            $table->foreign('approved_by')->references('id')->on('user')->onDelete('set null');
            $table->foreign('created_by')->references('id')->on('user')->onDelete('set null');
            $table->foreign('updated_by')->references('id')->on('user')->onDelete('set null');

            // Indexes for better performance
            $table->index(['employee_id', 'payroll_year', 'payroll_month']);
            $table->index(['payroll_earning_type_id']);
            $table->index(['effective_from', 'effective_to']);
            $table->index(['is_recurring', 'frequency']);

            $table->unique(['employee_id', 'payroll_earning_type_id', 'effective_from'], 'unique_employee_earning');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('employee_earnings');
    }
};