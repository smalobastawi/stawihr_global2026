<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class EmployeeFeedbackResponse extends Model
{
    //use BelongsToCompany;

    use HasFactory;

    protected $table = 'employee_feedback_responses';
    protected $fillable = [
        'feedback_id',
        'responder_id',
        'location_id',
        'content',
        'created_by',
        'deleted_by',
    ];

    public function feedback()
    {
        return $this->belongsTo(EmployeeFeedback::class);
    }
}
