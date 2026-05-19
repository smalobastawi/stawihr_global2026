<?php
/*
 * Copyright (c) 2023/9/6 sw@stawitech
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class TrainingInfo extends Model
{
    //use BelongsToCompany;

    use softDeletes;
    protected $table = 'training_info';
    protected $primaryKey = 'training_info_id';

    protected $fillable = [
        'training_info_id',
        'training_type_id',
        'employee_id',
        'subject',
        'start_date',
        'end_date',
        'description',
        'certificate',
        'created_by',
        'updated_by',
        'location_id'
    ];


    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }


    public function trainingType()
    {
        return $this->belongsTo(TrainingType::class, 'training_type_id');
    }


    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }
}
