<?php
/*
 * Copyright (c) 2023/9/6 sw@stawitech
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToCompany;

class Menu extends Model
{
    //use BelongsToCompany;

    use HasFactory;
    public $timestamps = false;
    protected $table = 'menus';
    protected $primaryKey = 'id';
    protected $fillable = [
        'parent_id',
        'action',
        'name',
        'menu_url',
        'module_id',
        'status'
    ];
}
