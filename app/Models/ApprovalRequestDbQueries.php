<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToCompany;

class ApprovalRequestDbQueries extends Model
{
   //use BelongsToCompany;

   use HasFactory;

   /**
    *   'approval_request_id' => $approval_request->id,

                    'query' => $query['query'],

                    'bindings' => json_encode($query['bindings']),

                    'execution_time' => $query['time'],
    * 
    */

   protected $fillable = [
      'query',
      'bindings',
      'execution_time',
      'approval_request_id',
      'changes'
   ];
}
