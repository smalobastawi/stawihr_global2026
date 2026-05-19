<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('performance_appraisals', function (Blueprint $table) {
            $table->id('appraisal_id');
            $table->unsignedBigInteger('employee_id');
            $table->foreign('employee_id')->references('employee_id')->on('employee')->onDelete('cascade');
            $table->unsignedBigInteger('supervisor_id')->nullable();
            $table->foreign('supervisor_id')->references('employee_id')->on('employee')->onDelete('set null');
            $table->string('review_period'); // e.g. "Jan - June 2026"
            $table->date('review_start_date')->nullable();
            $table->date('review_end_date')->nullable();
            $table->enum('status', ['draft', 'self_review', 'supervisor_review', 'hod_review', 'finalized', 'closed'])->default('draft');

            // Totals
            $table->decimal('total_itemized_weighting', 5, 2)->default(0);
            $table->decimal('total_self_weighting', 5, 2)->default(0);
            $table->decimal('total_review_weighting', 5, 2)->default(0);

            // Comments
            $table->text('employee_comments')->nullable();
            $table->text('supervisor_comments')->nullable();
            $table->text('hod_comments')->nullable();

            // Sign-offs
            $table->boolean('employee_signed')->default(false);
            $table->timestamp('employee_sign_date')->nullable();
            $table->boolean('supervisor_signed')->default(false);
            $table->timestamp('supervisor_sign_date')->nullable();
            $table->boolean('hod_signed')->default(false);
            $table->timestamp('hod_sign_date')->nullable();

            $table->unsignedBigInteger('finalized_by')->nullable();
            $table->foreign('finalized_by')->references('employee_id')->on('employee')->onDelete('set null');
            $table->timestamp('finalized_at')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('performance_appraisals');
    }
};
