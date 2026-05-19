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
        if (!Schema::hasTable('leave_adjustments')) {
            Schema::create('leave_adjustments', function (Blueprint $table) {
                $table->id();
                $table->unsignedInteger('employee_id');
                $table->unsignedInteger('leave_type_id');
                $table->unsignedInteger('leave_application_id')->nullable();
                $table->decimal('adjustment_days', 8, 2);
                $table->string('reason');
                $table->date('adjustment_date');
                $table->unsignedBigInteger('adjusted_by');
                $table->text('notes')->nullable();
                $table->timestamps();
                
                // Add indexes for better query performance
                $table->index('employee_id');
                $table->index('leave_type_id');
                $table->index('leave_application_id');
                $table->index('adjusted_by');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('leave_adjustments');
    }
};
