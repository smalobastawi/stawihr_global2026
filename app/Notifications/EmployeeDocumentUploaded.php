<?php

namespace App\Notifications;

use App\Models\Employee;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class EmployeeDocumentUploaded extends Notification implements ShouldQueue
{
    use Queueable;

    protected $employee;
    protected $documentNames;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Employee $employee, array $documentNames)
    {
        //
        $this->employee = $employee;
        $this->documentNames = $documentNames;
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
    public function toArray($notifiable): array
    {
        $documentsList = "\n- " . implode("\n- ", $this->documentNames);

        return [
            'employee_id' => $this->employee->employee_id,
            'employee_name' => $this->employee->full_name,
            'documents' => $this->documentNames,
            'message' => "New documents uploaded: {$documentsList}",
            'link' => route('employee.show', $this->employee->employee_id), // Added link to specific leave application
        ];
    }
}
