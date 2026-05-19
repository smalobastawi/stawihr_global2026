<?php
/*
 * Copyright (c) 2023/9/6 sw@stawitech
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToCompany;

class PrintHeadSetting extends Model
{
    //use BelongsToCompany;

    protected $table = 'print_head_settings';
    protected $primaryKey = 'print_head_setting_id';

    protected $fillable = [
        'print_head_setting_id',
        'description'
    ];
}
