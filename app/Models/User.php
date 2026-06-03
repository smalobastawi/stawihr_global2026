<?php
/*
 * Copyright (c) 2023/9/6 sw@stawitech
 */

namespace App\Models;

use App\Models\Role;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Activitylog\Facades\LogBatch;
use Spatie\Activitylog\LogOptions;
use Spatie\Permission\Traits\HasRoles;

//use Haruncpi\LaravelUserActivity\Traits\Loggable;
class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles, SoftDeletes;

    //use Loggable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $table = 'user';
    protected $primaryKey = 'id';


    protected $fillable = [
        'id',
        'role_id',
        'user_name',
        'email',
        'password',
        'status',
        'created_by',
        'deleted_at',
        'updated_by',
        'password_changed_at',
        'google_id',
        'token',
        'google_access_token',
        'refresh_token',
        'expires_in',
        'msisdn',
        'verification_code',
        'verification_code_expiry_date',
        'password_expiry_date',
        'google_ids',
        'company_id',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'google_ids' => 'array', // Add this line
    ];

    public function employeeDetails()
    {
        return $this->hasOne(Employee::class, 'user_id', 'id')->withTrashed();
    }
    public function getActivitylogOptions(): LogOptions
    {
        LogBatch::startBatch();
        return LogOptions::defaults()
            ->logAll();
    }


    public function location()
    {
        return $this->belongsTo(Location::class, 'location_id', 'location_id');
    }

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id', 'id');
    }

    public function PermittedCompanies()
    {
        return $this->hasMany(CompanyPermissions::class)
            ->distinct();
    }

    public function isSuperAdmin()
    {
        return $this->hasRole('SuperAdmin');
    }
    public function hasGoogleId($googleId): bool
    {
        if (empty($this->google_ids)) {
            return false;
        }

        return in_array($googleId, $this->google_ids);
    }

    // Helper method to add a Google ID
    public function addGoogleId($googleId): void
    {
        $googleIds = $this->google_ids ?? [];

        if (!in_array($googleId, $googleIds)) {
            $googleIds[] = $googleId;
            $this->google_ids = $googleIds;
            $this->save();
        }
    }
}
