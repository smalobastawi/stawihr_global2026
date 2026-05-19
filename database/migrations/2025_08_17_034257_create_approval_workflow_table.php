<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        // Migration for approval configuration
        Schema::create('approval_workflows', function (Blueprint $table) {
            $table->id();
            $table->string('model_type'); // e.g. App\Models\Payroll\PayrollRecord
            $table->json('reviewer_config')->nullable(); // Configuration for reviewers
            $table->json('approver_config')->nullable(); // Configuration for approvers
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Migration for approval steps
        Schema::create('approval_steps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('approval_workflow_id')
                ->constrained('approval_workflows')
                ->onDelete('cascade');
            $table->string('type'); // 'reviewer' or 'approver'
            $table->integer('level'); // 1, 2, 3 etc.
            $table->string('name'); // e.g. 'First Reviewer'
            $table->boolean('is_required')->default(true);
            $table->timestamps();
        });

        // Migration for approval assignments
        Schema::create('approval_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('approval_step_id')
                ->constrained('approval_steps')
                ->onDelete('cascade');
            $table->foreignId('user_id')
                ->constrained('user')
                ->onDelete('cascade');
            $table->timestamps();
        });

        // Migration for approval logs
        Schema::create('approval_logs', function (Blueprint $table) {
            $table->id();
            $table->string('approvable_type'); // Polymorphic type
            $table->unsignedBigInteger('approvable_id'); // Polymorphic id
            $table->foreignId('approval_step_id')
                ->constrained('approval_steps')
                ->onDelete('cascade');
            $table->foreignId('user_id')
                ->constrained('user')
                ->onDelete('cascade');
            $table->string('action'); // 'reviewed', 'approved', 'rejected'
            $table->text('comments')->nullable();
            $table->timestamp('action_date')->useCurrent();
            $table->timestamps();

            // Index for polymorphic relation
            $table->index(['approvable_type', 'approvable_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        // Drop tables in reverse order of creation to respect foreign key constraints
        Schema::dropIfExists('approval_logs');
        Schema::dropIfExists('approval_assignments');
        Schema::dropIfExists('approval_steps');
        Schema::dropIfExists('approval_workflows');
    }
};