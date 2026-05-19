<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\SoftDeletes;

class DisciplinaryCategory extends Model
{
    //use BelongsToCompany;

    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'status',
    ];

    public function disciplinaryCases()
    {
        return $this->hasMany(DisciplinaryCase::class, 'category_id');
    }
}
