<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ethnicity extends Model
{
    protected $fillable = ['name'];

    public function employees()
    {
        return $this->hasMany(Employee::class, 'ethnicity');
    }

    public static function getEthnicitiesArray(): array
    {
        return self::pluck('name', 'id')->toArray();
    }

    public static function getName($id): string
    {
        $ethnicity = self::find($id);
        return $ethnicity ? $ethnicity->name : 'Other';
    }

    public static function getValue($name)
    {
        $ethnicity = self::where('name', ucwords(strtolower($name)))->first();
        return $ethnicity ? $ethnicity->id : null;
    }
}
