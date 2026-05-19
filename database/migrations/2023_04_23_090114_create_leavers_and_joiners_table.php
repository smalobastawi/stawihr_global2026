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
        Schema::create('leavers_and_joiners', function (Blueprint $table) {
            $table->id();
            $table->string('employee_id')->nullable();
            $table->string('payroll_number')->nullable();
            $table->string('national_id')->nullable();
            $table->string('first_name')->nullable();
            $table->string('middle_name')->nullable();
            $table->string('last_name')->nullable();
            
            $table->date('date_of_movement');
            $table->date('date_approved')->nullable();
            $table->integer('approval_status')->comment('0-pending, 1-approved, 2-send_for_amends, 3-rejected')->default(0);
            $table->string('movement_type')->comment('leaving, joining');
            $table->string('reason')->comment('For leaving -Resignation ,Temporary Layoff, Retrenchment, Retirement; for joining-Permanent employment, temporary employment, contract employment');
            $table->timestamps();
            $table->softDeletes();

            //approvals here
            $table->foreignId('created_by')->constrained('user', 'id')->onUpdate('cascade')->onDelete('cascade')->nullable();

            $table->foreignId('stage1_approved_by')->nullable()->constrained('user', 'id')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('stage2_approved_by')->nullable()->constrained('user', 'id')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('stage3_approved_by')->nullable()->constrained('user', 'id')->onUpdate('cascade')->onDelete('cascade');

            $table->integer('stage1_approval_status')->default(0);
            $table->integer('stage2_approval_status')->default(0);
            $table->integer('stage3_approval_status')->default(0);


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
        Schema::dropIfExists('leavers_and_joiners');
    }
};
