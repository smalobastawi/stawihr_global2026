<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('employee', function (Blueprint $table) { 
            $table->string('next_of_kin')->nullable();
            $table->string('next_of_kin_phone', 15)->nullable()->after('next_of_kin');
            $table->string('personal_phone', 15)->nullable()->after('next_of_kin_phone');  
            $table->string('residential_area')->nullable()->after('residential_status');
            $table->string('highest_qualification')->nullable()->after('residential_area');
//            $table->string('ethnicity')->nullable()->after('highest_qualification');
//            $table->string('settlement_type')->nullable()->after('ethnicity');
  //          $table->string('personal_email')->nullable()->after('settlement_type');
        });
    }

    public function down()
    {
        Schema::table('employee', function (Blueprint $table) {
            $table->dropColumn([
                 'next_of_kin', 'next_of_kin_phone', 'personal_phone', 
                 'residential_area', 'highest_qualification'
//,'personal_email','settlement_type','ethnicity',
            ]);
        });
    }
};
