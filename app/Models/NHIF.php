<?php
/*
 * Copyright (c) 2023/9/6 sw@stawitech
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToCompany;

class NHIF extends Model
{
    //use BelongsToCompany;

    protected $table = 'nhif_rates';
    protected $primaryKey = 'id';
}
