<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToCompany;
use App\Models\Department;

class Project extends Model
{
    //use BelongsToCompany;


    protected $table = 'projects';

    protected $fillable = [
        'name',
        'code',
        'start_date',
        'end_date',
        'main_project',
        'created_by',
        'status',
    ];

    public function parent()
    {
        return $this->belongsTo(Project::class, 'main_project');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }
}
