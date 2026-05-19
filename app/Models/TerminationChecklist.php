<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToCompany;

class TerminationChecklist extends Model
{
    //use BelongsToCompany;

    use HasFactory;
    protected $table = 'termination_checklists';
    protected $fillable = [
        'checklist_name',
        'description',
        'comment',
        'cleared_by_id',
        'created_by'
    ];

    public function getCreatedByAttribute()
    {

        return optional(User::find($this->attributes['created_by']))->user_name;
    }

    public function checkListActions()
    {
        return $this->hasMany(TerminationChecklistAction::class, 'termination_checklist_id', 'id');
    }
}
