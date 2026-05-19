<?php

namespace App\Models;

use App\Http\Controllers\ApprovalRecordsController;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToCompany;

class ApprovalSettings extends Model
{
    //use BelongsToCompany;

    use HasFactory;
    protected $table = 'approval_settings';
    protected $fillable = ['model_type', 'approvers_list', 'approver_numbers'];

    public function approvers()
    {
        return $this->hasMany(ApprovalSettingApprover::class, 'approval_setting_id');
    }

    public function module()
    {
        return $this->belongsTo(Module::class, 'module_id');
    }
}
