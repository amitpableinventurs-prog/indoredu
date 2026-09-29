<?php

namespace App\Notifications;

use App\Models\Enquiry;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class EnquiryNotification extends Notification
{
    use Queueable;

    public function __construct(
        public Enquiry $enquiry,
        public string $event,
    ) {}

    public function via(object $notifiable): array
    {
        $pref = $notifiable->notificationPreference;

        return ($pref && ! $pref->email_messages) ? ['database'] : ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $lines = [
            'received' => "{$this->enquiry->student->name} sent you a new enquiry: \"{$this->enquiry->title}\".",
            'replied' => "{$this->enquiry->tutor->name} replied to your enquiry \"{$this->enquiry->title}\".",
            'declined' => "{$this->enquiry->tutor->name} is unable to take up your enquiry \"{$this->enquiry->title}\".",
        ];

        return (new MailMessage)
            ->subject('Enquiry update — IndorEdu')
            ->greeting("Hi {$notifiable->name},")
            ->line($lines[$this->event] ?? "Your enquiry \"{$this->enquiry->title}\" was updated.")
            ->action('View enquiry', url("/enquiries/{$this->enquiry->id}"));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'enquiry_id' => $this->enquiry->id,
            'event' => $this->event,
            'title' => $this->enquiry->title,
            'student_name' => $this->enquiry->student->name,
            'tutor_name' => $this->enquiry->tutor->name,
        ];
    }
}
