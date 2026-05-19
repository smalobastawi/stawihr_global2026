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
        Schema::create('staff_contracts', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('employee_id');
            $table->date('hire_date')->nullable();
            $table->date('probation_start_date')->nullable();
            $table->date('probation_end_date')->nullable();
            $table->date('start_date');
            $table->date('end_date');
            $table->string('contract_document_draft')->nullable();
            $table->string('contract_document_final')->nullable();
            $table->string('contract_type')->nullable();
            $table->integer('status')->default(1);
            $table->integer('approval_status')->default(1);

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
        Schema::dropIfExists('staff_contracts');
    }
};
