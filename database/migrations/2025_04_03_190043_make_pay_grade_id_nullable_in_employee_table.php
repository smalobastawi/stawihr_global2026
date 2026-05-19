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
        // This migration originally made pay_grade_id nullable in the employee table.
        // The pay_grade_id column and pay grade functionality have since been removed
        // from the application. This migration is kept as a no-op to maintain
        // migration history consistency in environments where it was previously run.
        //
        // See: 2026_05_03_151002_remove_job_category_and_job_group_from_employees.php
        //      2026_05_03_151001_remove_pay_grade_columns_from_employee_movements.php
        //      2026_05_03_151000_remove_pay_grade_columns_from_promotions.php
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // No-op: reversing this migration is not applicable since
        // pay grade functionality has been removed from the application.
    }
};
