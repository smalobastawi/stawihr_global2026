<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToCompany;

class FinancialYear extends Model
{
    // //use BelongsToCompany;

    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'status',
        'created_at',
        'updated_at',
        'deleted_at',
        'created_by',
        'updated_by',
        'deleted_by',
        'uuid',
        'start_date',
        'end_date',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
        'start_date',
        'end_date',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($financialYear) {

            // If the new financial year is active, deactivate all others
            if ($financialYear->status == 1) {
                FinancialYear::where('status', 1)->update(['status' => 0]);
            }
        });
    }

    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    public function getFormattedDateRangeAttribute()
    {
        $start = \Carbon\Carbon::parse($this->start_date)->format('M d, Y');
        $end = \Carbon\Carbon::parse($this->end_date)->format('M d, Y');
        return "$start - $end";
    }
}
