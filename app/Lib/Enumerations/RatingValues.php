<?php

/*
 * Copyright (c) 2023/9/6 sw@stawitech
 */
namespace App\Lib\Enumerations;

enum RatingValues
{
    case EXCEEDS_EXPECTATION;
    case MEETS_EXPECTATION;
    case NEEDS_SUPPORT;
    case DOES_NOT_MEET_EXPECTATION;

    public function value(): float
    {
        return match ($this) {
            self::EXCEEDS_EXPECTATION => 10.0,
            self::MEETS_EXPECTATION => 7.5,
            self::NEEDS_SUPPORT => 5.0,
            self::DOES_NOT_MEET_EXPECTATION => 2.5,
        };
    }

    public function label(): string
    {
        return match ($this) {
            self::EXCEEDS_EXPECTATION => 'Exceeds expectation',
            self::MEETS_EXPECTATION => 'Meets expectations',
            self::NEEDS_SUPPORT => 'Needs support',
            self::DOES_NOT_MEET_EXPECTATION => 'Does not meet expectation',
        };
    }

    public static function fromLabel(string $label): ?self
    {
        return match ($label) {
            'Exceeds expectation' => self::EXCEEDS_EXPECTATION,
            'Meets expectations' => self::MEETS_EXPECTATION,
            'Needs support' => self::NEEDS_SUPPORT,
            'Does not meet expectation' => self::DOES_NOT_MEET_EXPECTATION,
            default => null,
        };
    }
}

