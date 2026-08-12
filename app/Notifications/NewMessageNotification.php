<?php

namespace App\Notifications;

use App\Models\Message;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewMessageNotification extends Notification
{
    use Queueable;

    public function __construct(public Message $message) {}

    public function via(object $notifiable): array
    {
        $pref = $notifiable->notificationPreference;

        return ($pref && ! $pref->email_messages) ? ['database'] : ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('New message — IndorEdu')
            ->greeting("Hi {$notifiable->name},")
            ->line("{$this->message->sender->name} sent you a message:")
            ->line('"'.\Illuminate\Support\Str::limit($this->message->body, 140).'"')
            ->action('Reply', url("/messages/{$this->message->conversation_id}"));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'conversation_id' => $this->message->conversation_id,
            'sender_id' => $this->message->sender_id,
            'sender_name' => $this->message->sender->name,
            'preview' => \Illuminate\Support\Str::limit($this->message->body, 140),
        ];
    }
}
