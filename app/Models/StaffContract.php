<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class StaffContract extends Model
{
    //use BelongsToCompany;

    use HasFactory, SoftDeletes;

    protected $fillable = [
        'employee_id',
        'hire_date',
        'probation_start_date',
        'probation_end_date',
        'start_date',
        'end_date',
        'contract_document_draft',
        'contract_document_final',
        'status',
        'created_at',
        'updated_at',
        'deleted_at',
        'contract_type',
        'location_id',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }
}
