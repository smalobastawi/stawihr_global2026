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
        Schema::table('deduction', function (Blueprint $table) {
            if (!Schema::hasColumn('deduction', 'description')) {
                $table->string('description')->nullable();
            }
            if (!Schema::hasColumn('deduction', 'deduction_type')) {
                $table->string('deduction_type')->nullable();
            }
            if (!Schema::hasColumn('deduction', 'default_calculation_type')) {
                $table->string('default_calculation_type')->nullable();
            }
            if (!Schema::hasColumn('deduction', 'default_amount')) {
                $table->decimal('default_amount', 10, 2)->nullable();
            }
            if (!Schema::hasColumn('deduction', 'default_percentage')) {
                $table->decimal('default_percentage', 5, 2)->nullable();
            }
            if (!Schema::hasColumn('deduction', 'is_statutory')) {
                $table->boolean('is_statutory')->default(false);
            }
            if (!Schema::hasColumn('deduction', 'is_active')) {
                $table->boolean('is_active')->default(true);
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('deduction', function (Blueprint $table) {
            $columnsToDrop = ['description', 'deduction_type', 'default_calculation_type', 
                            'default_amount', 'default_percentage', 'is_statutory', 'is_active'];
            
            foreach ($columnsToDrop as $column) {
                if (Schema::hasColumn('deduction', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};