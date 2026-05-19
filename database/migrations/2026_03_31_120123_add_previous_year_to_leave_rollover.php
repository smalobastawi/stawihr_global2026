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
        Schema::table('leave_rollovers', function (Blueprint $table) {
            $table->unsignedBigInteger('previous_financial_year_id')->nullable()->after('financial_year_id');
            
            // Optional: Add foreign key constraint
            $table->foreign('previous_financial_year_id')
                  ->references('id')
                  ->on('financial_years')
                  ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
  public function down(): void
    {
        Schema::table('leave_rollovers', function (Blueprint $table) {
            $table->dropForeign(['previous_financial_year_id']);
            $table->dropColumn('previous_financial_year_id');
        });
    }
};
