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
        Schema::table('job', function (Blueprint $table) {
            // Add foreign key to link job posts to requisitions
            // Using unsignedBigInteger to match the id() type in job_requisitions table
            if (!Schema::hasColumn('job', 'job_requisition_id')) {
                $table->unsignedBigInteger('job_requisition_id')->nullable()->after('job_id');
            }

            // Add department_id to job posts for better filtering
            if (!Schema::hasColumn('job', 'department_id')) {
                $table->unsignedInteger('department_id')->nullable()->after('location_id');
            }

            // Add number of positions from requisition
            if (!Schema::hasColumn('job', 'number_of_positions')) {
                $table->integer('number_of_positions')->default(1);
            }

            // Add employment type
            if (!Schema::hasColumn('job', 'employment_type')) {
                $table->string('employment_type', 50)->nullable()->after('job_type');
            }

            // Add salary range from approved requisition
            if (!Schema::hasColumn('job', 'minimum_salary')) {
                $table->decimal('minimum_salary', 12, 2)->nullable();
            }
            if (!Schema::hasColumn('job', 'maximum_salary')) {
                $table->decimal('maximum_salary', 12, 2)->nullable();
            }

            // Add index for faster queries
            $table->index(['job_requisition_id', 'status']);
            $table->index(['department_id', 'status']);
        });

        // Add foreign key constraints separately to handle errors gracefully
        try {
            Schema::table('job', function (Blueprint $table) {
                $table->foreign('job_requisition_id')
                      ->references('job_requisition_id')
                      ->on('job_requisitions')
                      ->onDelete('set null');
            });
        } catch (\Exception $e) {
            // If foreign key fails, continue without it
        }

        try {
            Schema::table('job', function (Blueprint $table) {
                $table->foreign('department_id')
                      ->references('department_id')
                      ->on('department')
                      ->onDelete('set null');
            });
        } catch (\Exception $e) {
            // If foreign key fails, continue without it
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('job', function (Blueprint $table) {
            try {
                $table->dropForeign(['job_requisition_id']);
            } catch (\Exception $e) {}
            try {
                $table->dropForeign(['department_id']);
            } catch (\Exception $e) {}
            try {
                $table->dropIndex(['job_requisition_id', 'status']);
            } catch (\Exception $e) {}
            try {
                $table->dropIndex(['department_id', 'status']);
            } catch (\Exception $e) {}
            $table->dropColumn([
                'job_requisition_id',
                'department_id',
                'number_of_positions',
                'employment_type',
                'minimum_salary',
                'maximum_salary'
            ]);
        });
    }
};
