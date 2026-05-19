<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
                DB::statement("DROP VIEW IF EXISTS training_view;");
        DB::statement("CREATE VIEW training_view AS
            SELECT
                tr.id AS trainingID,
                d.department_id AS departmentID,
                e.employee_id As employeeID,
                tr.training_type_id  AS trainingTypeId,
                tf.id AS facilitatorID,
                tt.training_type_name AS training_type,
                tr.description AS training,
                tr.start_date,
                tr.end_date,
                tf.type AS facilitator_type,
                tf.name AS facilitator_name,
                d.department_name AS employee_department,
                CONCAT(e.first_name, ' ', COALESCE(e.middle_name, ''), ' ', e.last_name) AS employee_name,
                CASE WHEN ti.employee_id IS NOT NULL THEN 1 ELSE 0 END AS invited,
                CASE WHEN ta.employee_id IS NOT NULL THEN 1 ELSE 0 END AS attended,
                ti.approved AS invite_approved,
                ta.approved AS attendance_approved
            FROM (
                SELECT training_id, employee_id
                FROM training_invitees
                UNION
                SELECT training_id, employee_id
                FROM training_attendants
            ) re
            LEFT JOIN training_invitees ti ON re.training_id = ti.training_id AND re.employee_id = ti.employee_id
            LEFT JOIN training_attendants ta ON re.training_id = ta.training_id AND re.employee_id = ta.employee_id
            JOIN trainings tr ON re.training_id = tr.id
            JOIN training_type tt ON tr.training_type_id = tt.training_type_id
            JOIN training_facilitators tf ON tr.facilitator_id = tf.id
            JOIN employee e ON re.employee_id = e.employee_id
            JOIN department d ON e.department_id = d.department_id
        ");
    }

    public function down()
    {
        DB::statement("DROP VIEW IF EXISTS training_view");
    }
};
