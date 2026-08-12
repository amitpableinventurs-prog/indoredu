<?php

namespace App\Http\Controllers\Tutor;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $profile = $user->tutorProfile;

        $upcomingBookings = $user->bookingsAsTutor()
            ->with(['student', 'subject'])
            ->whereIn('status', [Booking::STATUS_PENDING, Booking::STATUS_CONFIRMED])
            ->where('scheduled_date', '>=', now()->toDateString())
            ->orderBy('scheduled_date')->orderBy('start_time')
            ->take(5)
            ->get();

        $stats = [
            'total_sessions' => $profile->total_sessions ?? 0,
            'rating_avg' => $profile->rating_avg ?? 0,
            'rating_count' => $profile->rating_count ?? 0,
            'upcoming_count' => $user->bookingsAsTutor()->whereIn('status', [Booking::STATUS_PENDING, Booking::STATUS_CONFIRMED])->where('scheduled_date', '>=', now()->toDateString())->count(),
            'earnings_this_month' => $user->paymentsReceived()->where('status', 'completed')->whereMonth('paid_at', now()->month)->whereYear('paid_at', now()->year)->sum('net_amount'),
            'unread_messages' => $user->unreadMessagesCount(),
        ];

        return view('tutor.dashboard', compact('profile', 'upcomingBookings', 'stats'));
    }
}
