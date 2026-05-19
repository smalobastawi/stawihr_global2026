<?php

namespace App\Mail\Payroll;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PayrollErrorNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $errors;

    public function __construct($errors)
    {
        $this->errors = $errors;
    }

    public function build()
    {
        return $this->view('emails.payroll.error_notification')
            ->subject('Payroll Processing Errors')
            ->with([
                'errors' => $this->errors
            ]);
    }
}