<?php
/*
 * Copyright (c) 2023/9/6 sw@stawitech
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Designation extends Model
{
    use softDeletes;
    use BelongsToCompany;
    protected $table = 'designation';
    protected $primaryKey = 'designation_id';

    protected $fillable = [
        'designation_id',
        'designation_name',
        'deleted_at'
    ];

    public function employees()
    {
        return $this->hasMany(Employee::class, 'designation_id');
    }
}
