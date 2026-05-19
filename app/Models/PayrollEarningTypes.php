<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\SoftDeletes;

class PayrollEarningTypes extends Model
{
    //use BelongsToCompany;

    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',                // Name of the earning type
        'description',         // Description of the earning type
        'taxable',             // Whether the earning type is taxable
        'is_pensionable',      // Whether the earning type is pensionable
        'is_recurring',        // Whether the earning type is recurring
        'calculation_type',    // Calculation type
        'default_amount',      // Default amount for the earning type
        'percentage_of_basic', // Percentage of the basic salary
        'limit_per_month',     // Limit per month
        'status',              // Status of the earning type
        'created_by',          // User who created the earning type
        'updated_by',          // User who last updated the earning type
        'deleted_by',          // User who deleted the earning type
    ];
}
