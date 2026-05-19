<?php

namespace App\Notifications;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PerformanceReviewSubmitted extends Notification implements ShouldQueue
{
    use Queueable;

    protected $employee;
    protected $financialYear;
    protected $supervisor;
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($employee, $financialYear, $supervisor)
    {
        //
        $this->employee = $employee;
        $this->financialYear = $financialYear;
        $this->supervisor = $supervisor;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database'];
    }


    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        $start_date = Carbon::parse($this->financialYear->start_date)->format('F d, Y');
        $end_date = Carbon::parse($this->financialYear->end_date)->format('F d, Y');
        $data = [
            //
            'message' => "New performance review submitted for {$this->employee->full_name} (FY: {$start_date} - {$end_date}) by {$this->supervisor->full_name}.",
            'employee_id' => $this->employee->employee_id,
            'financial_year_id' => $this->financialYear->id,
            'supervisor_name' => $this->supervisor->full_name,
            'timestamp' => now()->toDateTimeString(),
            'has_link' => false // Explicitly mark as no link
        ];
        return $data;
    }
}
