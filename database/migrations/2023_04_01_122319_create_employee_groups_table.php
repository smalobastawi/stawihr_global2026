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
        Schema::create('employee_groups', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('description');
            $table->foreignId('location_id')->nullable()->constrained('location', 'location_id')->onUpdate('cascade')->onDelete('cascade');

            $table->foreignId('created_by')->nullable()->constrained('employee', 'employee_id')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('approved_by')->nullable()->constrained('employee', 'employee_id')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('deleted_by')->nullable()->constrained('employee', 'employee_id')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('deleted_approved_by')->nullable()->constrained('employee', 'employee_id')->onUpdate('cascade')->onDelete('cascade');

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
        Schema::dropIfExists('employee_groups');
    }
};