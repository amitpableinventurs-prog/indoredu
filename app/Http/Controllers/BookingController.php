<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Conversation;
use App\Models\Payment;
use App\Models\Subject;
use App\Models\TutorProfile;
use App\Models\User;
use App\Notifications\BookingStatusNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Validation\ValidationException;

class BookingController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $user = $request->user();
        abort_unless($user->isStudent(), 403, 'Only students can book sessions.');

        $data = $request->validate([
            'tutor_id' => ['required', 'exists:users,id'],
            'subject_id' => ['required', 'exists:subjects,id'],
            'scheduled_date' => ['required', 'date', 'after_or_equal:today'],
            'start_time' => ['required', 'date_format:H:i'],
            'duration_minutes' => ['required', 'integer', 'in:30,60,90,120'],
            'student_notes' => ['nullable', 'string', 'max:1000'],
            'is_trial' => ['sometimes', 'boolean'],
        ]);

        $tutor = User::where('id', $data['tutor_id'])->where('role', User::ROLE_TUTOR)->firstOrFail();
        $tutorProfile = $tutor->tutorProfile;
        abort_unless($tutorProfile && $tutorProfile->isApproved(), 404);

        if ($tutor->hasBlocked($user) || $user->hasBlocked($tutor)) {
            abort(403, 'You are unable to book with this tutor.');
        }

        $isTrial = $request->boolean('is_trial') && $tutorProfile->offers_trial;

        if ($isTrial && Booking::where('student_id', $user->id)->where('tutor_id', $tutor->id)->where('is_trial', true)->exists()) {
            throw ValidationException::withMessages(['is_trial' => 'You already used your trial session with this tutor.']);
        }

        $start = Carbon::createFromFormat('H:i', $data['start_time']);
        $end = $start->copy()->addMinutes((int) $data['duration_minutes']);

        $overlaps = Booking::where('tutor_id', $tutor->id)
            ->where('scheduled_date', $data['scheduled_date'])
            ->whereIn('status', [Booking::STATUS_PENDING, Booking::STATUS_CONFIRMED])
            ->where('start_time', '<', $end->format('H:i:s'))
            ->where('end_time', '>', $start->format('H:i:s'))
            ->exists();

        if ($overlaps) {
            throw ValidationException::withMessages([
                'start_time' => 'This time slot is no longer available. Please choose another.',
            ]);
        }

        if ($isTrial) {
            $price = (float) $tutorProfile->trial_price;
        } else {
            $rate = $tutorProfile->tutorSubjects()->where('subject_id', $data['subject_id'])->value('hourly_rate')
                ?? $tutorProfile->hourly_rate;
            $price = round($rate * ($data['duration_minutes'] / 60), 2);
        }

        $booking = Booking::create([
            'student_id' => $user->id,
            'tutor_id' => $tutor->id,
            'subject_id' => $data['subject_id'],
            'scheduled_date' => $data['scheduled_date'],
            'start_time' => $start->format('H:i:s'),
            'end_time' => $end->format('H:i:s'),
            'timezone' => $user->timezone ?? 'UTC',
            'duration_minutes' => $data['duration_minutes'],
            'is_trial' => $isTrial,
            'price' => $price,
            'status' => Booking::STATUS_PENDING,
            'student_notes' => $data['student_notes'] ?? null,
            'meeting_link' => 'https://meet.jit.si/IndorEdu-'.\Illuminate\Support\Str::random(12),
        ]);

        $conversation = Conversation::create(['booking_id' => $booking->id, 'subject' => 'Booking #'.$booking->id]);
        $conversation->participants()->attach([$user->id, $tutor->id]);

        if ($price <= 0) {
            $booking->update(['status' => Booking::STATUS_CONFIRMED, 'confirmed_at' => now()]);
            $tutor->notify(new BookingStatusNotification($booking, 'created'));

            return redirect()->route('bookings.show', $booking)->with('status', 'Free trial session booked!');
        }

        $platformFeePercent = config('payments.platform_fee_percent', 15);
        $fee = round($price * $platformFeePercent / 100, 2);

        $payment = Payment::create([
            'booking_id' => $booking->id,
            'payer_id' => $user->id,
            'payee_id' => $tutor->id,
            'gateway' => config('payments.default_gateway', 'stripe'),
            'amount' => $price,
            'currency' => config('payments.currency', 'INR'),
            'platform_fee' => $fee,
            'net_amount' => $price - $fee,
            'status' => Payment::STATUS_PENDING,
        ]);

        return redirect()->route('payments.checkout', $payment)
            ->with('status', 'Booking created — complete payment to confirm your session.');
    }

    public function show(Request $request, Booking $booking)
    {
        $this->authorize('view', $booking);

        $booking->load(['student', 'tutor.tutorProfile', 'subject', 'attendance', 'review', 'payment', 'conversation']);

        return view('bookings.show', compact('booking'));
    }

    public function cancel(Request $request, Booking $booking): RedirectResponse
    {
        $this->authorize('cancel', $booking);

        $data = $request->validate([
            'cancellation_reason' => ['nullable', 'string', 'max:500'],
        ]);

        abort_if(in_array($booking->status, [Booking::STATUS_COMPLETED, Booking::STATUS_CANCELLED]), 422, 'This booking can no longer be cancelled.');

        $booking->update([
            'status' => Booking::STATUS_CANCELLED,
            'cancelled_by' => $request->user()->id,
            'cancellation_reason' => $data['cancellation_reason'] ?? null,
            'cancelled_at' => now(),
        ]);

        if ($booking->payment && $booking->payment->isCompleted()) {
            try {
                app(\App\Services\Payments\PaymentManager::class)
                    ->gateway($booking->payment->gateway)
                    ->refund($booking->payment);
            } catch (\Throwable $e) {
                // The booking is already cancelled regardless of refund outcome; a failed
                // gateway call (network issue, API outage) shouldn't block the cancellation.
                // The payment keeps its "completed" status so it's visibly unresolved for admin follow-up.
                report($e);
            }
        }

        $recipient = $request->user()->id === $booking->student_id ? $booking->tutor : $booking->student;
        $recipient->notify(new BookingStatusNotification($booking, 'cancelled'));

        return back()->with('status', 'Booking cancelled.');
    }

    public function complete(Request $request, Booking $booking): RedirectResponse
    {
        $this->authorize('markAttendance', $booking);

        abort_unless($booking->status === Booking::STATUS_CONFIRMED, 422, 'Only confirmed bookings can be marked complete.');

        $booking->update(['status' => Booking::STATUS_COMPLETED, 'completed_at' => now()]);

        $booking->tutor->tutorProfile()->increment('total_sessions');

        $booking->student->notify(new BookingStatusNotification($booking, 'completed'));

        return back()->with('status', 'Session marked as completed.');
    }
}
