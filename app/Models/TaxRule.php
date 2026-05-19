<?php
/*
 * Copyright (c) 2023/9/6 sw@stawitech
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToCompany;

class TaxRule extends Model
{
    //use BelongsToCompany;

    protected $table = 'tax_rule';
    protected $primaryKey = 'tax_rule_id';

    protected $fillable = [
        'tax_rule_id',
        'amount',
        'percentage_of_tax',
        'amount_of_tax',
        'gender',
        'max_amount',
        'min_amount'
    ];
}
