<?php
/*
 * Copyright (c) 2023/9/6 sw@stawitech
 */

namespace App\Lib\Enumerations;

class UserStatus
{
     public static $ACTIVE = 1;
     public static $INACTIVE = 2;
     public static $TERMINATE = 3;
     public static $PROBATION_PERIOD  = 0;
     public static $PERMANENT  = 1;

     public static function toArray(): array
     {
         return [
             self::$ACTIVE => 'Active',
             self::$INACTIVE => 'Inactive',
             self::$TERMINATE => 'Terminate',
             self::$PROBATION_PERIOD => 'Probation Period',
             self::$PERMANENT => 'Permanent'
         ];
     }

     public static function getName($value): string
    {
        switch ($value) {
            case self::$ACTIVE:
                return 'Active';
            case self::$INACTIVE:
                return 'Inactive';
            case self::$TERMINATE:
                return 'Terminate';
            case self::$PROBATION_PERIOD:
                return 'Probation Period';
            case self::$PERMANENT:
                return 'Permanent';
            default:
                return 'U';
        }
    }

}