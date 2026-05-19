<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApprovalQueryLog extends Model
{
    use HasFactory;
    protected $fillable = ['query', 'bindings', 'execution_time','approval_record_id'];
}

