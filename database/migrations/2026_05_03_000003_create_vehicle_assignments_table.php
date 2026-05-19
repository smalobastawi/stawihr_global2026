<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vehicle_assignments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('vehicle_id');
            $table->unsignedBigInteger('employee_id'); // driver
            $table->date('assigned_from');
            $table->date('assigned_to')->nullable(); // null means still assigned
            $table->text('assignment_reason')->nullable(); // why assigned
            $table->text('return_reason')->nullable(); // why returned/unassigned
            $table->unsignedBigInteger('assigned_by')->nullable(); // who assigned
            $table->unsignedBigInteger('returned_by')->nullable(); // who processed return
            $table->dateTime('returned_at')->nullable();
            $table->unsignedBigInteger('company_id')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('vehicle_id')->references('id')->on('vehicles')->onDelete('cascade');
            $table->foreign('employee_id')->references('employee_id')->on('employee')->onDelete('cascade');
            $table->foreign('assigned_by')->references('id')->on('user')->onDelete('set null');
            $table->foreign('returned_by')->references('id')->on('user')->onDelete('set null');
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');

            $table->index(['vehicle_id', 'assigned_from']);
            $table->index(['employee_id', 'assigned_from']);
            $table->index(['assigned_to']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicle_assignments');
    }
};
