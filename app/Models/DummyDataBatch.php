<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DummyDataBatch extends Model
{
    protected $fillable = [
        'user_id',
        'summary',
    ];

    protected $casts = [
        'summary' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function records(): HasMany
    {
        return $this->hasMany(DummyDataRecord::class, 'batch_id');
    }

    public static function active(): ?self
    {
        return static::query()->latest('id')->first();
    }
}
