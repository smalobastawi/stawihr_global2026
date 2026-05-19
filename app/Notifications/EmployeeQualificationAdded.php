<?php

namespace App\Notifications;

use App\Models\Employee;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class EmployeeQualificationAdded extends Notification implements ShouldQueue
{
    use Queueable;
    protected $employee;
    protected $qualificationData;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Employee $employee, array $qualificationData)
    {
        //
        $this->employee = $employee;
        $this->qualificationData = $qualificationData;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via(object $notifiable): array
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
            'degree' => $this->qualificationData['degree'],
            'institute' => $this->qualificationData['institute'],
            'passing_year' => $this->qualificationData['passing_year'],
            'message' => 'New education qualification added',
        ];
    }
}
