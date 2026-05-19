<?php

namespace App\Notifications;

use App\Models\Employee;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;

class EmployeeProfileUpdated extends Notification implements ShouldQueue
{
    use Queueable;

    protected $employee;
    protected $updater;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Employee $employee, Employee $updater)
    {
        //
        $this->employee = $employee;
        $this->updater = $updater;
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
            'updater_id' => $this->updater->employee_id,
            'updater_name' => $this->updater->full_name,
            'message' => 'Employee profile has been updated.'
        ];
    }
}
