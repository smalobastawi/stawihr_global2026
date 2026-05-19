<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToCompany;

class TrainingsView extends Model
{
    //use BelongsToCompany;

    use HasFactory;

    protected $table = 'training_view';
}
