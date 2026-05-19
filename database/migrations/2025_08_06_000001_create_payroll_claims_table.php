<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('payroll_claims', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_id');
            $table->string('claim_type')->default('general'); // general, medical, travel, overtime, etc.
            $table->string('claim_title');
            $table->text('description')->nullable();
            $table->decimal('claim_amount', 10, 2);
            $table->string('currency', 3)->default('KES');
            
            // Claim period (when the claim is for)
            $table->integer('claim_year');
            $table->integer('claim_month');
            
            // Recovery settings
            $table->enum('recovery_method', ['lump_sum', 'installments'])->default('lump_sum');
            $table->integer('recovery_periods')->nullable(); // Number of payroll periods for recovery
            $table->decimal('recovery_amount_per_period', 10, 2)->nullable();
            $table->integer('recovery_start_year')->nullable();
            $table->integer('recovery_start_month')->nullable();
            
            // Status and approval
            $table->enum('status', ['draft', 'pending_approval', 'approved', 'rejected', 'paid', 'partially_recovered', 'fully_recovered', 'cancelled'])->default('draft');
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->text('approval_notes')->nullable();
            
            // Payment tracking
            $table->decimal('amount_paid', 10, 2)->default(0);
            $table->decimal('amount_recovered', 10, 2)->default(0);
            $table->timestamp('paid_at')->nullable();
            $table->string('payment_reference')->nullable();
            
            // Supporting documentation
            $table->json('attachments')->nullable(); // Store file paths or references
            $table->string('reference_number')->unique();
            
            // Effective dates
            $table->date('effective_date')->nullable();
            $table->date('recovery_completion_date')->nullable();
            
            // Audit fields
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index(['employee_id', 'status']);
            $table->index(['claim_year', 'claim_month']);
            $table->index(['recovery_start_year', 'recovery_start_month']);
            $table->index('status');
            $table->index('reference_number');
            
            // Foreign key constraints
            $table->foreign('employee_id')->references('employee_id')->on('employee');
            $table->foreign('approved_by')->references('id')->on('user');
            $table->foreign('created_by')->references('id')->on('user');
            $table->foreign('updated_by')->references('id')->on('user');
        });
    }

    public function down()
    {
        Schema::dropIfExists('payroll_claims');
    }
};