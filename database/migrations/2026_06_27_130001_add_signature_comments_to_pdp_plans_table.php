<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pdp_plans', function (Blueprint $table) {
            $table->text('employee_comments')->nullable()->after('employee_ack_date');
            $table->text('supervisor_comments')->nullable()->after('supervisor_approve_date');
            $table->text('hr_comments')->nullable()->after('hr_review_date');
        });
    }

    public function down(): void
    {
        Schema::table('pdp_plans', function (Blueprint $table) {
            $table->dropColumn(['employee_comments', 'supervisor_comments', 'hr_comments']);
        });
    }
};
