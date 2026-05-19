<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('termination', function (Blueprint $table) {
            $table->tinyInteger('reinstatement_status')->default(0)->comment('0=Not Reinstated, 1=Reinstated')->after('arrears_paid');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('termination', function (Blueprint $table) {
            $table->dropColumn('reinstatement_status');
        });
    }
};
