<?php

namespace App\Mail\Auth;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OtpVerification extends Mailable
{
    use Queueable, SerializesModels;

    protected $content;

    public function __construct($mailContent)
    {
        $this->content = $mailContent;
    }

    public function build()
    {
        $app_name = config('app.name');

        return $this->view('emails.otp_verification_email')
            ->subject(`$app_name verification code`)
            ->with(['content'=>$this->content ])->delay(30);

    }

}
