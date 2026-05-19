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
        Schema::create('loan_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('max_amount', 15, 2)->nullable();
            $table->decimal('interest_rate', 5, 2)->default(0);
            $table->integer('max_duration_months')->default(12);
            $table->tinyInteger('status')->default(1)->comment('0=inactive,1=active');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
        });

        Schema::create('loans', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_id');
            $table->unsignedBigInteger('loan_type_id');
            $table->decimal('amount', 15, 2);
            $table->decimal('interest_rate', 5, 2)->default(0);
            $table->integer('duration_months');
            $table->decimal('monthly_installment', 15, 2);
            $table->decimal('total_repayable', 15, 2);
            $table->decimal('balance', 15, 2);
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->text('purpose')->nullable();
            $table->text('justification')->nullable();
            $table->tinyInteger('status')->default(0)->comment('0=inactive,1=active,2=suspended');
            $table->tinyInteger('approval_status')->default(-1)->comment('-1=draft,0=pending,1=approved,2=rejected,3=cancelled');
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->text('approval_notes')->nullable();
            $table->date('date_approved')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
        });

        Schema::create('loan_applications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_id');
            $table->unsignedBigInteger('loan_type_id');
            $table->decimal('amount_requested', 15, 2);
            $table->integer('duration_months');
            $table->text('reason')->nullable();
            $table->text('approval_comments')->nullable();
            $table->tinyInteger('status')->default(0)->comment('0=pending,1=approved,2=rejected');
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->timestamp('date_approved')->nullable();
            $table->decimal('amount_approved', 15, 2)->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('loan_deductions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('loan_id');
            $table->unsignedBigInteger('employee_id');
            $table->unsignedBigInteger('payroll_period_id')->nullable();
            $table->decimal('amount', 15, 2);
            $table->date('deduction_date');
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
        });

        Schema::create('manual_loan_deductions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('loan_id');
            $table->unsignedBigInteger('employee_id');
            $table->decimal('amount', 15, 2);
            $table->date('deduction_date');
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('manual_loan_deductions');
        Schema::dropIfExists('loan_deductions');
        Schema::dropIfExists('loan_applications');
        Schema::dropIfExists('loans');
        Schema::dropIfExists('loan_types');
    }
};
