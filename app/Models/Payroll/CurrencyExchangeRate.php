<?php

namespace App\Models\Payroll;

use App\Lib\Enumerations\ExchangeRateStatus;
use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class CurrencyExchangeRate extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'company_id',
        'from_currency',
        'to_currency',
        'rate',
        'effective_date',
        'payroll_period_id',
        'source',
        'status',
        'created_by',
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'rate' => 'decimal:8',
        'effective_date' => 'date',
        'approved_at' => 'datetime',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function payrollPeriod(): BelongsTo
    {
        return $this->belongsTo(PayrollPeriod::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function canBeEdited(): bool
    {
        return ExchangeRateStatus::isEditable($this->status);
    }

    public function isUsableForPayroll(): bool
    {
        return $this->status !== ExchangeRateStatus::LOCKED;
    }

    public function scopeForPayroll($query)
    {
        return $query->whereIn('status', ExchangeRateStatus::usableForPayroll());
    }

    public function scopeForPair($query, string $from, string $to)
    {
        return $query->where('from_currency', strtoupper($from))
            ->where('to_currency', strtoupper($to));
    }
}
