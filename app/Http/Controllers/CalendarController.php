<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CalendarController extends Controller
{
    public function index(Request $request): View
    {
        return view('calendar.index');
    }

    /**
     * JSON feed of the authenticated user's bookings, consumed by the
     * calendar view's JS (FullCalendar-compatible event shape).
     */
    public function events(Request $request)
    {
        $user = $request->user();

        $query = $user->isTutor()
            ? Booking::where('tutor_id', $user->id)
            : Booking::where('student_id', $user->id);

        $bookings = $query->whereIn('status', [Booking::STATUS_PENDING, Booking::STATUS_CONFIRMED, Booking::STATUS_COMPLETED])
            ->with(['student', 'tutor', 'subject'])
            ->get();

        $events = $bookings->map(function (Booking $booking) use ($user) {
            $other = $user->isTutor() ? $booking->student : $booking->tutor;
            $colors = [
                'pending' => '#f59e0b',
                'confirmed' => '#4f46e5',
                'completed' => '#16a34a',
            ];

            return [
                'id' => $booking->id,
                'title' => ($booking->subject->name ?? 'Session').' — '.$other->name,
                'start' => $booking->scheduled_date->toDateString().'T'.$booking->start_time,
                'end' => $booking->scheduled_date->toDateString().'T'.$booking->end_time,
                'color' => $colors[$booking->status] ?? '#6b7280',
                'url' => route('bookings.show', $booking),
            ];
        });

        return response()->json($events);
    }

    /**
     * Available slots for a tutor on a given date, used by the booking
     * widget on the public tutor profile page.
     */
    public function tutorSlots(Request $request, \App\Models\TutorProfile $tutorProfile)
    {
        $date = $request->date('date') ?? now();
        $dayOfWeek = (int) $date->format('w');

        $availabilities = $tutorProfile->availabilities()->where('day_of_week', $dayOfWeek)->where('is_active', true)->get();

        $timeOff = $tutorProfile->timeOffs()->where('date', $date->toDateString())->get();
        if ($timeOff->contains(fn ($t) => is_null($t->start_time))) {
            return response()->json([]);
        }

        $bookings = Booking::where('tutor_id', $tutorProfile->user_id)
            ->where('scheduled_date', $date->toDateString())
            ->whereIn('status', [Booking::STATUS_PENDING, Booking::STATUS_CONFIRMED])
            ->get(['start_time', 'end_time']);

        $slots = [];
        foreach ($availabilities as $availability) {
            $cursor = \Illuminate\Support\Carbon::parse($availability->start_time);
            $end = \Illuminate\Support\Carbon::parse($availability->end_time);

            while ($cursor->copy()->addMinutes(30) <= $end) {
                $slotEnd = $cursor->copy()->addMinutes(30);

                $blocked = $bookings->contains(fn ($b) => $cursor->format('H:i:s') < $b->end_time && $slotEnd->format('H:i:s') > $b->start_time)
                    || $timeOff->contains(fn ($t) => $t->start_time && $cursor->format('H:i:s') < $t->end_time && $slotEnd->format('H:i:s') > $t->start_time);

                if (! $blocked && $cursor->greaterThan(now()->setTimezone($tutorProfile->user->timezone ?? 'UTC'))) {
                    $slots[] = $cursor->format('H:i');
                }

                $cursor->addMinutes(30);
            }
        }

        return response()->json($slots);
    }
}
