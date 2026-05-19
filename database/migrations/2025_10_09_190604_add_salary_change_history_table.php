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
        Schema::create('employee_salary_history', function (Blueprint $table) {
            $table->id();

            // Foreign key to employees table
            $table->foreignId('employee_id')
                ->constrained('employee', 'employee_id') // assumes employees table has an 'id' column
                ->onDelete('cascade');

            $table->decimal('previous_salary', 12, 2);
            $table->decimal('new_salary', 12, 2);
            $table->decimal('salary_change_amount', 12, 2);
            $table->decimal('salary_change_percentage', 8, 2);
            $table->date('effective_date');
            $table->string('change_type'); // promotion, adjustment, increment, etc.
            $table->text('change_reason');

            // Foreign key to users table (who changed the salary)
            $table->foreignId('changed_by')
                ->nullable()
                ->constrained('user', 'id')
                ->onDelete('cascade');

            $table->json('metadata')->nullable(); // For additional data
            $table->timestamps();

            // Indexes for efficient querying
            $table->index(['employee_id', 'effective_date']);
            $table->index(['effective_date', 'change_type']);
        });

        // Add columns to employee_payrolls table
        Schema::table('employee_payrolls', function (Blueprint $table) {
            $table->decimal('previous_basic_salary', 12, 2)->nullable();
            $table->date('last_salary_change_date')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('employee_payrolls', function (Blueprint $table) {
            $table->dropColumn(['previous_basic_salary', 'last_salary_change_date']);
        });

        Schema::dropIfExists('employee_salary_history');
    }
};
