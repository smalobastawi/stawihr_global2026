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
        $tables = [
            "absentees",
            "allowance",
            "approvals",
            "app_licenses",
            "attendances",
            "attendance_overtime_approvals",
            "biometric_devices",
            "biometric_run_logs",
            "bonus_setting",
            "location",
            "company_address_settings",
            "department",
            "designation",
            "employee",
            "employee_attendance_approve",
            "employee_award",
            "employee_bonus",
            "employee_documents",
            "employee_education_qualification",
            "employee_experience",
            "employee_groups",
            "employee_movements",
            "employee_sections",
            "employee_to_deductions",
            "employee_to_work_shift",
            "employee_types",
            "front_settings",
            "holiday",
            "holiday_details",
            "hourly_salaries",
            "interview",
            "ip_settings",
            "job",
            "job_applicant",
            "leavers_and_joiners",
            "leave_application",
            "leave_rollovers",
            "leave_type",
            "lunch_reports",
            "morpho_devices",
            "morpho_device_logs",
            "notice",
            "paryroll9s",
            "password_resets",
            "permissions",
            "print_head_settings",
            "promotion",
            "recurrent_deductions",
            "salary_bonuses",
            "salary_bonus_types",
            "salary_deduction_for_late_attendance",
            "salary_details",
            "services",
            "teams",
            "termination",
            "training_info",
            "training_type",
            "user",
            "warning",
            "weekly_holiday",
            "white_listed_ips",
            "work_shift",
        ];
        foreach ($tables as $table) {

            if (Schema::hasColumn($table, 'deleted_at')) {
                //do nothing
            } else {
                Schema::table($table, function (Blueprint $table) {
                    $table->dateTime('deleted_at')->nullable();
                });
            }

            if (Schema::hasColumn($table, 'status')) {
                //do nothing
            } else {
                Schema::table($table, function (Blueprint $table) {
                    $table->integer('status')->default(1);
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('all_tables', function (Blueprint $table) {
            //
        });
    }
};