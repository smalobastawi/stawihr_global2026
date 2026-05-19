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
        Schema::table('termination', function (Blueprint $table) {
            $table->string('national_id');
            $table->string('entry_type')->nullable()->default('auto');
            $table->foreignId('created_by')->nullable()->constrained('employee', 'employee_id')->onUpdate('cascade')->onDelete('cascade');
            $table->integer('stage1_approval_status')->default(0);
            $table->integer('stage2_approval_status')->default(0);
            $table->integer('stage3_approval_status')->default(0);

            $table->foreignId('stage1_approved_by')->nullable()->constrained('employee', 'employee_id')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('stage2_approved_by')->nullable()->constrained('employee', 'employee_id')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('stage3_approved_by')->nullable()->constrained('employee', 'employee_id')->onUpdate('cascade')->onDelete('cascade');

            $table->string('stage1_approval_comments')->nullable();
            $table->string('stage2_approval_comments')->nullable();
            $table->string('stage3_approval_comments')->nullable();

            $table->dateTime('stage1_approval_date')->nullable();
            $table->dateTime('stage2_approval_date')->nullable();
            $table->dateTime('stage3_approval_date')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('termination', function (Blueprint $table) {
            //
        });
    }
};
