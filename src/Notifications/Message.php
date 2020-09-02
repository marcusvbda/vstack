<?php

namespace marcusvbda\vstack\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\BroadcastMessage;

class Message extends Notification
{
    use Queueable;

    public $message;
    public function __construct($message, $type)
    {
        $this->message  = $message;
        $this->_type  = $type;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['broadcast'];
    }

    public function toBroadcast($notifiable)
    {
        return (new BroadcastMessage($this->makeData()))->onQueue('alert-broadcasts');
    }

    public function toArray($notifiable)
    {
        return $this->makeData();
    }

    private function makeData()
    {
        return [
            "message"  => $this->message,
            "_type"    => $this->_type
        ];
    }
}
