<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\SubjectCategory;
use Illuminate\Http\Request;

class CourseCatalogController extends Controller
{
    public function index(Request $request)
    {
        $query = Course::query()
            ->where('status', Course::STATUS_PUBLISHED)
            ->with(['tutorProfile.user', 'subject'])
            ->withCount('enrollments');

        if ($search = $request->string('q')->trim()->value()) {
            $query->where('title', 'like', "%{$search}%");
        }

        if ($subjectSlug = $request->string('subject')->trim()->value()) {
            $query->whereHas('subject', fn ($s) => $s->where('slug', $subjectSlug));
        }

        if ($level = $request->string('level')->trim()->value()) {
            $query->where('level', $level);
        }

        $courses = $query->latest()->paginate(12)->withQueryString();
        $categories = SubjectCategory::with('subjects')->orderBy('name')->get();

        return view('courses.index', compact('courses', 'categories'));
    }

    public function show(Course $course)
    {
        abort_unless($course->status === Course::STATUS_PUBLISHED, 404);
        $course->load(['tutorProfile.user', 'subject']);

        return view('courses.show', compact('course'));
    }
}
