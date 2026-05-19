<?php

namespace App\Notifications;

use App\Models\Employee;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class EmployeeExperienceUpdated extends Notification implements ShouldQueue
{
    use Queueable;

    protected $employee;
    protected $experienceData;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Employee $employee, array $experienceData)
    {
        //
        $this->employee = $employee;
        $this->experienceData = $experienceData;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable): array
    {
        return ['database'];
    }

    
    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
            'employee_id' => $this->employee->employee_id,
            'employee_name' => $this->employee->full_name,
            'organization_name' => $this->experienceData['organization_name'],
            'designation' => $this->experienceData['designation'],
            'message' => 'Professional experience added/updated',
        ];
    }
}
