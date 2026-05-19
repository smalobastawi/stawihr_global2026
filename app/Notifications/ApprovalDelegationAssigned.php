<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\ApprovalDelegation;
use App\Models\User;

class ApprovalDelegationAssigned extends Notification implements ShouldQueue
{
    use Queueable;

    protected $delegation;
    protected $delegator;
    protected $delegate;

    /**
     * Create a new notification instance.
     *
     * @param ApprovalDelegation $delegation The delegation record
     * @param User $delegator The user who created the delegation
     * @param User $delegate The user who received the delegation
     */
    public function __construct(ApprovalDelegation $delegation, User $delegator, User $delegate)
    {
        $this->delegation = $delegation;
        $this->delegator = $delegator;
        $this->delegate = $delegate;
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
        $delegationDetails = $this->getDelegationDetails();
        $scopeDescription = $this->getScopeDescription();
        $periodDescription = $this->getPeriodDescription();

        return (new MailMessage)
            ->subject('Approval Delegation Assigned - Action Required')
            ->view('emails.approval-delegation-assigned', [
                'notifiable' => $notifiable,
                'delegation' => $this->delegation,
                'delegator' => $this->delegator,
                'delegate' => $this->delegate,
                'delegationDetails' => $delegationDetails,
                'scopeDescription' => $scopeDescription,
                'periodDescription' => $periodDescription,
                'actionUrl' => $this->getActionUrl()
            ]);
    }

    /**
     * Get delegation details for the email
     */
    protected function getDelegationDetails(): array
    {
        $details = [
            'Delegator' => $this->delegator->name . ' (' . $this->delegator->email . ')',
            'Delegated To' => $this->delegate->name . ' (' . $this->delegate->email . ')',
            'Scope' => $this->getScopeDescription(),
            'Period' => $this->getPeriodDescription(),
            'Can View Submissions' => $this->delegation->include_submissions ? 'Yes' : 'No',
            'Status' => $this->delegation->isValid() ? 'Active' : 'Inactive',
            'Created On' => $this->delegation->created_at->format('Y-m-d H:i:s')
        ];

        if ($this->delegation->notes) {
            $details['Notes'] = $this->delegation->notes;
        }

        return $details;
    }

    /**
     * Get human-readable scope description
     */
    protected function getScopeDescription(): string
    {
        switch ($this->delegation->delegation_type) {
            case 'all':
                return 'All Approval Types';
            case 'specific_model':
                $modelName = class_basename($this->delegation->model_type);
                return "Specific Model: {$modelName}";
            case 'specific_workflow':
                return "Specific Workflow: " . ($this->delegation->workflow->name ?? 'Unknown Workflow');
            default:
                return 'Unknown Scope';
        }
    }

    /**
     * Get human-readable period description
     */
    protected function getPeriodDescription(): string
    {
        $startDate = $this->delegation->start_date->format('Y-m-d');
        $endDate = $this->delegation->end_date?->format('Y-m-d');

        if ($endDate) {
            return "From {$startDate} to {$endDate}";
        }

        return "From {$startDate} (Indefinite)";
    }

    /**
     * Get the action URL for the notification
     */
    protected function getActionUrl(): string
    {
        return route('ess.approval.index');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray($notifiable): array
    {
        return [
            'message' => "You have been assigned as an approval delegate by {$this->delegator->name}",
            'delegation_id' => $this->delegation->id,
            'delegator_name' => $this->delegator->name,
            'delegator_email' => $this->delegator->email,
            'scope' => $this->getScopeDescription(),
            'period' => $this->getPeriodDescription(),
            'action_url' => $this->getActionUrl(),
            'details' => $this->getDelegationDetails(),
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
