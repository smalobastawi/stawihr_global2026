<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApprovalDelegation extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'delegate_to_user_id',
        'model_type',
        'delegation_type',
        'workflow_id',
        'start_date',
        'end_date',
        'is_active',
        'include_submissions',
        'notes',
        'created_by'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'boolean',
        'include_submissions' => 'boolean',
    ];

    // Relationships
    public function delegator()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function delegate()
    {
        return $this->belongsTo(User::class, 'delegate_to_user_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function workflow()
    {
        return $this->belongsTo(ApprovalWorkflow::class, 'workflow_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->where('start_date', '<=', now())
            ->where(function ($q) {
                $q->whereNull('end_date')
                    ->orWhere('end_date', '>=', now());
            });
    }

    public function scopeForDelegator($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeForDelegate($query, $userId)
    {
        return $query->where('delegate_to_user_id', $userId);
    }

    public function scopeForModel($query, $modelType)
    {
        return $query->where(function ($q) use ($modelType) {
            $q->where('model_type', $modelType)
                ->orWhere('delegation_type', 'all')
                ->orWhereNull('model_type');
        });
    }

    // Check if delegation is currently valid
    public function isValid()
    {
        return $this->is_active
            && $this->start_date <= now()
            && (!$this->end_date || $this->end_date >= now());
    }

    // Check if delegation applies to specific model
    public function appliesToModel($modelType)
    {
        return $this->delegation_type === 'all'
            || $this->model_type === $modelType
            || $this->model_type === null;
    }
}
