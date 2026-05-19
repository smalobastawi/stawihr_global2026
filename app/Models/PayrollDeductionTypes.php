<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\SoftDeletes;

class PayrollDeductionTypes extends Model
{
    //use BelongsToCompany;

    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'tax_deductible',
        'default_amount',
        'percentage_of_basic',
        'limit_per_month',
        'status',
        'created_by',
        'updated_by',
        'deleted_by',
        'deduction_type',
    ];
}
