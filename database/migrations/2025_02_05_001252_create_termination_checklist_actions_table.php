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
        Schema::create('termination_checklist_actions', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->unsignedBigInteger('termination_checklist_id')->foreign('termination_checklist_id')->references('id')->on('termination_checklists')->onDelete('no_action');
            $table->unsignedBigInteger('termination_id')->references('termination_id')->on('termination')->onDelete('no_action');
            $table->unsignedBigInteger('actioned_by')->references('employee_id')->on('employee')->onDelete('set null');
            $table->longText('comment')->nullable();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('termination_checklist_actions');
    }
};
