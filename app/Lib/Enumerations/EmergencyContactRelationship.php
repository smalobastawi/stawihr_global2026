<?php 

/*
 * Copyright (c) 2023/9/6 sw@stawitech
 */
namespace App\Lib\Enumerations;

class EmergencyContactRelationship 
{
    public const SPOUSE = 1;
    public const PARENT = 2;
    public const SIBLING = 3;
    public const GUARDIAN = 4;
    public const FRIEND = 5;
    public const RELATIVE = 6;
    public const OTHERS = 7;

    public static function toArray(): array
    {
        return [
            self::SPOUSE => 'Spouse',
            self::PARENT => 'Parent',
            self::SIBLING => 'Sibling',
            self::GUARDIAN => 'Guardian',
            self::FRIEND => 'Friend',
            self::RELATIVE => 'Relative',
            self::OTHERS => 'Others',
        ];
    }

    // Function to get the name based on id
    public static function getName($value): string
    {
        switch ($value) {
            case self::SPOUSE:
                return 'Spouse';
            case self::PARENT:
                return 'Parent';
            case self::SIBLING:
                return 'Sibling';
            case self::GUARDIAN:
                return 'Guardian';
            case self::FRIEND:
                return 'Friend';
            case self::RELATIVE:
                return 'Relative';
            case self::OTHERS:
                return 'Others';
            default:
                return 'Unknown';
        }
    }

    public static function getValue($name): int
    {
        switch ($name) {
            case 'Spouse':
                return self::SPOUSE;
            case 'Parent':
                return self::PARENT;
            case 'Sibling':
                return self::SIBLING;
            case 'Guardian':
                return self::GUARDIAN;
            case 'Friend':
                return self::FRIEND;
            case 'Relative':
                return self::RELATIVE;  
            case 'Others':
                return self::OTHERS;
            default:
                return 0;
        }
    }
}