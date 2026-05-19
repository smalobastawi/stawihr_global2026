<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CompanyPermissions extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'company_permissions';
    protected $fillable = ['permission_id', 'user_id', 'company_id', 'permission_name'];

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'company_permissions', 'company_id', 'user_id');
    }

    public function permission()
    {
        return $this->belongsTo(Permissions::class);
    }
}