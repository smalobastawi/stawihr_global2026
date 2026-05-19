<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCeoApprovalToLeaveapplicationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('leave_application', function (Blueprint $table) {
            $table->date('ceo_approval_date')->nullable();
            $table->string('ceo_approval_type')->nullable();
            $table->string('ceo_approval_comments')->nullable();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('leave_application', function (Blueprint $table) {
            $table->dropColumn('ceo_approval_date');
            $table->dropColumn('ceo_approval_type');
            $table->dropColumn('ceo_approval_comments');
        });
    }
}
