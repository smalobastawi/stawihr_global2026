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
        Schema::dropIfExists('verification_codes');

        Schema::table('user', function (Blueprint $table) {

                $table->string('verification_code')->nullable();
                $table->timestamp('verification_code_expiry_date')->nullable();

        });


    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user', function (Blueprint $table) {
            
            $table->dropColumn('verification_code');
           
            $table->dropColumn('verification_code_expiry_date');

        });
    }
};
