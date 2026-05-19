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
        Schema::table('leave_group_settings', function (Blueprint $table) {
            $table->boolean('allow_advanced_leave')->default(false)->after('max_consecutive_days');
            $table->integer('advanced_period_months')->default(1)->after('allow_advanced_leave');
            $table->decimal('advanced_limit_days', 8, 2)->nullable()->after('advanced_period_months');
        });

        Schema::create('advanced_leave_records', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_id');
            $table->unsignedInteger('leave_type_id');
            $table->unsignedBigInteger('financial_year_id');
            $table->decimal('advanced_days', 8, 2)->default(0);
            $table->decimal('recovered_days', 8, 2)->default(0);
            $table->json('transactions')->nullable();
            $table->timestamps();

            $table->foreign('employee_id')
                ->references('employee_id')
                ->on('employee')
                ->onDelete('cascade');

            $table->foreign('leave_type_id')
                ->references('leave_type_id')
                ->on('leave_type')
                ->onDelete('cascade');

            $table->foreign('financial_year_id')
                ->references('id')
                ->on('financial_years')
                ->onDelete('cascade');

            // Shorter unique constraint name
            $table->unique(
                ['employee_id', 'leave_type_id', 'financial_year_id'],
                'adv_leave_records_unique'
            );
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('advanced_leave_records');

        Schema::table('leave_group_settings', function (Blueprint $table) {
            $table->dropColumn([
                'allow_advanced_leave',
                'advanced_period_months',
                'advanced_limit_days'
            ]);
        });
    }
};