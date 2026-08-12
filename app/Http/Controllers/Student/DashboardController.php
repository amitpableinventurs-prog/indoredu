<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\TutorProfile;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        $upcomingBookings = $user->bookingsAsStudent()
            ->with(['tutor.tutorProfile', 'subject'])
            ->whereIn('status', [Booking::STATUS_PENDING, Booking::STATUS_CONFIRMED])
            ->where('scheduled_date', '>=', now()->toDateString())
            ->orderBy('scheduled_date')->orderBy('start_time')
            ->take(5)
            ->get();

        $pendingReviews = $user->bookingsAsStudent()
            ->with(['tutor', 'subject'])
            ->where('status', Booking::STATUS_COMPLETED)
            ->whereDoesntHave('review')
            ->latest('scheduled_date')
            ->take(3)
            ->get();

        $stats = [
            'total_sessions' => $user->bookingsAsStudent()->where('status', Booking::STATUS_COMPLETED)->count(),
            'hours_learned' => round($user->bookingsAsStudent()->where('status', Booking::STATUS_COMPLETED)->sum('duration_minutes') / 60, 1),
            'upcoming_count' => $user->bookingsAsStudent()->whereIn('status', [Booking::STATUS_PENDING, Booking::STATUS_CONFIRMED])->where('scheduled_date', '>=', now()->toDateString())->count(),
        ];

        $recommended = $this->recommendedTutors($user);

        return view('student.dashboard', compact('upcomingBookings', 'pendingReviews', 'stats', 'recommended'));
    }

    protected function recommendedTutors($user)
    {
        $subjectIds = $user->studentProfile?->subjects()->pluck('subjects.id') ?? collect();

        $query = TutorProfile::approved()->with(['user', 'subjects']);

        if ($subjectIds->isNotEmpty()) {
            $query->whereHas('subjects', fn ($q) => $q->whereIn('subjects.id', $subjectIds));
        }

        return $query->orderByDesc('rating_avg')->take(4)->get();
    }
}
