<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class EmployeeFeedback extends Model
{
    //use BelongsToCompany;

    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'employee_id',
        'location_id',
        'title',
        'content',
        'status',
        'created_by',
        'deleted_by',
        'category_id',
    ];

    public function category()
    {
        return $this->belongsTo(FeedbackCategories::class);
    }
    public function response()
    {
        return $this->hasOne(EmployeeFeedbackResponse::class, 'feedback_id');
    }
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }
}
