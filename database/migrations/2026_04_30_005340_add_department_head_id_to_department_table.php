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
        Schema::table('department', function (Blueprint $table) {
            $table->unsignedBigInteger('department_head_id')->nullable()->after('department_name');
            $table->foreign('department_head_id')->references('employee_id')->on('employee')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('department', function (Blueprint $table) {
            $table->dropForeign(['department_head_id']);
            $table->dropColumn('department_head_id');
        });
    }
};
