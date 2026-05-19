<?php

namespace App\Models\Vehicle;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class VehicleAssignment extends Model
{
    use SoftDeletes, LogsActivity;

    protected $table = 'vehicle_assignments';

    protected $fillable = [
        'vehicle_id',
        'employee_id',
        'assigned_from',
        'assigned_to',
        'assignment_reason',
        'return_reason',
        'assigned_by',
        'returned_by',
        'returned_at',
        'company_id',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'assigned_from' => 'date',
        'assigned_to' => 'date',
        'returned_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->useLogName('vehicle_assignment');
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class, 'vehicle_id');
    }

    public function employee()
    {
        return $this->belongsTo(\App\Models\Employee::class, 'employee_id', 'employee_id');
    }

    public function assignedBy()
    {
        return $this->belongsTo(\App\Models\User::class, 'assigned_by');
    }

    public function returnedBy()
    {
        return $this->belongsTo(\App\Models\User::class, 'returned_by');
    }

    public function company()
    {
        return $this->belongsTo(\App\Models\Company::class, 'company_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(\App\Models\User::class, 'updated_by');
    }

    public function scopeCurrent($query)
    {
        return $query->whereNull('assigned_to');
    }

    public function scopePast($query)
    {
        return $query->whereNotNull('assigned_to');
    }

    public function scopeForVehicle($query, $vehicleId)
    {
        return $query->where('vehicle_id', $vehicleId);
    }

    public function scopeForEmployee($query, $employeeId)
    {
        return $query->where('employee_id', $employeeId);
    }

    public function isCurrent()
    {
        return is_null($this->assigned_to);
    }

    public function durationInDays()
    {
        $endDate = $this->assigned_to ?? now();
        return $this->assigned_from->diffInDays($endDate);
    }
}
