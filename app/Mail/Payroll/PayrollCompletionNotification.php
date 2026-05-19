<?php

namespace App\Mail\Payroll;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PayrollCompletionNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $message;
    public $successCount;
    public $errorCount;
    public $errors;

    public function __construct($message, $successCount, $errorCount, $errors = [])
    {
        $this->message = $message;
        $this->successCount = $successCount;
        $this->errorCount = $errorCount;
        $this->errors = $errors;
    }

    public function build()
    {
        return $this->view('emails.payroll.completion_notification')
            ->subject('Payroll Processing Completed')
            ->with([
                'message' => $this->message,
                'successCount' => $this->successCount,
                'errorCount' => $this->errorCount,
                'errors' => $this->errors
            ]);
    }
}