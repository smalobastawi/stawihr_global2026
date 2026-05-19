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
        Schema::create('leave_schedules', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_id');
            $table->unsignedInteger('leave_type_id');
            $table->date('scheduled_from_date');
            $table->date('scheduled_to_date');
            $table->integer('number_of_days')->default(0);
            $table->text('purpose')->nullable();
            $table->enum('status', ['scheduled', 'applied', 'cancelled', 'completed'])->default('scheduled');
            $table->boolean('notification_sent')->default(false);
            $table->dateTime('notification_sent_at')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Indexes (foreign keys handled separately to avoid constraint issues)
            $table->index('employee_id');
            $table->index('leave_type_id');
            $table->index('scheduled_from_date');
            $table->index('status');
        });

        // Add foreign keys separately with raw SQL for better error handling
        try {
            Schema::table('leave_schedules', function (Blueprint $table) {
                $table->foreign('employee_id')->references('employee_id')->on('employee')->onDelete('cascade');
            });
        } catch (\Exception $e) {
            // Foreign key may fail - log it but don't stop migration
            \Illuminate\Support\Facades\Log::warning('Could not add employee_id foreign key to leave_schedules: ' . $e->getMessage());
        }

        try {
            Schema::table('leave_schedules', function (Blueprint $table) {
                $table->foreign('leave_type_id')->references('leave_type_id')->on('leave_type')->onDelete('cascade');
            });
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::warning('Could not add leave_type_id foreign key to leave_schedules: ' . $e->getMessage());
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leave_schedules');
    }
};
