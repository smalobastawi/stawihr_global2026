<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use App\Models\LeaveApplication;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;

class NewSurveyNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $survey;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(LeaveApplication $survey)
    {
        $this->survey = $survey;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database', 'broadcast']; // Removed 'mail' from the channels
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
            'message' => 'New Survey added',
           // 'leave_id' => $this->leaveApplication->leave_application_id,
            'link' => route('requestedApplication.viewDetails', $this->survey->form_url), // Added link to specific leave application
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
            'message' => 'New survey added',
            'link' => route( $this->survey->form_url), // Updated to specific leave application
           // 'application_id' => $this->survey->leave_application_id
        ]);
    }
}