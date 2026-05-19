<?php

namespace App\Mail;

use Carbon\Carbon;
use App\Models\Employee;
use App\Models\Training;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use App\Models\TrainingInvitee;
use Illuminate\Support\Facades\URL;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Contracts\Queue\ShouldQueue;

class TrainingInvitationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $training;
    public $employee;
    public $acceptUrl;
    public $declineUrl;

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

        // Ensure start_date is a Carbon instance
        $expires = $training->start_date instanceof Carbon 
            ? $training->start_date 
            : Carbon::parse($training->start_date);

        // Generate URLs for accepting/declining
        $this->acceptUrl = URL::temporarySignedRoute(
            'ess.trainings.invitation.response',
            $expires,
            [
                'training' => $training->id,
                'employee' => $employee->employee_id,
                'status' => 'accepted'
            ]
        );

        $this->declineUrl = URL::temporarySignedRoute(
            'ess.trainings.invitation.response',
            $expires,
            [
                'training' => $training->id,
                'employee' => $employee->employee_id,
                'status' => 'declined'
            ]
        );
    }

    /**
     * Get the message envelope.
     *
     * @return \Illuminate\Mail\Mailables\Envelope
     */
    public function envelope()
    {
        return new Envelope(
            subject:  'Training Invitation: ' . $this->training->subject,
        );
    }

    /**
     * Get the message content definition.
     *
     * @return \Illuminate\Mail\Mailables\Content
     */
    public function content()
    {
        $invite = TrainingInvitee::where([
            'training_id' => $this->training->id,
            'employee_id' => $this->employee->employee_id
        ])->firstOrFail();
        $hasResponded = $invite && $invite->responded_at;
        $isExpired = now() > $this->training->start_date;
        return new Content(
            view: 'emails.training_invitation',
            with: [
                'training' => $this->training,
                'employee' => $this->employee,
                'acceptUrl' => $this->acceptUrl,
                'declineUrl' => $this->declineUrl,
                'hasResponded' => $hasResponded,
                'isExpired' => $isExpired
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
