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
        Schema::table('job', function (Blueprint $table) {
            if (!Schema::hasColumn('job', 'required_qualification')) {
                $table->string('required_qualification', 100)->nullable()->after('experience_required');
            }
            if (!Schema::hasColumn('job', 'min_years_experience')) {
                $table->unsignedTinyInteger('min_years_experience')->nullable()->after('required_qualification');
            }
            if (!Schema::hasColumn('job', 'required_skills')) {
                $table->text('required_skills')->nullable()->after('min_years_experience');
            }
        });

        Schema::table('job_applicant', function (Blueprint $table) {
            if (!Schema::hasColumn('job_applicant', 'skills')) {
                $table->text('skills')->nullable()->after('highest_qualification');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('job', function (Blueprint $table) {
            foreach (['required_qualification', 'min_years_experience', 'required_skills'] as $column) {
                if (Schema::hasColumn('job', $column)) {
                    $table->dropColumn($column);
                }
            }
        });

        Schema::table('job_applicant', function (Blueprint $table) {
            if (Schema::hasColumn('job_applicant', 'skills')) {
                $table->dropColumn('skills');
            }
        });
    }
};
