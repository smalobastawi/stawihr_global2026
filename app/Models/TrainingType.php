<?php
/*
 * Copyright (c) 2023/9/6 sw@stawitech
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToCompany;

class TrainingType extends Model
{
    //use BelongsToCompany;

    protected $table = 'training_type';
    protected $primaryKey = 'training_type_id';

    protected $fillable = [
        'training_type_id',
        'training_type_name',
        'status'
    ];
}
