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
        Schema::table('job_applicant', function (Blueprint $table) {
            $columns = [
                // Personal Information
                'date_of_birth' => ['type' => 'date', 'nullable' => true],
                'gender' => ['type' => 'string', 'length' => 20, 'nullable' => true],
                'nationality' => ['type' => 'string', 'length' => 100, 'nullable' => true],

                // Address Information
                'current_address' => ['type' => 'string', 'length' => 500, 'nullable' => true],
                'city' => ['type' => 'string', 'length' => 100, 'nullable' => true],
                'state' => ['type' => 'string', 'length' => 100, 'nullable' => true],
                'country' => ['type' => 'string', 'length' => 100, 'nullable' => true],

                // Professional Information
                'current_employer' => ['type' => 'string', 'length' => 255, 'nullable' => true],
                'current_position' => ['type' => 'string', 'length' => 255, 'nullable' => true],
                'notice_period' => ['type' => 'string', 'length' => 50, 'nullable' => true],
                'expected_salary' => ['type' => 'decimal', 'precision' => 12, 'scale' => 2, 'nullable' => true],

                // Online Presence
                'linkedin_url' => ['type' => 'string', 'length' => 500, 'nullable' => true],
                'portfolio_url' => ['type' => 'string', 'length' => 500, 'nullable' => true],

                // Additional Information
                'referral_source' => ['type' => 'string', 'length' => 100, 'nullable' => true],
                'additional_comments' => ['type' => 'text', 'nullable' => true],
            ];

            foreach ($columns as $column => $definition) {
                if (!Schema::hasColumn('job_applicant', $column)) {
                    if ($definition['type'] === 'date') {
                        $table->date($column)->nullable();
                    } elseif ($definition['type'] === 'text') {
                        $table->text($column)->nullable();
                    } elseif ($definition['type'] === 'decimal') {
                        $table->decimal($column, $definition['precision'], $definition['scale'])->nullable();
                    } else {
                        $table->string($column, $definition['length'] ?? 255)->nullable();
                    }
                }
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('job_applicant', function (Blueprint $table) {
            $columns = [
                'date_of_birth',
                'gender',
                'nationality',
                'current_address',
                'city',
                'state',
                'country',
                'current_employer',
                'current_position',
                'notice_period',
                'expected_salary',
                'linkedin_url',
                'portfolio_url',
                'referral_source',
                'additional_comments',
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('job_applicant', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
