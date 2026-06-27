<?php

namespace App\Services\Pdp;

use App\Models\Pdp\PdpPlan;
use App\Models\PrintHeadSetting;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Str;

class PdpPlanPdfService
{
    public function download(PdpPlan $plan)
    {
        $plan->load([
            'employee.department',
            'employee.designation',
            'supervisor',
            'department',
            'designation',
            'goals',
            'progressEntries',
        ]);

        $printHead = PrintHeadSetting::first();

        $employee = $plan->employee;
        $supervisor = $plan->supervisor;

        $signatures = [
            'employee' => [
                'signature' => $plan->employee_acknowledged && $employee
                    ? $employee->full_name
                    : '',
                'date' => $plan->employee_ack_date?->format('d M Y'),
                'comments' => $plan->employee_comments ?? '',
            ],
            'supervisor' => [
                'signature' => $plan->supervisor_approved && $supervisor
                    ? $supervisor->full_name
                    : '',
                'date' => $plan->supervisor_approve_date?->format('d M Y'),
                'comments' => $plan->supervisor_comments
                    ?? $this->aggregateSupervisorComments($plan),
            ],
            'hr' => [
                'signature' => $plan->hr_reviewed ? 'HR Review Complete' : '',
                'date' => $plan->hr_review_date?->format('d M Y'),
                'comments' => $plan->hr_comments ?? $plan->overall_summary ?? '',
            ],
        ];

        $pdf = Pdf::loadView('admin.pdp.plan.pdf', [
            'plan' => $plan,
            'printHead' => $printHead,
            'signatures' => $signatures,
        ]);

        $pdf->setPaper('A4', 'portrait');

        $filename = sprintf(
            'development-plan-%s-%s.pdf',
            Str::slug($plan->plan_title ?: 'plan'),
            $plan->plan_year
        );

        return $pdf->download($filename);
    }

    private function aggregateSupervisorComments(PdpPlan $plan): string
    {
        return $plan->progressEntries
            ->pluck('supervisor_comments')
            ->filter()
            ->unique()
            ->implode("\n");
    }
}
