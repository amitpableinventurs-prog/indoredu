<?php

namespace App\Notifications;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SessionReminderNotification extends Notification
{
    use Queueable;

    public function __construct(public Booking $booking) {}

    public function via(object $notifiable): array
    {
        $pref = $notifiable->notificationPreference;

        return ($pref && ! $pref->email_reminders) ? ['database'] : ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $isTutor = $notifiable->id === $this->booking->tutor_id;
        $other = $isTutor ? $this->booking->student : $this->booking->tutor;

        return (new MailMessage)
            ->subject('Upcoming session reminder — IndorEdu')
            ->greeting("Hi {$notifiable->name},")
            ->line("Reminder: your session with {$other->name} starts soon.")
            ->line("Date: {$this->booking->scheduled_date->format('D, M j, Y')}")
            ->line("Time: {$this->booking->start_time} - {$this->booking->end_time} ({$this->booking->timezone})")
            ->when($this->booking->meeting_link, fn ($mail) => $mail->action('Join session', $this->booking->meeting_link))
            ->line('See you there!');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'booking_id' => $this->booking->id,
            'scheduled_date' => $this->booking->scheduled_date->toDateString(),
            'start_time' => (string) $this->booking->start_time,
        ];
    }
}
