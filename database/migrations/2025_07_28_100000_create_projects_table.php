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
        Schema::create('projects', function (Blueprint $table) {
            $table->id(); // Primary key
            $table->string('name'); // Project name
            $table->date('start_date'); // Project start date
            $table->date('end_date'); // Project end date
            $table->unsignedBigInteger('main_project')->nullable(); // Parent project (self-referencing)
            $table->foreign('main_project')->references('id')->on('projects')->nullOnDelete(); // Foreign key constraint
            $table->unsignedBigInteger('created_by'); // User who created the project
            $table->unsignedBigInteger('updated_by')->nullable(); // User who last updated the project
            $table->enum('status', ['active', 'inactive', 'completed'])->default('active'); // Project status
            $table->timestamps(); // Created and updated timestamps
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('projects');
    }
};
