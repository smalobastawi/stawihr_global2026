<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToCompany;

class Offboarding extends Model
{
    //use BelongsToCompany;

    use HasFactory;
    protected $table = 'offboarding_process';
    protected $fillable = [
        'checklist_name',
        'description',
        'cleared',
        'comment',
        'cleared_by_id'
    ];
}
