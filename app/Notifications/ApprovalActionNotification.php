<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Database\Eloquent\Model;

class ApprovalActionNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $approvable;
    protected $action;
    protected $comments;

    public function __construct(Model $approvable, string $action, ?string $comments = null)
    {
        $this->approvable = $approvable;
        $this->action = $action;
        $this->comments = $comments;
    }

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        $modelType = $this->getModelType($this->approvable);
        $humanReadableModelType = $this->getHumanReadableModelType($this->approvable);

        return (new MailMessage)
            ->subject("Your submission has been {$this->action}")
            ->view('emails.approval-action', [
                'notifiable' => $notifiable,
                'modelType' => $modelType,
                'humanReadableModelType' => $humanReadableModelType,
                'action' => $this->action,
                'comments' => $this->comments,
                'actionUrl' => $this->getActionUrl(),
                'approvable' => $this->approvable
            ]);
    }

    public function toArray($notifiable): array
    {
        return [
            'message' => "Your submission has been {$this->action}",
            'action' => $this->action,
            'comments' => $this->comments,
            'action_url' => $this->getActionUrl(),
        ];
    }

    protected function getModelType(Model $model): string
    {
        $class = get_class($model);
        $parts = explode('\\', $class);
        return array_pop($parts);
    }

    protected function getHumanReadableModelType(Model $model): string
    {
        $modelType = $this->getModelType($model);
        return preg_replace('/(?<!\ )[A-Z]/', ' $0', $modelType);
    }

    protected function getActionUrl(): string
    {
        return route("ess.approval.index");
    }
}