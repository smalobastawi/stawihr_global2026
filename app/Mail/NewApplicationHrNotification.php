<?php

namespace App\Mail;

use App\Models\Job;
use App\Models\JobApplicant;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Contracts\Queue\ShouldQueue;

class NewApplicationHrNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $application;
    public $job;
    public $applicant;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(JobApplicant $application, Job $job)
    {
        //
        $this->application = $application;
        $this->job = $job;
        $this->applicant = $application; // Since you're storing name/email directly in JobApplicant
    }

    /**
     * Get the message envelope.
     *
     * @return \Illuminate\Mail\Mailables\Envelope
     */
    public function envelope()
    {
        return new Envelope(
            subject: 'New Job Application: ' . $this->job->job_title,
        );
    }

    /**
     * Get the message content definition.
     *
     * @return \Illuminate\Mail\Mailables\Content
     */
    public function content()
    {
        return new Content(
            view: 'emails.hr_new_application_notification',
            with: [
                'application' => $this->application,
                'job' => $this->job,
                'applicant' => $this->applicant,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array
     */
    public function attachments()
    {
        return [];
    }
}
