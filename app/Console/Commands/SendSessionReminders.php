<?php

namespace App\Console\Commands;

use App\Models\Booking;
use App\Notifications\SessionReminderNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class SendSessionReminders extends Command
{
    protected $signature = 'bookings:send-reminders';

    protected $description = 'Notify students and tutors about sessions starting within the next hour';

    public function handle(): int
    {
        $windowStart = Carbon::now();
        $windowEnd = Carbon::now()->addHour();

        $bookings = Booking::query()
            ->where('status', Booking::STATUS_CONFIRMED)
            ->whereNull('reminder_sent_at')
            ->whereDate('scheduled_date', $windowStart->toDateString())
            ->whereTime('start_time', '>=', $windowStart->toTimeString())
            ->whereTime('start_time', '<=', $windowEnd->toTimeString())
            ->with(['student', 'tutor'])
            ->get();

        foreach ($bookings as $booking) {
            $booking->student->notify(new SessionReminderNotification($booking));
            $booking->tutor->notify(new SessionReminderNotification($booking));
            $booking->update(['reminder_sent_at' => now()]);
        }

        $this->info("Sent reminders for {$bookings->count()} upcoming session(s).");

        return self::SUCCESS;
    }
}
