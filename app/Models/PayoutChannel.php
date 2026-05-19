<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToCompany;

class PayoutChannel extends Model
{
    //use BelongsToCompany;

    use HasFactory;

    protected $fillable = [
        'name',
        'relationship',
        'type_of_channel',
        'main_account_number',
        'branch',
        'branch_code',
        'swift_code',
        'approval_status',
        'status',
        'location_id'
    ];

    public function employees()
    {
        return $this->hasMany(Employee::class, 'employee_id');
    }

    public function branch()
    {
        return $this->belongsTo(Location::class, 'location_id');
    }
}
