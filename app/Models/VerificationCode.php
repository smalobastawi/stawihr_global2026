<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VerificationCode extends Model
{


    use HasFactory;
    protected $table = 'verification_codes';
    protected $fillable = [
        'verification_code',
        'user_id',
        'is_password_change'
    ];
}