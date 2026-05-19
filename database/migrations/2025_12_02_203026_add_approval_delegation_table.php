<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('approval_delegations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id'); // The user who is delegating
            $table->unsignedBigInteger('delegate_to_user_id'); // The user being delegated to
            $table->string('model_type')->nullable(); // Specific model type (optional)
            $table->string('delegation_type')->default('all'); // all, specific_model, specific_workflow
            $table->unsignedBigInteger('workflow_id')->nullable(); // Specific workflow (optional)
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('include_submissions')->default(true); // Whether delegate can see submissions
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->timestamps();
            $table->softDeletes();

            // Foreign keys
            $table->foreign('user_id')->references('id')->on('user')->onDelete('cascade');
            $table->foreign('delegate_to_user_id')->references('id')->on('user')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('user')->onDelete('cascade');

            // Indexes
            $table->index(['user_id', 'is_active']);
            $table->index(['delegate_to_user_id', 'is_active']);
            $table->index(['model_type', 'is_active']);
        });

        // Add delegation info to approval logs
        Schema::table('approval_logs', function (Blueprint $table) {
            $table->unsignedBigInteger('delegated_from_user_id')->nullable()->after('user_id');
            $table->foreign('delegated_from_user_id')->references('id')->on('user')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('approval_logs', function (Blueprint $table) {
            $table->dropForeign(['delegated_from_user_id']);
            $table->dropColumn('delegated_from_user_id');
        });

        Schema::dropIfExists('approval_delegations');
    }
};
