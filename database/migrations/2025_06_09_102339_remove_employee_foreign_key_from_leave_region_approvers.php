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
        Schema::table('leave_region_approvers', function (Blueprint $table) {
            //
            $table->dropForeign(['employee_id']);
            $table->unsignedBigInteger('employee_id')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('leave_region_approvers', function (Blueprint $table) {
            //
            $table->foreign('employee_id')
                  ->references('employee_id')
                  ->on('employee')
                  ->onDelete('cascade'); // or whatever your original constraint was
        });
    }
};
