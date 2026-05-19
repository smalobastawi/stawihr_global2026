<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE payroll_record_details MODIFY COLUMN type ENUM('allowance', 'deduction', 'statutory_deduction', 'earning')");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE payroll_record_details MODIFY COLUMN type ENUM('allowance', 'deduction', 'statutory_deduction')");
    }
};