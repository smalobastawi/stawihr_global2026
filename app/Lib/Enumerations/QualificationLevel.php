<?php

namespace App\Lib\Enumerations;

class QualificationLevel
{
    public const NONE = 'None';
    public const HIGH_SCHOOL = 'High School';
    public const ASSOCIATE = 'Associate Degree';
    public const BACHELOR = "Bachelor's Degree";
    public const MASTER = "Master's Degree";
    public const PHD = 'PhD';

    /**
     * Ordered from lowest to highest for ATS comparison.
     */
    public static function ordered(): array
    {
        return [
            self::NONE,
            self::HIGH_SCHOOL,
            self::ASSOCIATE,
            self::BACHELOR,
            self::MASTER,
            self::PHD,
        ];
    }

    public static function options(): array
    {
        $options = [];
        foreach (self::ordered() as $level) {
            $options[$level] = $level;
        }

        return $options;
    }

    public static function rank(?string $qualification): int
    {
        if ($qualification === null || $qualification === '') {
            return -1;
        }

        $index = array_search($qualification, self::ordered(), true);

        return $index === false ? -1 : (int) $index;
    }

    /**
     * Return qualifications at or above the given minimum.
     */
    public static function atOrAbove(?string $minimum): array
    {
        $rank = self::rank($minimum);
        if ($rank < 0) {
            return self::ordered();
        }

        return array_values(array_slice(self::ordered(), $rank));
    }

    public static function meetsOrExceeds(?string $applicantQualification, ?string $requiredQualification): bool
    {
        if ($requiredQualification === null || $requiredQualification === '' || $requiredQualification === self::NONE) {
            return true;
        }

        return self::rank($applicantQualification) >= self::rank($requiredQualification);
    }
}
