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
        // Check if table already exists before creating
        if (!Schema::hasTable('leave_adjustments')) {
            Schema::create('leave_adjustments', function (Blueprint $table) {
                $table->id();
                $table->unsignedInteger('employee_id');
                $table->unsignedInteger('leave_type_id');
                $table->unsignedBigInteger('financial_year_id');
                $table->enum('adjustment_type', ['add', 'deduct']);
                $table->decimal('days', 8, 2);
                $table->text('reason');
                $table->unsignedBigInteger('created_by');
                $table->unsignedBigInteger('approved_by')->nullable();
                $table->enum('status', ['pending', 'approved', 'rejected'])->default('approved');
                $table->timestamp('approved_at')->nullable();
                $table->text('rejection_reason')->nullable();
                $table->timestamps();
                $table->softDeletes();

                // Indexes for better performance (foreign keys removed to avoid constraint issues)
                $table->index('employee_id');
                $table->index('leave_type_id');
                $table->index('financial_year_id');
                $table->index('status');
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
        Schema::dropIfExists('leave_adjustments');
    }
};
