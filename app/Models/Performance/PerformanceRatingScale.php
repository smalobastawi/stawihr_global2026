<?php

namespace App\Models\Performance;

use Illuminate\Database\Eloquent\Model;

class PerformanceRatingScale extends Model
{
    protected $table = 'performance_rating_scales';
    protected $primaryKey = 'rating_scale_id';

    protected $fillable = [
        'points',
        'rating_label',
        'description',
        'definition',
        'score_range',
        'is_active',
    ];

    protected $casts = [
        'points' => 'integer',
        'is_active' => 'boolean',
    ];
}
