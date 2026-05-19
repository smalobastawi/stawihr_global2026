<?php

namespace App\Models\Vehicle;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use App\Lib\Enumerations\GeneralStatus;

class Vehicle extends Model
{
    use SoftDeletes, LogsActivity;

    protected $table = 'vehicles';

    protected $fillable = [
        'registration_number',
        'make',
        'model',
        'engine_number',
        'purchase_date',
        'purchase_price',
        'ownership_status',
        'location_id',
        'status',
        'remarks',
        'company_id',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'purchase_price' => 'decimal:2',
        'purchase_date' => 'date',
        'status' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected $appends = ['full_name', 'current_driver_id', 'current_driver'];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->useLogName('vehicle');
    }

    public function getFullNameAttribute()
    {
        return $this->make . ' ' . $this->model . ' (' . $this->registration_number . ')';
    }

    // Get current driver from the latest active assignment
    public function getCurrentDriverIdAttribute()
    {
        $currentAssignment = $this->assignments()
            ->whereNull('assigned_to')
            ->where('assigned_from', '<=', now())
            ->orderBy('assigned_from', 'desc')
            ->first();

        return $currentAssignment ? $currentAssignment->employee_id : null;
    }

    // Get current driver object
    public function getCurrentDriverAttribute()
    {
        $currentAssignment = $this->assignments()
            ->whereNull('assigned_to')
            ->where('assigned_from', '<=', now())
            ->with('employee')
            ->orderBy('assigned_from', 'desc')
            ->first();

        return $currentAssignment ? $currentAssignment->employee : null;
    }

    public function location()
    {
        return $this->belongsTo(\App\Models\Location::class, 'location_id');
    }

    public function company()
    {
        return $this->belongsTo(\App\Models\Company::class, 'company_id');
    }

    public function assignments()
    {
        return $this->hasMany(VehicleAssignment::class, 'vehicle_id')->orderBy('assigned_from', 'desc');
    }

    public function currentAssignment()
    {
        return $this->hasOne(VehicleAssignment::class, 'vehicle_id')
            ->whereNull('assigned_to')
            ->where('assigned_from', '<=', now())
            ->orderBy('assigned_from', 'desc');
    }

    public function pastAssignments()
    {
        return $this->hasMany(VehicleAssignment::class, 'vehicle_id')
            ->whereNotNull('assigned_to')
            ->orderBy('assigned_from', 'desc');
    }

    public function createdBy()
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(\App\Models\User::class, 'updated_by');
    }

    // Check if vehicle is currently assigned to any driver
    public function isCurrentlyAssigned()
    {
        return $this->assignments()
            ->whereNull('assigned_to')
            ->where('assigned_from', '<=', now())
            ->exists();
    }

    // Get current assignment details
    public function getCurrentAssignment()
    {
        return $this->assignments()
            ->whereNull('assigned_to')
            ->where('assigned_from', '<=', now())
            ->orderBy('assigned_from', 'desc')
            ->first();
    }

    // Scope for active vehicles
    public function scopeActive($query)
    {
        return $query->where('status', GeneralStatus::ACTIVE);
    }

    // Scope for inactive vehicles
    public function scopeInactive($query)
    {
        return $query->where('status', GeneralStatus::INACTIVE);
    }

    // Scope for suspended vehicles
    public function scopeSuspended($query)
    {
        return $query->where('status', GeneralStatus::SUSPENDED);
    }

    // Get status name using GeneralStatus enum
    public function getStatusNameAttribute()
    {
        return GeneralStatus::getName($this->status);
    }

    // Check if vehicle is active
    public function isActive()
    {
        return $this->status === GeneralStatus::ACTIVE;
    }
}
