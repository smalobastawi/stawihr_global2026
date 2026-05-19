<?php
/*
 * Copyright (c) 2023/9/6 sw@stawitech
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToCompany;

class Absentee extends Model
{
    //use BelongsToCompany;

    //
    protected $primaryKey = 'id';
    protected $fillable = array('employee_id', 'date', 'absence_description', "created_at", "updated_at");
}
