<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class TrainingFacilitator extends Model
{
    //use BelongsToCompany;

    use HasFactory;

    // The table associated with the model (optional if it matches the pluralized form of the model name)
    protected $table = 'training_facilitators';

    // Fillable properties for mass assignment protection
    protected $fillable = [
        'name',
        'contact_email',
        'contact_phone',
        'type',
        'expertise',
        'notes',
    ];

    // Cast properties to specific data types
    protected $casts = [
        'status' => 'integer',  // For storing status as integer (1 or 2)
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the status attribute.
     * Converts the status integer to a human-readable string for use in views.
     * 
     * @return string
     */
    public function status(): Attribute
    {
        return Attribute::make(
            get: fn($value) => $value == 1 ? __('common.active') : __('common.inactive')
        );
    }

    /**
     * Accessor for facilitator's name.
     * If needed, we can modify the way names are formatted or returned.
     * 
     * @return string
     */
    public function getNameAttribute($value)
    {
        return ucfirst($value);
    }

    /**
     * A method for handling the facilitator's type.
     * We can add logic to handle different facilitator types (internal vs external).
     *
     * @return string
     */
    public function getTypeAttribute($value)
    {
        return ucfirst($value);
    }

    /**
     * Example of a method to get the full contact information.
     * 
     * @return string
     */
    public function getContactInformation()
    {
        return $this->contact_email ? $this->contact_email : $this->contact_phone;
    }
}
