<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToCompany;

class CompanySettings extends Model
{
    //use BelongsToCompany;

    use HasFactory;
    protected $table = 'company_settings';
    protected $fillable = [
        'legal_Name',
        'legal_Address',
        'official_contact_number',
        'official_email',
        'company_contact_name',
        'representative_phone',
        'representative_email',
        'KRA_PIN',
        'employer_number',
        'NSSF_employer_number',
        'NHIF_employer_code',
        'financial_year_start'
    ];
}
