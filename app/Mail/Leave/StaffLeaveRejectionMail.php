<?php
/*
 * Copyright (c) 2023/9/6 sw@stawitech
 */

namespace App\Mail\Leave;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
class StaffLeaveRejectionMail extends Mailable
{
    use Queueable, SerializesModels;
    public function __construct($mailContent)
    {
        $this->content = $mailContent;
    }

    public function build()
    {
        return $this->view('emails.staff_leave_rejected')
            ->subject('Leave Application Rejected')
            ->with(['content'=>$this->content
            ])->delay(240);
    }


}