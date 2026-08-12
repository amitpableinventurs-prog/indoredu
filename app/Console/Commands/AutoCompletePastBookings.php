<?php

namespace App\Console\Commands;

use App\Models\Booking;
use Illuminate\Console\Command;

class AutoCompletePastBookings extends Command
{
    protected $signature = 'bookings:auto-complete';

    protected $description = 'Mark confirmed bookings as completed once their end time has passed';

    public function handle(): int
    {
        $bookings = Booking::query()
            ->where('status', Booking::STATUS_CONFIRMED)
            ->where(function ($query) {
                $query->where('scheduled_date', '<', now()->toDateString())
                    ->orWhere(function ($q) {
                        $q->where('scheduled_date', now()->toDateString())
                            ->whereTime('end_time', '<', now()->toTimeString());
                    });
            })
            ->get();

        foreach ($bookings as $booking) {
            $booking->update(['status' => Booking::STATUS_COMPLETED, 'completed_at' => now()]);
            $booking->tutor->tutorProfile()?->increment('total_sessions');
        }

        $this->info("Auto-completed {$bookings->count()} past session(s).");

        return self::SUCCESS;
    }
}
