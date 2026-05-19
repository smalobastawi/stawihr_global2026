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
            $table->string('code')->nullable();
             $table->decimal('default_percentage', 10, 2)->nullable();
            $table->boolean('is_statutory')->default(false);
            $table->boolean('is_active')->default(true);

            // Ensure the code is unique
            $table->unique('code');
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
            $table->dropUnique(['code']);
            $table->dropColumn('code');
            $table->dropColumn('default_percentage');
            $table->dropColumn('is_statutory');
            $table->dropColumn('is_active');
        });
    }
};
