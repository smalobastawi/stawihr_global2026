<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ErrorLog extends Model
{
    use HasFactory, HasUuids;
    use SoftDeletes;

    protected $table = 'error_logs';

    protected $fillable = [
        'log_name',
        'description',
        'affected_employee_id',
        'subject',
        'subject_id',
        'causer',
        'logged_check_time',
        'date',
        'error_type',
        'module',
        'properties'
    ];

    protected $casts = [
        'properties' => 'array',
        'logged_check_time' => 'datetime',
        'date' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'causer');
    }

    public function affectedEmployee()
    {
        return $this->belongsTo(Employee::class, 'affected_employee_id');
    }
}