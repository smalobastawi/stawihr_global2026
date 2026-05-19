<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToCompany;

class TrainingInvitee extends Model
{
    //use BelongsToCompany;

    use HasFactory;

    // Define the table associated with the model (if different from default)
    protected $table = 'training_invitees';

    // Specify the fields that are mass assignable
    protected $fillable = [
        'employee_id',
        'training_id',
        'status',  // SENT|ACCEPTED|DECLINED 
        'sent_by',
        'responded_at',
        'responded_from'
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
    ];

    // Define the relationship with Employee model
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    // Define the relationship with Training model
    public function training()
    {
        return $this->belongsTo(Training::class, 'training_id');
    }

    // Define the relationship with User model (for approved_by field)
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
