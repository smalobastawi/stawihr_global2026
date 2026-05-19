<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Collection;

class BatchApprovalActionNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $models;
    protected $action;
    protected $batchId;
    protected $additionalData;

    /**
     * Create a new notification instance.
     *
     * @param Collection|array $models
     * @param string $action
     * @param string $batchId
     * @param array $additionalData
     */
    public function __construct($models, $action, $batchId, $additionalData = [])
    {
        $this->models = collect($models);
        $this->action = $action;
        $this->batchId = $batchId;
        $this->additionalData = $additionalData;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param mixed $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $count = $this->additionalData['count'] ?? $this->models->count();
        $modelType = class_basename($this->models->first());
        $actionedBy = $this->additionalData['actioned_by'] ?? 'System';
        $comments = $this->additionalData['comments'] ?? '';

        // Prepare items data for the blade template
        $items = $this->models->take(5)->map(function ($model) {
            return [
                'title' => method_exists($model, 'getApprovalTitle') 
                    ? $model->getApprovalTitle() 
                    : class_basename($model) . " #{$model->id}",
            ];
        })->toArray();

        $additionalItemsCount = $count > 5 ? ($count - 5) : 0;

        return (new MailMessage)
            ->subject("Batch " . ucfirst($this->action) . " - {$count} {$modelType} Items")
            ->view('emails.batch-approval-action', [
                'notifiable' => $notifiable,
                'count' => $count,
                'modelType' => $modelType,
                'action' => $this->action,
                'actionText' => ucfirst($this->action),
                'batchId' => $this->batchId,
                'actionedBy' => $actionedBy,
                'comments' => $comments,
                'items' => $items,
                'additionalItemsCount' => $additionalItemsCount,
                'actionUrl' => route('ess.approval.index'),
                'statusClass' => $this->action === 'approved' ? 'success' : 'danger'
            ]);
    }

    /**
     * Get the array representation of the notification.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        $count = $this->additionalData['count'] ?? $this->models->count();
        $modelType = class_basename($this->models->first());

        return [
            'type' => 'batch_approval_action',
            'batch_id' => $this->batchId,
            'action' => $this->action,
            'model_type' => $modelType,
            'count' => $count,
            'actioned_by' => $this->additionalData['actioned_by'] ?? 'System',
            'comments' => $this->additionalData['comments'] ?? '',
            'message' => "Batch {$this->action}: {$count} {$modelType} items have been {$this->action}",
            'action_url' => url('/admin/ess/approvals'),
            'items' => $this->models->map(function ($model) {
                return [
                    'id' => $model->id,
                    'title' => method_exists($model, 'getApprovalTitle') 
                        ? $model->getApprovalTitle() 
                        : class_basename($model) . " #{$model->id}",
                    'type' => class_basename($model)
                ];
            })->toArray()
        ];
    }
}