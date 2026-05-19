<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMissingColumnsToEmployeeTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('employee', function (Blueprint $table) {
            // Add new columns
            $table->string('contract_status')->nullable()->after('permanent_status');
            $table->string('location')->nullable()->after('address');
            $table->string('sub_location')->nullable()->after('location');
            $table->string('program')->nullable()->after('sub_location');
            $table->string('sub_programs')->nullable()->after('program');
            //$table->string('titles')->nullable()->after('sub_programs');
            $table->string('contract_type')->nullable()->after('sub_programs');
            $table->date('start_date')->nullable()->after('contract_type');
            $table->integer('years_in_service')->nullable()->after('start_date');
            $table->date('end_of_probation')->nullable()->after('years_in_service');
            $table->date('end_of_contract')->nullable()->after('end_of_probation');
            $table->integer('age')->nullable()->after('date_of_birth');
            
            $table->string('bank')->nullable()->after('personal_phone');
            $table->string('bank_branch')->nullable()->after('bank');
            $table->string('brank_branch_code')->nullable()->after('bank_branch');
            $table->string('bank_account_number')->nullable()->after('brank_branch_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employee', function (Blueprint $table) {
            // Drop the columns if the migration is rolled back
            $table->dropColumn([
               
                'contract_status',
                'location',
                'sub_location',
                'program',
                'sub_programs',
                //'titles',
                'contract_type',
                'start_date',
                'years_in_service',
                'end_of_probation',
                'end_of_contract',
                'age',
             
                'bank',
                'bank_branch',
                'brank_branch_code',
                'bank_account_number',
            ]);
        });
    }
}