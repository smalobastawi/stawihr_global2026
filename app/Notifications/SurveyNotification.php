<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;

class SurveyNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $survey;
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($survey)
    {
        //
        $this->survey = $survey;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database', 'broadcast'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'survey_id' => $this->survey->id,
            'title' => $this->survey->title,
            'message' => 'A new survey is available: ' . $this->survey->title,
            'link' => route('ess.survey.index'),
        ];
    }

    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage([
            'survey_id' => $this->survey->id,
            'title' => $this->survey->title,
            'message' => 'A new survey is available: ' . $this->survey->title,
            'url' => route('survey.show', $this->survey->id),
        ]);
    }


    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'survey_id' => $this->survey->id,
            'title' => $this->survey->title,
            'message' => 'A new survey is available: ' . $this->survey->title,
            'link' => route('ess.survey.index'),
        ];
    }
}
