<?php 

namespace App\Lib\Enumerations;

class DegreeClassification
{
    public const FIRST_CLASS = 1;
    public const SECOND_CLASS_UPPER_DIVISION = 2;
    public const SECOND_CLASS_LOWER_DIVISION = 3;
    public const THIRD_CLASS = 4;
    public const ORDINARY = 5; 
    
    public static function toArray(): array
    {
        return [
            self::FIRST_CLASS => 'First Class Honors(First)',
            self::SECOND_CLASS_UPPER_DIVISION => 'Second Class Honours (Upper Division, 2:1)',
            self::SECOND_CLASS_LOWER_DIVISION => 'Second Class Honours (Lower Division, 2:2)',
            self::THIRD_CLASS => 'Third Class Honours (Third)',
            self::ORDINARY => 'Pass/Ordinary Degree',
        ];
    }
    
    public static function getName($value): string
    {
        switch ($value) {
            case self::FIRST_CLASS:
                return 'First Class Honors(First)';
            case self::SECOND_CLASS_UPPER_DIVISION:
                return 'Second Class Honours (Upper Division, 2:1)';
            case self::SECOND_CLASS_LOWER_DIVISION:
                return 'Second Class Honours (Lower Division, 2:2)';
            case self::THIRD_CLASS:
                return 'Third Class Honours (Third)';
            case self::ORDINARY:
                return 'Pass/Ordinary Degree';
            default:
                return 'U';
        }
    }
}