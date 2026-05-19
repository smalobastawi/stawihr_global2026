<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Training extends Model
{
    //use BelongsToCompany;

    use HasFactory, SoftDeletes;

    protected $fillable = [
        'training_type_id',
        'facilitator_id',
        'subject',
        'attendance_type',
        'attendance_link',
        'attendance_location',
        'start_date',
        'start_time',
        'end_date',
        'end_time',
        'description',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'start_date' => 'date:Y-m-d',
        'end_date' => 'date:Y-m-d',
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
    ];

    /**
     * Get the training type associated with the training.
     */
    public function trainingType()
    {
        return $this->belongsTo(TrainingType::class, 'training_type_id', 'training_type_id');
    }

    /**
     * Get the facilitator associated with the training.
     */
    public function facilitator()
    {
        return $this->belongsTo(TrainingFacilitator::class, 'facilitator_id');
    }

    /**
     * Get the user who created the training.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by', 'user_id');
    }

    /**
     * Get the user who updated the training.
     */
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by', 'user_id');
    }

    /**
     * Get employees associated with the training.
     */
    public function attendances()
    {
        return $this->hasMany(TrainingAttendant::class,   'training_id');
    }

    public function invites()
    {
        return $this->hasMany(TrainingInvitee::class,   'training_id');
    }


    public function getGoogleCalendarLinkAttribute()
    {
        $start = Carbon::parse($this->start_date->format('Y-m-d') . ' ' . $this->start_time);
        $end = Carbon::parse($this->end_date->format('Y-m-d') . ' ' . $this->end_time);

        return "https://www.google.com/calendar/render?action=TEMPLATE" .
            "&text=" . urlencode($this->subject) .
            "&dates=" . $start->format('Ymd\THis') .
            "/" . $end->format('Ymd\THis') .
            "&details=" . urlencode($this->description) .
            "&location=" . urlencode($this->location ?? 'Online');
    }

    public function getOutlookCalendarLinkAttribute()
    {
        $start = Carbon::parse($this->start_date->format('Y-m-d') . ' ' . $this->start_time);
        $end = Carbon::parse($this->end_date->format('Y-m-d') . ' ' . $this->end_time);

        return "https://outlook.live.com/calendar/0/deeplink/compose?" .
            "subject=" . urlencode($this->subject) .
            "&body=" . urlencode($this->description) .
            "&startdt=" . $start->format('Y-m-d\TH:i:s') .
            "&enddt=" . $end->format('Y-m-d\TH:i:s') .
            "&location=" . urlencode($this->location ?? 'Online');
    }
}
