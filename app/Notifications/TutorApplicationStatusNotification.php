<?php

namespace App\Notifications;

use App\Models\TutorProfile;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TutorApplicationStatusNotification extends Notification
{
    use Queueable;

    public function __construct(public TutorProfile $tutorProfile) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $mail = (new MailMessage)->greeting("Hi {$notifiable->name},");

        if ($this->tutorProfile->status === TutorProfile::STATUS_APPROVED) {
            return $mail->subject('Your tutor application was approved — IndorEdu')
                ->line('Congratulations! Your tutor profile has been approved.')
                ->line('You can now set your availability, list subjects, and start accepting bookings.')
                ->action('Go to your dashboard', url('/tutor/dashboard'));
        }

        return $mail->subject('Update on your tutor application — IndorEdu')
            ->line('Your tutor application was not approved at this time.')
            ->when($this->tutorProfile->rejection_reason, fn ($m) => $m->line("Reason: {$this->tutorProfile->rejection_reason}"))
            ->action('Update your profile', url('/tutor/profile'));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'tutor_profile_id' => $this->tutorProfile->id,
            'status' => $this->tutorProfile->status,
        ];
    }
}
