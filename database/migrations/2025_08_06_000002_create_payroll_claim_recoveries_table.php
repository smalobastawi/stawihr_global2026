<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('payroll_claim_recoveries', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('payroll_claim_id');
            $table->unsignedBigInteger('employee_id');
            
            // Recovery period details
            $table->integer('recovery_year');
            $table->integer('recovery_month');
            $table->integer('installment_number'); // 1, 2, 3, etc.
            
            // Recovery amounts
            $table->decimal('scheduled_amount', 10, 2); // Amount scheduled to be recovered
            $table->decimal('actual_amount', 10, 2)->default(0); // Amount actually recovered
            $table->decimal('balance_amount', 10, 2); // Remaining balance after this recovery
            
            // Status tracking
            $table->enum('status', ['pending', 'processed', 'skipped', 'cancelled'])->default('pending');
            $table->timestamp('processed_at')->nullable();
            $table->string('payroll_reference')->nullable(); // Reference to payroll record
            
            // Notes and adjustments
            $table->text('notes')->nullable();
            $table->decimal('adjustment_amount', 10, 2)->default(0); // Any adjustments made
            $table->string('adjustment_reason')->nullable();
            
            // Audit fields
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            
            // Indexes with shortened names
            $table->index(['payroll_claim_id', 'installment_number'], 'pcr_payroll_claim_installment');
            $table->index(['employee_id', 'recovery_year', 'recovery_month'], 'pcr_employee_recovery_period');
            $table->index(['recovery_year', 'recovery_month'], 'pcr_recovery_period');
            $table->index('status', 'pcr_status');
            
            // Foreign key constraints
            $table->foreign('payroll_claim_id')->references('id')->on('payroll_claims')->onDelete('cascade');
            $table->foreign('employee_id')->references('employee_id')->on('employee');
            $table->foreign('created_by')->references('id')->on('user');
            $table->foreign('updated_by')->references('id')->on('user');
            
            // Unique constraint with shortened name
            $table->unique(['payroll_claim_id', 'recovery_year', 'recovery_month'], 'pcr_unique_claim_recovery');
        });
    }

    public function down()
    {
        Schema::dropIfExists('payroll_claim_recoveries');
    }
};