<?php
/*
 * Copyright (c) 2023/9/6 sw@stawitech
 */

namespace App\Mail\Employee;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PasswordResetSuccess extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct($mailContent)
    {
        $this->content = $mailContent;
    }

    public function build()
    {
        return $this->view('emails.password_reset_success')
            ->subject('Password Reset Successful')
            ->with(['content'=>$this->content
            ])->delay(30);
    }

}