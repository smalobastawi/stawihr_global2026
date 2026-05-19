<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class AnonymousFeedback extends Model
{
    //use BelongsToCompany;

    use HasFactory;
    protected $fillable = [
        'title',
        'category_id',
        'content',
        'created_at',
        'updated_at',
        'status',           // New
        'action_type',      // New
        'action_description' // New
    ];

    public function category()
    {
        return $this->belongsTo(FeedbackCategories::class);
    }
}
