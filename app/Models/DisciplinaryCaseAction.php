<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class DisciplinaryCaseAction extends Model
{
    //use BelongsToCompany;

    use HasFactory, SoftDeletes;
    protected $fillable = [
        'case_id',
        'action_type',
        'remarks',
        'action_by',
        'action_date',
        'status',
        'attachment',
        'approved_by',
    ];

    public function case()
    {
        return $this->belongsTo(DisciplinaryCase::class, 'case_id');
    }


    public function actionedBy()
    {
        return $this->belongsTo(Employee::class, 'action_by');
    }
    public function approvedBy()
    {
        return $this->belongsTo(Employee::class, 'approved_by');
    }
}
