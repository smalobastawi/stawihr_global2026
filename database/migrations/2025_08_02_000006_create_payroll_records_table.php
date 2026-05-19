<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('payroll_records', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_payroll_id');
            $table->unsignedBigInteger('payroll_period_id');
            $table->decimal('basic_salary', 12, 2);
            $table->decimal('total_allowances', 12, 2)->default(0);
            $table->decimal('gross_salary', 12, 2);
            $table->decimal('total_deductions', 12, 2)->default(0);
            $table->decimal('statutory_deductions', 12, 2)->default(0);
            $table->decimal('non_statutory_deductions', 12, 2)->default(0);
            $table->decimal('paye_tax', 12, 2)->default(0);
            $table->decimal('nssf_contribution', 12, 2)->default(0);
            $table->decimal('shif_contribution', 12, 2)->default(0);
            $table->decimal('housing_levy', 12, 2)->default(0);
            $table->decimal('pension_contribution', 12, 2)->default(0);
            $table->decimal('net_salary', 12, 2);
            $table->enum('payment_method', ['bank_transfer', 'mobile_money', 'cash', 'cheque'])->default('bank_transfer');
            $table->string('payment_reference')->nullable();
            $table->date('payment_date')->nullable();
            $table->enum('status', ['draft', 'calculated', 'approved', 'paid', 'cancelled'])->default('draft');
            $table->unsignedBigInteger('processed_by')->nullable();
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('employee_payroll_id')->references('id')->on('employee_payrolls')->onDelete('cascade');
            $table->foreign('payroll_period_id')->references('id')->on('payroll_periods')->onDelete('cascade');
            $table->foreign('processed_by')->references('id')->on('user')->onDelete('set null');
            $table->foreign('approved_by')->references('id')->on('user')->onDelete('set null');
            $table->foreign('created_by')->references('id')->on('user')->onDelete('set null');
            $table->foreign('updated_by')->references('id')->on('user')->onDelete('set null');
            
            $table->unique(['employee_payroll_id', 'payroll_period_id']);
            $table->index(['payroll_period_id', 'status']);
            $table->index('payment_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payroll_records');
    }
};