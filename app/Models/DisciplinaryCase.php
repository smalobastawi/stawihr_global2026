<?php

namespace App\Models;

use App\Lib\Enumerations\DisciplinaryCaseStatus;
use App\Traits\WithBranchPermissions;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\SoftDeletes;

class DisciplinaryCase extends Model
{
    //use BelongsToCompany;

    use HasFactory, SoftDeletes;
    use WithBranchPermissions;

    protected $fillable = [
        'case_number',
        'description',
        'category_id',
        'employee_id',
        'assigned_officer',
        'location',
        'attachment',
        'date_of_incident',
        'date_of_report',
        'reporter_id',
        'status',
        'location_id',
    ];

    public function category()
    {
        return $this->belongsTo(DisciplinaryCategory::class, 'category_id');
    }
    public function branch()
    {
        return $this->belongsTo(Location::class, 'location_id');
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function reporter()
    {
        return $this->belongsTo(Employee::class, 'reporter_id');
    }
    public function assignedOfficer()
    {
        return $this->belongsTo(Employee::class, 'assigned_officer');
    }
    public function actions()
    {
        return $this->hasMany(DisciplinaryCaseAction::class, 'case_id');
    }
    public static  function closed()
    {
        return self::where('status', DisciplinaryCaseStatus::CLOSED);
    }
      public  function officeLocation()
    {
        return $this->belongsTo(Location::class, 'location_id');
    }
}
