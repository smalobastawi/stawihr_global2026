<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LeaveSchedule extends Model
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'id';

    protected $fillable = [
        'employee_id',
        'leave_type_id',
        'scheduled_from_date',
        'scheduled_to_date',
        'number_of_days',
        'purpose',
        'status',
        'notification_sent',
        'notification_sent_at',
        'created_by',
        'remarks',
    ];

    protected $casts = [
        'scheduled_from_date' => 'date',
        'scheduled_to_date' => 'date',
        'notification_sent_at' => 'datetime',
    ];

    /**
     * Get the employee associated with this leave schedule.
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id', 'employee_id');
    }

    /**
     * Get the leave type associated with this leave schedule.
     */
    public function leaveType()
    {
        return $this->belongsTo(LeaveType::class, 'leave_type_id', 'leave_type_id');
    }

    /**
     * Get the user who created this leave schedule.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Scope for upcoming scheduled leaves.
     */
    public function scopeUpcoming($query)
    {
        return $query->where('scheduled_from_date', '>=', now())
                     ->where('status', 'scheduled');
    }

    /**
     * Scope for leaves that need notification (upcoming in next 7 days).
     */
    public function scopeNeedsNotification($query)
    {
        return $query->where('status', 'scheduled')
                     ->where('notification_sent', false)
                     ->whereBetween('scheduled_from_date', [now(), now()->addDays(7)]);
    }

    /**
     * Scope for a specific employee.
     */
    public function scopeForEmployee($query, $employeeId)
    {
        return $query->where('employee_id', $employeeId);
    }
}
