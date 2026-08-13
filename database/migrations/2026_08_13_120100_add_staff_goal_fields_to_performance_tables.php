<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('performance_focus_areas', function (Blueprint $table) {
            $table->unsignedBigInteger('employee_id')->nullable()->after('designation_id');
            $table->unsignedBigInteger('appraisal_id')->nullable()->after('employee_id');
            $table->string('source', 20)->default('hr')->after('appraisal_id'); // hr | staff

            $table->foreign('employee_id')->references('employee_id')->on('employee')->onDelete('cascade');
            $table->foreign('appraisal_id')->references('appraisal_id')->on('performance_appraisals')->onDelete('cascade');
            $table->index(['source', 'employee_id']);
        });

        Schema::table('performance_goals', function (Blueprint $table) {
            $table->unsignedBigInteger('employee_id')->nullable()->after('focus_area_id');
            $table->string('source', 20)->default('hr')->after('employee_id'); // hr | staff

            $table->foreign('employee_id')->references('employee_id')->on('employee')->onDelete('cascade');
            $table->index(['source', 'employee_id']);
        });

        Schema::table('performance_appraisals', function (Blueprint $table) {
            $table->enum('goals_defined_by', ['hr', 'staff'])->default('hr')->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('performance_appraisals', function (Blueprint $table) {
            $table->dropColumn('goals_defined_by');
        });

        Schema::table('performance_goals', function (Blueprint $table) {
            $table->dropForeign(['employee_id']);
            $table->dropIndex(['source', 'employee_id']);
            $table->dropColumn(['employee_id', 'source']);
        });

        Schema::table('performance_focus_areas', function (Blueprint $table) {
            $table->dropForeign(['employee_id']);
            $table->dropForeign(['appraisal_id']);
            $table->dropIndex(['source', 'employee_id']);
            $table->dropColumn(['employee_id', 'appraisal_id', 'source']);
        });
    }
};
