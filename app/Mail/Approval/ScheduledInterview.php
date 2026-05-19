<?php

namespace App\Mail\Approval;

use App\Models\Job;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ScheduledInterview extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public $subject = "Interview Invitation";
    public $interviewLocation='Kibera Drive, Gatwekera Village, Kibera';
    public $name;
    public $email;
    public $jobTitle;
    public $interviewDate;
    public $interviewTime;

    public function __construct($name, $email, $jobTitle,$interviewDate,$interviewTime)
    {
        $this->name = $name;
        $this->email = $email;
        $this->jobTitle = $jobTitle;
        $this->interviewDate = $interviewDate;
        $this->interviewTime = $interviewTime;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Call For Interviews",
            from: 'noreply@yourdomain.com', // Set sender
            replyTo: env('MAIL_FROM_ADDRESS') // Set the reply-to address
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.interview',  // Specify the view for the email content
            with: [
                'name' => $this->name,
                'email' => $this->email,
                'jobTitle' => $this->jobTitle,
                'interviewDate' => $this->interviewDate,
                'interviewTime' => $this->interviewTime,
                'interviewLocation' => $this->interviewLocation,
            ]
        );
    }
    public function build()
    {
        return $this->from(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME')) // Using env variables
            ->view('emails.interview')
            ->subject('Call For Interviews')
            ->with([
                'name' => $this->name,
                'email' => $this->email,
                'jobTitle' => $this->jobTitle,
                'interviewDate' => $this->interviewDate,
                'interviewTime' => $this->interviewTime,
                'interviewLocation' => $this->interviewLocation,
            ]);
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
