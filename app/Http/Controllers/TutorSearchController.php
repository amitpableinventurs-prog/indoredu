<?php

namespace App\Http\Controllers;

use App\Models\Subject;
use App\Models\SubjectCategory;
use App\Models\TutorProfile;
use Illuminate\Http\Request;

class TutorSearchController extends Controller
{
    public function index(Request $request)
    {
        $query = TutorProfile::query()
            ->approved()
            ->with(['user', 'subjects.category'])
            ->whereHas('user', fn ($u) => $u->where('status', 'active'));

        if ($search = $request->string('q')->trim()->value()) {
            $query->where(function ($q) use ($search) {
                $q->where('headline', 'like', "%{$search}%")
                    ->orWhere('bio', 'like', "%{$search}%")
                    ->orWhereHas('user', fn ($u) => $u->where('name', 'like', "%{$search}%"))
                    ->orWhereHas('subjects', fn ($s) => $s->where('name', 'like', "%{$search}%"));
            });
        }

        if ($subjectSlug = $request->string('subject')->trim()->value()) {
            $query->whereHas('subjects', fn ($s) => $s->where('slug', $subjectSlug));
        }

        if ($minRate = $request->input('min_rate')) {
            $query->where('hourly_rate', '>=', $minRate);
        }

        if ($maxRate = $request->input('max_rate')) {
            $query->where('hourly_rate', '<=', $maxRate);
        }

        if ($minRating = $request->input('min_rating')) {
            $query->where('rating_avg', '>=', $minRating);
        }

        if ($city = $request->string('city')->trim()->value()) {
            $query->whereHas('user', fn ($u) => $u->where('city', 'like', "%{$city}%"));
        }

        if ($country = $request->string('country')->trim()->value()) {
            $query->whereHas('user', fn ($u) => $u->where('country', 'like', "%{$country}%"));
        }

        if ($request->boolean('trial_only')) {
            $query->where('offers_trial', true);
        }

        $sort = $request->string('sort')->value() ?: 'rating';
        match ($sort) {
            'rate_low' => $query->orderBy('hourly_rate'),
            'rate_high' => $query->orderByDesc('hourly_rate'),
            'experience' => $query->orderByDesc('experience_years'),
            default => $query->orderByDesc('rating_avg'),
        };

        $tutors = $query->paginate(12)->withQueryString();
        $categories = SubjectCategory::with('subjects')->orderBy('name')->get();

        return view('tutors.index', compact('tutors', 'categories'));
    }

    public function show(TutorProfile $tutorProfile)
    {
        abort_unless($tutorProfile->status === TutorProfile::STATUS_APPROVED, 404);

        $tutorProfile->load(['user', 'subjects.category', 'availabilities' => fn ($q) => $q->where('is_active', true), 'courses' => fn ($q) => $q->where('status', 'published')]);

        $reviews = \App\Models\Review::where('tutor_id', $tutorProfile->user_id)
            ->where('is_approved', true)
            ->with('student')
            ->latest()
            ->paginate(10);

        return view('tutors.show', compact('tutorProfile', 'reviews'));
    }
}
