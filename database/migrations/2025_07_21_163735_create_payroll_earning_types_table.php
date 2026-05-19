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
        Schema::create('payroll_earning_types', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Name of the earning type
            $table->text('description')->nullable(); // Description of the earning type
            $table->boolean('taxable')->default(false); // Whether the earning type is taxable
            $table->decimal('default_amount', 10, 2)->nullable(); 
            $table->decimal('percentage_of_basic', 5, 2)->nullable();
            $table->decimal('limit_per_month'); // Percentage of the basic salary if applicable
            $table->integer('status')->default(1); // Status of the earning type (1 for active, 0 for inactive)
            $table->string('created_by')->nullable(); // User who created the earning type
            $table->string('updated_by')->nullable(); // User who last updated the earning type
            $table->string('deleted_by')->nullable(); // User who deleted the earning type,
            $table->timestamps();
            $table->softDeletes(); // Soft delete column
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('payroll_earning_types');
    }
};
