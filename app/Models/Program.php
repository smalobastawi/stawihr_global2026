<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToCompany;

class Program extends Model
{
    //use BelongsToCompany;

    use HasFactory;

    protected $fillable = [
        'name',
        'start_date',
        'end_date',
        'main_program',
        'created_by',
        'updated_by',
        'status',
        'code', // Added code field
    ];

    // Relationship: Parent program
    public function parent()
    {
        return $this->belongsTo(Program::class, 'main_program');
    }

    // Relationship: Sub-programs
    public function children()
    {
        return $this->hasMany(Program::class, 'main_program');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
