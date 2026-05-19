<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToCompany;
use App\Models\Termination;
use App\Models\Employee;

class TerminationDocs extends Model
{
    //use BelongsToCompany;

    use HasFactory;


    protected $fillable = [
        'termination_id',
        'employee_id',
        'document_name',
        'file_url',
        'created_at',
        'updated_at'
    ];

    // Relationship with Termination
    public function termination()
    {
        return $this->belongsTo(Termination::class);
    }

    // Relationship with Employee
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
