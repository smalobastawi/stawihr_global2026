<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use App\Models\Notice;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;

class NoticeNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $notice;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Notice $notice)
    {
        $this->notice = $notice;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database', 'broadcast'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'message' => 'New notice: ' . $this->notice->title,
            'notice_id' => $this->notice->notice_id,
            'link' => route('notice.show', $this->notice->notice_id),
        ];
    }

    /**
     * Get the broadcastable representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return BroadcastMessage
     */
    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage([
            'message' => 'New notice: ' . $this->notice->title,
            'link' => route('notice.show', $this->notice->notice_id),
            'notice_id' => $this->notice->notice_id
        ]);
    }
}
