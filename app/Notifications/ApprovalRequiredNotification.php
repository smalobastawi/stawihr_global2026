<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Database\Eloquent\Model;

class ApprovalRequiredNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $approvable;
    protected $modelType;
    protected $details;

    /**
     * Create a new notification instance.
     *
     * @param Model $approvable The model requiring approval
     * @param array $details Additional details for the notification
     */
    public function __construct(Model $approvable, array $details = [])
    {
        $this->approvable = $approvable;
        $this->modelType = $this->getModelType($approvable);
        $this->details = $details;
    }

    /**
     * Get the human-readable model type
     */
    protected function getModelType(Model $model): string
    {
        $class = get_class($model);
        $parts = explode('\\', $class);
        $name = array_pop($parts);
        
        // Convert camel case to words (e.g., "PayrollRecord" => "Payroll Record")
        return preg_replace('/(?<!\ )[A-Z]/', ' $0', $name);
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable): MailMessage
    {
        $url = $this->getActionUrl();
        $currentStep = $this->details['current_step'] ?? 'next step';
        $details = $this->getDetailsForMail();

        return (new MailMessage)
            ->subject("Approval Required: {$this->modelType}")
            ->view('emails.approval-required', [
                'notifiable' => $notifiable,
                'modelType' => $this->modelType,
                'currentStep' => $currentStep,
                'actionUrl' => $url,
                'details' => $details,
                'approvable' => $this->approvable
            ]);
    }

    /**
     * Get the action URL for the model
     */
    protected function getActionUrl(): string
    {
        // Convert model type to URL-friendly format (e.g., "PayrollRecord" => "payroll_record")
        $modelRoute = strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $this->modelType));
        
        return route("ess.approval.index");
    }

    /**
     * Get details for the email notification
     */
    protected function getDetailsForMail(): array
    {
        $defaultDetails = [
            //'ID' => $this->approvable->id,
            // 'Status' => $this->approvable->status ?? 'N/A',
            // 'Submitted By' => $this->details['submitter'] ?? 'System',
            // 'Submitted At' => $this->approvable->created_at->format('Y-m-d H:i'),
        ];

        // Model-specific details
        if (method_exists($this->approvable, 'getApprovalDetails')) {
            $defaultDetails = array_merge($defaultDetails, $this->approvable->getApprovalDetails());
        }

        return array_merge($defaultDetails, $this->details);
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray($notifiable): array
    {
        return [
            'message' => "A new record: {$this->modelType} requires your approval.",
            'model_type' => $this->modelType,
            'model_id' => $this->approvable->id,
            'details' => $this->getDetailsForMail(),
            'action_url' => $this->getActionUrl(),
        ];
    }

    /**
     * Get the database representation of the notification.
     */
    public function toDatabase($notifiable): array
    {
        return $this->toArray($notifiable);
    }
}