<?php
// database/migrations/YYYY_MM_DD_create_bank_branches_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        Schema::create('bank_branches', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();
            $table->unsignedBigInteger('bank_id');
            $table->string('branch_code', 10);
            $table->string('branch_name');
            $table->string('swift_code')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->text('address')->nullable();
            $table->boolean('status')->default(true);
            $table->timestamps();
            $table->softDeletes();

            // Add index first, then foreign key
            $table->index('bank_id');

            // Unique constraint - branch code must be unique per bank
            $table->unique(['bank_id', 'branch_code']);
        });

        // Add foreign key constraint separately
        Schema::table('bank_branches', function (Blueprint $table) {
            $table->foreign('bank_id')
                ->references('id')
                ->on('banks')
                ->onDelete('cascade');
        });

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bank_branches', function (Blueprint $table) {
            $table->dropForeign(['bank_id']);
        });

        Schema::dropIfExists('bank_branches');
    }
};
