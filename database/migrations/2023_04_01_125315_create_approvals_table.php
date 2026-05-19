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
        Schema::create('approvals', function (Blueprint $table) {
            $table->id();
            $table->string('approval_name');
            $table->string('action_item');
            $table->string('item_id');
            $table->string('action_type')->comment('creation, deletion, editing, salaryGeneration etc. take action_type from the current route');

            $table->integer('final_status')->default(0)->comment('0-pending, 1-approved, 2-send-for-amends,3-rejected ');

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

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('approvals');
    }
};
