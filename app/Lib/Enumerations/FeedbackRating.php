<?php
/*
 * Copyright (c) 2023/9/6 sw@stawitech
 */
namespace App\Lib\Enumerations;

class FeedbackRating
{
    public const POSITIVE = 5;
    public const NEGATIVE = 1;
    public const NEUTRAL = 3;
   

    public static function toArray(): array
    {
        return [
            self::POSITIVE => 'Positive',
            self::NEGATIVE => 'Negative',
            self::NEUTRAL => 'Neutral',
            
        ];
    }

    // Function to get the name based on id
    public static function getName($value): string
    {
        switch ($value) {
            case self::POSITIVE:
                return 'Positive';
            case self::NEGATIVE:
                return 'Negative';
            case self::NEUTRAL:
                return 'Neutral';
            
            default:
                return 'Unknown';
        }
    }

    public static function getValue($name): int
    {
        switch ($name) {
            case 'Positive':
                return self::POSITIVE;
            case 'Negative':
                return self::NEGATIVE;
            case 'Neutral':
                return self::NEUTRAL;
            
            default:
                return 0;
        }
    }
}
