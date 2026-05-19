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
        Schema::table('approval_records', function (Blueprint $table) {
            $table->bigInteger('stages')->comment('this coincides with the  number of approvers from the approval settings table');
            $table->longText('response_approver_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('approval_records', function (Blueprint $table) {
            //
        });
    }
};
