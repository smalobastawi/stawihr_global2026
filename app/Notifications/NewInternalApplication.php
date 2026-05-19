<?php

namespace App\Notifications;

use App\Models\Employee;
use App\Models\JobApplicant;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class NewInternalApplication extends Notification
{
    use Queueable;

    public $application;
    public $applicant;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(JobApplicant $application)
    {
        //
        $this->application = $application;
        $this->applicant = Employee::find($application->employee_id);
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('New Internal Job Application: ' . $this->application->job->job_title)
            ->markdown('emails.internal_application_notification', [
                'application' => $this->application,
                'applicant' => $this->applicant,
                'hr' => $notifiable
            ]);
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
            //
            'application_id' => $this->application->id,
            'job_title' => $this->application->job->job_title,
            'applicant_name' => $this->applicant->fullname(),
            'applicant_id' => $this->applicant->employee_id,
            'applied_at' => now()->toDateTimeString(),
            // 'url' => route('hr.applications.show', $this->application->id)
        ];
    }
}
