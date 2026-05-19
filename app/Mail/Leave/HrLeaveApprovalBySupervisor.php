<?php
/*
 * Copyright (c) 2023/9/6 sw@stawitech
 */

namespace App\Mail\Leave;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;


class HrLeaveApprovalBySupervisor extends Mailable
{
    use Queueable, SerializesModels;
    public function __construct($mailContent)
    {
        $this->content = $mailContent;
    }

    public function build()
    {
        return $this->view('emails.hr_leave_approval_by_supervisor')
            ->subject('Leave Approved by Supervisor')
            ->with(['content'=>$this->content
            ])->delay(60);
    }

}