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
        Schema::create('leave_justifications', function (Blueprint $table) {
            $table->id();
            $table->integer('leave_application_id');
            $table->string('file_name')->nullable();
            $table->string('file_url')->nullable();
            $table->integer('employee_id')->nullable();
            $table->timestamps();
        });
        Schema::table('leave_application', function (Blueprint $table) {
            $table->integer('reliever_ack')->default(0);
            $table->string('purpose')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('leave_justifications');
        
        Schema::table('leave_application', function (Blueprint $table) {
            $table->dropColumn('reliever_ack');
        });
    }
};
