<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\SubjectCategory;
use App\Models\TutorProfile;

class HomeController extends Controller
{
    public function index()
    {
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

        return view('home', compact('featuredTutors', 'categories', 'testimonials'));
    }
}
