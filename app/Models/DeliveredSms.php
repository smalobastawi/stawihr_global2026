<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToCompany;

class DeliveredSms extends Model
{
    //use BelongsToCompany;

    use HasFactory;
    protected $table = 'delivered_sms';
    protected $fillable = ['message_id', 'message_status', 'API_response', 'message', 'mobile'];
}
