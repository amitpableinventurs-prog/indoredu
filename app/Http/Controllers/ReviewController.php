<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Review;
use App\Notifications\NewReviewNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function store(Request $request, Booking $booking): RedirectResponse
    {
        abort_unless($request->user()->id === $booking->student_id, 403);
        abort_unless($booking->status === Booking::STATUS_COMPLETED, 422, 'You can only review completed sessions.');
        abort_if($booking->review()->exists(), 422, 'You already reviewed this session.');

        $data = $request->validate([
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['nullable', 'string', 'max:2000'],
        ]);

        $review = Review::create([
            'booking_id' => $booking->id,
            'student_id' => $booking->student_id,
            'tutor_id' => $booking->tutor_id,
            'rating' => $data['rating'],
            'comment' => $data['comment'] ?? null,
        ]);

        $this->recalculateTutorRating($booking->tutor_id);

        $booking->tutor->notify(new NewReviewNotification($review));

        return back()->with('status', 'Thanks for your review!');
    }

    public function respond(Request $request, Review $review): RedirectResponse
    {
        $this->authorize('respond', $review);

        $data = $request->validate([
            'tutor_response' => ['required', 'string', 'max:1000'],
        ]);

        $review->update([
            'tutor_response' => $data['tutor_response'],
            'tutor_responded_at' => now(),
        ]);

        return back()->with('status', 'Response posted.');
    }

    protected function recalculateTutorRating(int $tutorId): void
    {
        $stats = Review::where('tutor_id', $tutorId)->where('is_approved', true)
            ->selectRaw('AVG(rating) as avg_rating, COUNT(*) as total')
            ->first();

        \App\Models\TutorProfile::where('user_id', $tutorId)->update([
            'rating_avg' => round($stats->avg_rating ?? 0, 2),
            'rating_count' => $stats->total ?? 0,
        ]);
    }
}
