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
        Schema::table('leave_rollovers', function (Blueprint $table) {
       



                $table->unsignedBigInteger('leave_type_id')->nullable(true)->foreign('leave_type_id')->references('leave_type_id')->on('leave_types')->onDelete('no action');
                $table->unsignedBigInteger('financial_year_id')->nullable(true)->foreign('id')->references('id')->on('financial_years')->cascadeOnUpdate()->onDelete('no action');

             
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('leave_rollovers', function (Blueprint $table) {
             
 
            $table->dropColumn(['leave_type_id', 'financial_year_id']);
        });
    }
};
