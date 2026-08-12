<?php

namespace App\Notifications;

use App\Models\Review;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewReviewNotification extends Notification
{
    use Queueable;

    public function __construct(public Review $review) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('You received a new review — IndorEdu')
            ->greeting("Hi {$notifiable->name},")
            ->line("{$this->review->student->name} left you a {$this->review->rating}-star review.")
            ->when($this->review->comment, fn ($mail) => $mail->line('"'.$this->review->comment.'"'))
            ->action('View review', url('/tutor/reviews'));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'review_id' => $this->review->id,
            'rating' => $this->review->rating,
            'student_name' => $this->review->student->name,
        ];
    }
}
