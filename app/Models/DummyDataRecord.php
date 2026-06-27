<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DummyDataRecord extends Model
{
    protected $fillable = [
        'batch_id',
        'table_name',
        'record_id',
    ];

    public function batch(): BelongsTo
    {
        return $this->belongsTo(DummyDataBatch::class, 'batch_id');
    }
}
