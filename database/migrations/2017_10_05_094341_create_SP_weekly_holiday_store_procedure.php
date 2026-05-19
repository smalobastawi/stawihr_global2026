<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

class CreateSPWeeklyHolidayStoreProcedure extends Migration
{
    public function up()
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS SP_getWeeklyHoliday');

        DB::unprepared("
            CREATE PROCEDURE SP_getWeeklyHoliday()
            BEGIN
                SELECT day_name FROM weekly_holiday WHERE status = 1;
            END
        ");
    }

    public function down()
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS SP_getWeeklyHoliday');
    }
}