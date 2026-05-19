<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToCompany;

class PendingPasswordChange extends Model
{
    //use BelongsToCompany;

    use HasFactory;
    protected $table = 'pending_change_passwords';
    protected $fillable = [
        'user_id',
        'password',
        'password_changed_at'
    ];
}
