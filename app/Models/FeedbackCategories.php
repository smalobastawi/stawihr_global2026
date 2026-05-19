<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\SoftDeletes;

class FeedbackCategories extends Model
{
    ////use BelongsToCompany;

    use HasFactory;
    use SoftDeletes;
    protected $fillable = ['name', 'status', 'description'];
}
