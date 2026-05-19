<?php
/*
 * Copyright (c) 2023/9/6 sw@stawitech
 */

namespace App\Mail\Employee;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
class NewEmployeeEmail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct($mailContent)
    {
        $this->content = $mailContent;
    }

    public function build()
    {
        return $this->view('emails.new_user_welcome_email')
            ->subject('Welcome to the Staff Portal')
            ->with(['content'=>$this->content
            ])->delay(30);
    }

}