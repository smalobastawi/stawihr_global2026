<?php

namespace App\Support;

use App\Lib\Enumerations\QualificationLevel;
use App\Models\Job;
use App\Models\JobApplicant;

class AtsMatcher
{
    /**
     * Normalize a comma/newline-separated skill list into unique lowercase tokens.
     */
    public static function parseSkills(?string $skills): array
    {
        if ($skills === null || trim($skills) === '') {
            return [];
        }

        $parts = preg_split('/[,;\n]+/', $skills) ?: [];
        $normalized = [];

        foreach ($parts as $part) {
            $skill = strtolower(trim($part));
            if ($skill !== '') {
                $normalized[$skill] = $skill;
            }
        }

        return array_values($normalized);
    }

    public static function skillsOverlap(array $required, array $applicant): array
    {
        if (empty($required)) {
            return [];
        }

        return array_values(array_intersect($required, $applicant));
    }

    /**
     * Score applicant against job ATS criteria (0-100).
     */
    public static function score(Job $job, JobApplicant $applicant): int
    {
        $weights = [];
        $earned = 0.0;

        if ($job->min_years_experience !== null && $job->min_years_experience !== '') {
            $weights['experience'] = 30;
            $years = (int) ($applicant->years_of_experience ?? 0);
            $min = (int) $job->min_years_experience;
            if ($years >= $min) {
                $earned += 30;
            } elseif ($min > 0) {
                $earned += max(0, 30 * ($years / $min));
            }
        }

        if (!empty($job->required_qualification) && $job->required_qualification !== QualificationLevel::NONE) {
            $weights['qualification'] = 30;
            if (QualificationLevel::meetsOrExceeds($applicant->highest_qualification, $job->required_qualification)) {
                $earned += 30;
            } else {
                $requiredRank = QualificationLevel::rank($job->required_qualification);
                $applicantRank = max(0, QualificationLevel::rank($applicant->highest_qualification));
                if ($requiredRank > 0) {
                    $earned += 30 * min(1, $applicantRank / $requiredRank);
                }
            }
        }

        $requiredSkills = self::parseSkills($job->required_skills);
        if (!empty($requiredSkills)) {
            $weights['skills'] = 40;
            $applicantSkills = self::parseSkills($applicant->skills);
            $matched = self::skillsOverlap($requiredSkills, $applicantSkills);
            $earned += 40 * (count($matched) / count($requiredSkills));
        }

        $totalWeight = array_sum($weights);
        if ($totalWeight === 0) {
            return 0;
        }

        return (int) round(($earned / $totalWeight) * 100);
    }

    public static function meetsCriteria(Job $job, JobApplicant $applicant): bool
    {
        if ($job->min_years_experience !== null && $job->min_years_experience !== '') {
            if ((int) ($applicant->years_of_experience ?? 0) < (int) $job->min_years_experience) {
                return false;
            }
        }

        if (!QualificationLevel::meetsOrExceeds($applicant->highest_qualification, $job->required_qualification)) {
            return false;
        }

        $requiredSkills = self::parseSkills($job->required_skills);
        if (!empty($requiredSkills)) {
            $applicantSkills = self::parseSkills($applicant->skills);
            if (count(self::skillsOverlap($requiredSkills, $applicantSkills)) < count($requiredSkills)) {
                return false;
            }
        }

        return true;
    }
}
