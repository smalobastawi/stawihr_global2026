<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AnonymizedRecordBackup extends Model
{
    protected $fillable = [
        'user_id',
        'employee_id',
        'user_data',
        'employee_data',
        'role_names',
        'anonymized_by',
        'anonymized_at',
        'restored_by',
        'restored_at',
    ];

    protected $casts = [
        'user_data' => 'array',
        'employee_data' => 'array',
        'role_names' => 'array',
        'anonymized_at' => 'datetime',
        'restored_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id')->withTrashed();
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_id', 'employee_id')->withTrashed();
    }

    public function scopeRestorable($query)
    {
        return $query->whereNull('restored_at');
    }
}
