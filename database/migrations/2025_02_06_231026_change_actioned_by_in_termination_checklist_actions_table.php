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
        Schema::table('termination_checklist_actions', function (Blueprint $table) {
            // Add the column if it doesn't exist
            if (!Schema::hasColumn('termination_checklist_actions', 'actioned_by')) {
                $table->unsignedBigInteger('actioned_by')->references('id')->on('user')->onDelete('set null');
            }

            // Add foreign key constraint

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('termination_checklist_actions', function (Blueprint $table) {
            $table->dropColumn('actioned_by');
            $table->unsignedBigInteger('actioned_by')->references('employee_id')->on('employee')->onDelete('set null');
        });
    }
};