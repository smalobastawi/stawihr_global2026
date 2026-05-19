<?php
/*
 * Copyright (c) 2023/9/6 sw@stawitech
 */

namespace App\Mail\P9;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendEmployeeP9 extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct($mailContent)
    {
        $this->content = $mailContent;
    }

    public function build()
    {
        //dd($this->content->p9form->output());
        return $this->view('emails.send_employee_p9.blade')
            ->subject('P9 Form')
            ->with(['content'=>$this->content
                ->attachData($this->content->p9form->output(), "P9.pdf")
            ])->delay(15);
    }

}