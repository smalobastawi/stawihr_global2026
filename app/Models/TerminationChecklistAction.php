<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToCompany;

class TerminationChecklistAction extends Model
{
    //use BelongsToCompany;

    use HasFactory;
    protected $fillable = [
        'termination_checklist_id',
        'termination_id',
        'action',
        'comment',
        'status',
        'created_by',
        'actioned_by',
    ];

    public function checklist()
    {
        return $this->belongsTo(TerminationChecklist::class, 'termination_checklist_id', 'id');
    }

    public function clearedBy()
    {
        return $this->belongsTo(User::class, 'actioned_by', 'id');
    }
}
