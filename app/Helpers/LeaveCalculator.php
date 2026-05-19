<?php

namespace App\Helpers;

use Carbon\Carbon;
use App\Models\HolidayDetails;
use App\Models\LeaveGroupSetting;

trait LeaveCalculator
{
 
    private function calculateLeaveDaysInPeriod($employee, $leaveStartDate, $leaveEndDate, $leaveTypeId, $fiscalYearStart, $fiscalYearEnd)
    {
        $leaveStart = Carbon::parse($leaveStartDate);
        $leaveEnd = Carbon::parse($leaveEndDate);
        $fiscalStart = Carbon::parse($fiscalYearStart);
        $fiscalEnd = Carbon::parse($fiscalYearEnd);

        // Determine the overlap period between leave and fiscal year
        $overlapStart = $leaveStart->greaterThan($fiscalStart) ? $leaveStart : $fiscalStart;
        $overlapEnd = $leaveEnd->lessThan($fiscalEnd) ? $leaveEnd : $fiscalEnd;

        // If no overlap, return 0
        if ($overlapStart->greaterThan($overlapEnd)) {
            return 0;
        }

        // Get leave group settings to determine how to count days
        $leaveGroup = $employee->leaveGroup;
        if (!$leaveGroup) {
            return 0;
        }

        $settings = LeaveGroupSetting::where('leave_group_id', $leaveGroup->id)
            ->where('leave_type_id', $leaveTypeId)
            ->first();

        if (!$settings) {
            return 0;
        }

        // If calendar_days, count all days in the overlap period
        if ($settings->applicable_on === 'calendar_days') {
            return $overlapStart->diffInDays($overlapEnd) + 1;
        }

        // For working_days, exclude weekends and holidays
        $affectingHolidays = $leaveGroup->publicHolidays->pluck('holiday_id')->toArray();

        // Fetch and expand holiday date ranges into individual dates
        $holidays = HolidayDetails::whereIn('holiday_id', $affectingHolidays)
            ->where('status', 1)
            ->get()
            ->flatMap(function ($holiday) {
                return Carbon::parse($holiday->from_date)->toPeriod($holiday->to_date)->toArray();
            })
            ->map(fn($date) => $date->format('Y-m-d'))
            ->toArray();

        // Get weekly holidays (weekends)
        $weekendDays = $leaveGroup->weeklyHolidays->pluck('day_name')->map(function ($day) {
            return strtolower($day);
        })->toArray();

        // Count individual days within the fiscal year overlap period
        $leaveDays = 0;
        for ($date = $overlapStart->copy(); $date->lte($overlapEnd); $date->addDay()) {
            $dayName = strtolower($date->format('l'));

            if (!in_array($date->format('Y-m-d'), $holidays) && !in_array($dayName, $weekendDays)) {
                $leaveDays++;
            }
        }

        return $leaveDays;
    }

    public function calculateHolidayAdjustment($employee, $leaveStartDate, $leaveEndDate, $leaveTypeId)
    {
        $leaveStart = Carbon::parse($leaveStartDate);
        $leaveEnd = Carbon::parse($leaveEndDate);

        $leaveGroup = $employee->leaveGroup;
        if (!$leaveGroup) {
            return ['holiday_count' => 0, 'applicable_on' => null];
        }

        $settings = LeaveGroupSetting::where('leave_group_id', $leaveGroup->id)
            ->where('leave_type_id', $leaveTypeId)
            ->first();

        $holidayCount = 0;
        if ($settings && $settings->applicable_on === 'working_days') {
            $affectingHolidays = $leaveGroup->publicHolidays->pluck('holiday_id')->toArray();

            $holidayDates = HolidayDetails::whereIn('holiday_id', $affectingHolidays)
                ->where('status', 1)
                ->get()
                ->flatMap(function ($holiday) {
                    return Carbon::parse($holiday->from_date)->toPeriod($holiday->to_date)->toArray();
                })
                ->map(fn($date) => $date->format('Y-m-d'))
                ->toArray();

            for ($date = $leaveStart->copy(); $date->lte($leaveEnd); $date->addDay()) {
                if (in_array($date->format('Y-m-d'), $holidayDates)) {
                    $holidayCount++;
                }
            }
        }

        return [
            'holiday_count' => $holidayCount,
            'applicable_on' => $settings->applicable_on ?? null
        ];
    }
}