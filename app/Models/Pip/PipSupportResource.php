<?php

namespace App\Models\Pip;

use Illuminate\Database\Eloquent\Model;

class PipSupportResource extends Model
{
    protected $table = 'pip_support_resources';
    protected $primaryKey = 'resource_id';

    protected $fillable = [
        'pip_id',
        'support_type',
        'description',
        'provider',
        'scheduled_date',
        'status',
    ];

    protected $casts = [
        'scheduled_date' => 'date',
    ];

    public function pip()
    {
        return $this->belongsTo(PipPlan::class, 'pip_id', 'pip_id');
    }
}
