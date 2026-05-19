<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up()
    {
        Schema::create('payout_channels', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('relationship')->nullable();
            $table->string('type_of_channel')->comment('bank, sacco, savings plan, morgage');
            $table->string('main_account_number')->nullable();
            $table->string('branch')->nullable();
            $table->string('branch_code')->nullable();
            $table->string('swift_code')->nullable();
            $table->integer('approval_status')->default(0);
            $table->integer('status')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('payout_channels');
    }
};
