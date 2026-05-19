<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToCompany;

class ApprovalSettingApprover extends Model
{
    //use BelongsToCompany;

    use HasFactory;

    /**
     * Summary of fillable
     * @var array
     * 
     * 
     */
    protected $fillable = ['aproval_setting_id', 'user_id', 'module_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function module()
    {

        return $this->belongsTo(Module::class);
    }

    public function approvalSetting()
    {
        return $this->belongsTo(ApprovalSettings::class);
    }
}
