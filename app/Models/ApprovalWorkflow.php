<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToCompany;

class ApprovalWorkflow extends Model
{
    //use BelongsToCompany;

    protected $fillable = [
        'model_type',
        'reviewer_config',
        'approver_config',
        'is_active'
    ];

    protected $casts = [
        'reviewer_config' => 'array',
        'approver_config' => 'array',
        'is_active' => 'boolean'
    ];

    public function steps()
    {
        return $this->hasMany(ApprovalStep::class)
            ->orderByRaw("FIELD(type, 'reviewer', 'approver')")
            ->orderBy('level');
    }

    public static function forModel($modelClass)
    {
        return static::where('model_type', $modelClass)->first();
    }

    public function initializeWorkflow()
    {
        // Create steps based on config
        $this->createSteps('reviewer', $this->reviewer_config);
        $this->createSteps('approver', $this->approver_config);
    }

    protected function createSteps($type, $config)
    {
        if (empty($config['levels'])) {
            return;
        }

        for ($i = 1; $i <= $config['levels']; $i++) {
            $this->steps()->create([
                'type' => $type,
                'level' => $i,
                'name' => ucfirst($type) . ' ' . $i,
                'is_required' => $config['required_levels'] >= $i
            ]);
        }
    }
}
