<?php

namespace App\Notifications;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BookingStatusNotification extends Notification
{
    use Queueable;

    public function __construct(
        public Booking $booking,
        public string $event,
    ) {}

    public function via(object $notifiable): array
    {
        $prefKey = "email_bookings";
        $pref = $notifiable->notificationPreference;

        return ($pref && ! $pref->{$prefKey}) ? ['database'] : ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $isTutor = $notifiable->id === $this->booking->tutor_id;
        $other = $isTutor ? $this->booking->student : $this->booking->tutor;
        $when = $this->booking->scheduled_date->format('D, M j, Y')." at {$this->booking->start_time}";

        $lines = [
            'confirmed' => "Your session with {$other->name} on {$when} is confirmed.",
            'cancelled' => "Your session with {$other->name} on {$when} was cancelled.",
            'completed' => "Your session with {$other->name} on {$when} is marked complete.",
            'created' => "New booking request with {$other->name} for {$when}.",
        ];

        return (new MailMessage)
            ->subject('Booking update — IndorEdu')
            ->greeting("Hi {$notifiable->name},")
            ->line($lines[$this->event] ?? "Your booking status changed to {$this->event}.")
            ->action('View booking', url("/bookings/{$this->booking->id}"))
            ->line('Thanks for using IndorEdu!');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'booking_id' => $this->booking->id,
            'event' => $this->event,
            'scheduled_date' => $this->booking->scheduled_date->toDateString(),
            'start_time' => (string) $this->booking->start_time,
        ];
    }
}
