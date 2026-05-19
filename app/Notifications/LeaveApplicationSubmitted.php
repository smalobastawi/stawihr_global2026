<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use App\Models\LeaveApplication;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;

class LeaveApplicationSubmitted extends Notification implements ShouldQueue
{
    use Queueable;

    protected $leaveApplication;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(LeaveApplication $leaveApplication)
    {
        $this->leaveApplication = $leaveApplication;
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
            'message' => 'New leave application from ' . $this->leaveApplication->employee->full_name,
            'leave_id' => $this->leaveApplication->leave_application_id,
            'link' => route('requestedApplication.viewDetails', $this->leaveApplication->leave_application_id), // Added link to specific leave application
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
            'message' => 'New leave application from ' . $this->leaveApplication->employee->full_name,
            'link' => route('requestedApplication.viewDetails', $this->leaveApplication->leave_application_id), // Updated to specific leave application
            'application_id' => $this->leaveApplication->leave_application_id
        ]);
    }
}