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
        Schema::table('disciplinary_cases', function (Blueprint $table) {
            $table->date('closed_date')->nullable()->after('status');
            $table->string('remarks')->nullable()->after('closed_date');
            $table->string('closing_remarks')->nullable()->after('closed_date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('disciplinary_cases', function (Blueprint $table) {
            $table->dropColumn('closed_date');
            $table->dropColumn('remarks');
            $table->dropColumn('closing_remarks');
        });
    }
};
