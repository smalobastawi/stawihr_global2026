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
        Schema::create('employee_payrolls', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_id');
            $table->string('payroll_number')->unique();
            $table->decimal('basic_salary', 12, 2);
            $table->string('currency', 3)->default('KES');
            $table->enum('payment_method', ['bank_transfer', 'mobile_money', 'cash', 'cheque'])->default('bank_transfer');
            $table->string('bank_name')->nullable();
            $table->string('bank_branch')->nullable();
            $table->string('account_number')->nullable();
            $table->string('account_name')->nullable();
            $table->string('kra_pin')->nullable();
            $table->string('nssf_number')->nullable();
            $table->string('shif_number')->nullable();
            $table->enum('tax_status', ['resident', 'non_resident', 'exempt'])->default('resident');
            $table->boolean('disability_exemption')->default(false);
            $table->unsignedBigInteger('pension_scheme_id')->nullable();
            $table->boolean('is_active')->default(true);
            $table->date('effective_date');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('employee_id')->references('employee_id')->on('employee')->onDelete('cascade');
            $table->foreign('pension_scheme_id')->references('id')->on('pension_schemes')->onDelete('set null');
            $table->foreign('created_by')->references('id')->on('user')->onDelete('set null');
            $table->foreign('updated_by')->references('id')->on('user')->onDelete('set null');
            
            $table->index(['employee_id', 'is_active']);
            $table->index('payroll_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_payrolls');
    }
};