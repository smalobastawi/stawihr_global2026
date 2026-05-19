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
        Schema::table('hr_documents', function (Blueprint $table) {
         // Drop the existing columns first
            if (Schema::hasColumn('hr_documents', 'approved_by')) {
                $table->dropColumn('approved_by');
            }

            if (Schema::hasColumn('hr_documents', 'rejected_by')) {
                $table->dropColumn('rejected_by');
            }
        });

        Schema::table('hr_documents', function (Blueprint $table) {
            // Add the JSON columns
            $table->json('approved_by')->nullable();
            $table->json('rejected_by')->nullable();
        });

     
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('hr_documents', function (Blueprint $table) {
            if (Schema::hasColumn('hr_documents', 'approved_by')) {
                $table->dropColumn('approved_by');
            }

            if (Schema::hasColumn('hr_documents', 'rejected_by')) {
                $table->dropColumn('rejected_by');
            }
        });

        Schema::table('hr_documents', function (Blueprint $table) {
            $table->foreignId('approved_by')->nullable()->constrained('user');
            $table->foreignId('rejected_by')->nullable()->constrained('user');
        });
    }
};
