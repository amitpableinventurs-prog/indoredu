<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Review;
use App\Models\Subject;
use App\Models\SubjectCategory;
use App\Models\TutorProfile;
use Illuminate\Support\Facades\Cache;

class HomeController extends Controller
{
    public function index()
    {
        $stats = Cache::remember('home.stats', now()->addMinutes(10), fn () => [
            'tutors' => TutorProfile::approved()->count(),
            'subjects' => Subject::count(),
            'sessions_completed' => Booking::where('status', 'completed')->count(),
            'avg_rating' => Review::where('is_approved', true)->avg('rating') ?? 5,
        ]);

        $featuredTutors = TutorProfile::query()
            ->approved()
            ->with('user', 'subjects')
            ->orderByDesc('is_featured')
            ->orderByDesc('rating_avg')
            ->take(6)
            ->get();

        $categories = SubjectCategory::withCount('subjects')->orderBy('name')->get();

        $testimonials = Review::query()
            ->where('is_approved', true)
            ->where('rating', '>=', 4)
            ->whereNotNull('comment')
            ->with(['student', 'tutor'])
            ->latest()
            ->take(6)
            ->get();

        return view('home', compact('featuredTutors', 'categories', 'testimonials', 'stats'));
    }
}
