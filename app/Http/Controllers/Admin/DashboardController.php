<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\ContentReport;
use App\Models\Payment;
use App\Models\Review;
use App\Models\TutorProfile;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $stats = [
            'total_users' => User::count(),
            'total_students' => User::where('role', User::ROLE_STUDENT)->count(),
            'total_tutors' => User::where('role', User::ROLE_TUTOR)->count(),
            'pending_tutors' => TutorProfile::where('status', TutorProfile::STATUS_PENDING)->count(),
            'total_bookings' => Booking::count(),
            'bookings_this_month' => Booking::whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->count(),
            'revenue_total' => Payment::where('status', 'completed')->sum('amount'),
            'revenue_this_month' => Payment::where('status', 'completed')->whereMonth('paid_at', now()->month)->whereYear('paid_at', now()->year)->sum('amount'),
            'platform_fees_total' => Payment::where('status', 'completed')->sum('platform_fee'),
            'pending_reports' => ContentReport::where('status', ContentReport::STATUS_PENDING)->count(),
            'flagged_reviews' => Review::where('is_flagged', true)->count(),
            'avg_rating' => round(Review::where('is_approved', true)->avg('rating') ?? 0, 2),
        ];

        $signupsByDay = User::where('created_at', '>=', now()->subDays(30))
            ->selectRaw('DATE(created_at) as date, COUNT(*) as total')
            ->groupBy('date')->orderBy('date')->get();

        $bookingsByDay = Booking::where('created_at', '>=', now()->subDays(30))
            ->selectRaw('DATE(created_at) as date, COUNT(*) as total')
            ->groupBy('date')->orderBy('date')->get();

        $revenueByDay = Payment::where('status', 'completed')->where('paid_at', '>=', now()->subDays(30))
            ->selectRaw('DATE(paid_at) as date, SUM(amount) as total')
            ->groupBy('date')->orderBy('date')->get();

        $topSubjects = DB::table('bookings')
            ->join('subjects', 'subjects.id', '=', 'bookings.subject_id')
            ->selectRaw('subjects.name, COUNT(*) as total')
            ->groupBy('subjects.name')
            ->orderByDesc('total')
            ->take(6)
            ->get();

        $topTutors = TutorProfile::with('user')
            ->approved()
            ->orderByDesc('rating_avg')
            ->orderByDesc('total_sessions')
            ->take(5)
            ->get();

        return view('admin.dashboard', compact('stats', 'signupsByDay', 'bookingsByDay', 'revenueByDay', 'topSubjects', 'topTutors'));
    }
}
