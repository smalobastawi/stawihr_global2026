<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToCompany;

class PayeTaxBand extends Model
{
    //use BelongsToCompany;

    use HasFactory;

    protected $fillable = [
        'country_id',
        'country_name',
        'band_order',
        'monthly_lower_bound',
        'monthly_upper_bound',
        'annual_lower_bound',
        'annual_upper_bound',
        'tax_rate'
    ];

    protected $casts = [
        'monthly_lower_bound' => 'decimal:2',
        'monthly_upper_bound' => 'decimal:2',
        'annual_lower_bound' => 'decimal:2',
        'annual_upper_bound' => 'decimal:2',
        'tax_rate' => 'decimal:2'
    ];
}
