<?php
/*
 * Copyright (c) 2023/9/6 sw@stawitech
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class EmployeeDocuments extends Model
{
    //use BelongsToCompany;

    use HasFactory;

    protected $fillable = [
        'document_name',
        'employee_id',
        'national_id',
        'date_uploaded',
        'document_type',
        'document_link',
        'uploaded_by',
        'created_by',
        'updated_by',
        'uuid',
        'location_id'
    ];
    protected $dates = ['date_uploaded'];
}
