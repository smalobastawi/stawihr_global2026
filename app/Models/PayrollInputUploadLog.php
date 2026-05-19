<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PayrollInputUploadLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'payroll_period_id',
        'uploaded_by',
        'uploaded_at',
        'file_name',
        'file_path',
        'details',
    ];

    protected $casts = [
        'uploaded_at' => 'datetime',
        'details' => 'array',
    ];

    public function payrollPeriod()
    {
        return $this->belongsTo(PayrollPeriod::class);
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
