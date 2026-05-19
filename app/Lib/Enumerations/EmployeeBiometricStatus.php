<?php
/*
 * Copyright (c) 2023/9/6 sw@stawitech
 */
namespace App\Lib\Enumerations;

class EmployeeBiometricStatus
{
    public const PENDING = 0;
    public const UPLOADED = 1;
    public const DEACTIVATED = 2;
    public const DELETED = 3;

    public static function toArray(): array
    {
        return [
            self::PENDING => 'Pending',
            self::UPLOADED => 'Uploaded',
            self::DEACTIVATED => 'Deactivated',
            self::DELETED => 'Deleted',
        ];
    }

    // Function to get the name based on id
    public static function getName($value): string
    {
        switch ($value) {
            case self::PENDING:
                return 'Pending';
            case self::UPLOADED:
                return 'Uploaded';
            case self::DEACTIVATED:
                return 'Deactivated';
            case self::DELETED:
                return 'Deleted';
            default:
                return 'Unknown';
        }
    }

    public static function getValue($name): int
    {
        switch ($name) {
            case 'Pending':
                return self::PENDING;
            case 'Uploaded':
                return self::UPLOADED;
            case 'Deactivated':
                return self::DEACTIVATED;
            case 'Deleted':
                return self::DELETED;
            default:
                return 0;
        }
    }
}