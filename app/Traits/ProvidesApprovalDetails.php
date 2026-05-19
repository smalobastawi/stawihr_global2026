<?php

namespace App\Traits;

trait ProvidesApprovalDetails
{
    /**
     * Get details for approval notifications
     */
    public function getApprovalDetails(): array
    {
        // Default implementation - models can override this
        return [
            'Type' => class_basename($this),
            'Created At' => $this->created_at?->format('Y-m-d H:i') ?? 'N/A',
        ];
    }
}