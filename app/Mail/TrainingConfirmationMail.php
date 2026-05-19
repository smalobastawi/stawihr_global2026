<?php

namespace App\Mail;

use App\Models\Employee;
use App\Models\Training;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Contracts\Queue\ShouldQueue;

class TrainingConfirmationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $training;
    public $employee;
    public $googleCalendarUrl;
    public $outlookCalendarUrl;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Training $training, Employee $employee)
    {
        //
        $this->training = $training;
        $this->employee = $employee;

        // Generate calendar links
        $this->googleCalendarUrl = $this->generateGoogleCalendarLink();
        $this->outlookCalendarUrl = $this->generateOutlookCalendarLink();
    }

    private function generateGoogleCalendarLink()
    {
        return "https://www.google.com/calendar/render?action=TEMPLATE".
               "&text=".urlencode($this->training->subject).
               "&dates=".$this->training->start_date->format('Ymd\THis').
               "/".$this->training->end_date->format('Ymd\THis').
               "&details=".urlencode($this->training->description).
               "&location=".urlencode($this->training->location ?? 'Online');
    }

    private function generateOutlookCalendarLink()
    {
        return "https://outlook.live.com/calendar/0/deeplink/compose?".
               "subject=".urlencode($this->training->subject).
               "&body=".urlencode($this->training->description).
               "&startdt=".$this->training->start_date->format('Y-m-d\TH:i:s').
               "&enddt=".$this->training->end_date->format('Y-m-d\TH:i:s').
               "&location=".urlencode($this->training->location ?? 'Online');
    }

    /**
     * Get the message envelope.
     *
     * @return \Illuminate\Mail\Mailables\Envelope
     */
    public function envelope()
    {
        return new Envelope(
            subject: 'Training Confirmation: ' . $this->training->subject,
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
            view: 'emails.training_confirmation',
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
