<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToCompany;
use App\Lib\Enumerations\TrainingAttendanceStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TrainingAttendant extends Model
{
    //use BelongsToCompany;

    use HasFactory;

    // Define the table associated with the model (if different from default)
    protected $table = 'training_attendants';

    // Specify the fields that are mass assignable
    protected $fillable = [
        'employee_id',
        'training_id',
        'status',
        'responded_at'
    ];

    protected $casts = [
        'responded_at' => 'datetime',
        'status' => 'integer'
    ];

    // Add this method to check status
    public function isConfirmed()
    {
        return $this->status === TrainingAttendanceStatus::CONFIRMED;
    }

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
