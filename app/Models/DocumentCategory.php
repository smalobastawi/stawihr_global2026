<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToCompany;

class DocumentCategory extends Model
{
    //use BelongsToCompany;

    use HasFactory;
    protected $table = 'document_categories';

    protected $fillable = ['name', 'description'];
    protected $hidden = ['created_at', 'updated_at'];
}
