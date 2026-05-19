<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Collection;

class BatchApprovalRequiredNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $models;
    protected $batchId;
    protected $additionalData;

    /**
     * Create a new notification instance.
     *
     * @param Collection|array $models
     * @param string $batchId
     * @param array $additionalData
     */
    public function __construct($models, $batchId, $additionalData = [])
    {
        $this->models = collect($models);
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
        $count = $this->models->count();
        $modelType = class_basename($this->models->first());
        $submitter = $this->additionalData['submitter'] ?? 'System';
        $currentStep = $this->additionalData['current_step'] ?? 'Unknown Step';

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
            ->subject("Batch Approval Required - {$count} {$modelType} Items")
            ->view('emails.batch-approval-required', [
                'notifiable' => $notifiable,
                'count' => $count,
                'modelType' => $modelType,
                'batchId' => $this->batchId,
                'submitter' => $submitter,
                'currentStep' => $currentStep,
                'items' => $items,
                'additionalItemsCount' => $additionalItemsCount,
                'actionUrl' => route('ess.approval.index')
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
        $count = $this->models->count();
        $modelType = class_basename($this->models->first());

        return [
            'type' => 'batch_approval_required',
            'batch_id' => $this->batchId,
            'model_type' => $modelType,
            'count' => $count,
            'submitter' => $this->additionalData['submitter'] ?? 'System',
            'current_step' => $this->additionalData['current_step'] ?? 'Unknown Step',
            'message' => "Batch approval required for {$count} {$modelType} items",
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