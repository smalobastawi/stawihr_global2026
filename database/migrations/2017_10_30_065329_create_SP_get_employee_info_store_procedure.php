<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSPGetEmployeeInfoStoreProcedure extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS SP_getEmployeeInfo; CREATE  PROCEDURE SP_getEmployeeInfo(IN employeeId INT(10))
        BEGIN
	       SELECT employee.*,user.`user_name` FROM employee 
            INNER JOIN `user` ON `user`.`id` = employee.`id`
            WHERE employee_id = employeeId;
        END');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS SP_getEmployeeInfo');
    }
}
