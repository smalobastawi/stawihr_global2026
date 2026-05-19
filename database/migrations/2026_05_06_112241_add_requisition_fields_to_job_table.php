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
            $columns = [
                'minimum_qualifications' => ['type' => 'text', 'nullable' => true],
                'experience_required' => ['type' => 'string', 'length' => 255, 'nullable' => true],
                'skills_competencies' => ['type' => 'text', 'nullable' => true],
                'key_responsibilities' => ['type' => 'text', 'nullable' => true],
                'other_benefits' => ['type' => 'text', 'nullable' => true],
            ];

            foreach ($columns as $column => $definition) {
                if (!Schema::hasColumn('job', $column)) {
                    if ($definition['type'] === 'text') {
                        $table->text($column)->nullable();
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
        Schema::table('job', function (Blueprint $table) {
            $columns = [
                'minimum_qualifications',
                'experience_required',
                'skills_competencies',
                'key_responsibilities',
                'other_benefits',
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('job', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
