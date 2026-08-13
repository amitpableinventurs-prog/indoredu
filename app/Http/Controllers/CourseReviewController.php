<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseReview;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CourseReviewController extends Controller
{
    public function store(Request $request, Course $course): RedirectResponse
    {
        abort_unless($request->user()->isStudent(), 403);

        $profile = $request->user()->studentProfile;

        abort_unless(
            $course->enrollments()->where('student_profile_id', $profile->id)->where('status', '!=', 'cancelled')->exists(),
            422,
            'You can only review courses you are enrolled in.'
        );

        $data = $request->validate([
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['nullable', 'string', 'max:2000'],
        ]);

        CourseReview::updateOrCreate(
            ['course_id' => $course->id, 'student_profile_id' => $profile->id],
            ['rating' => $data['rating'], 'comment' => $data['comment'] ?? null]
        );

        $this->recalculateCourseRating($course);

        return back()->with('status', 'Thanks for your review!');
    }

    protected function recalculateCourseRating(Course $course): void
    {
        $stats = $course->reviews()->selectRaw('AVG(rating) as avg_rating, COUNT(*) as total')->first();

        $course->update([
            'rating_avg' => round($stats->avg_rating ?? 0, 2),
            'rating_count' => $stats->total ?? 0,
        ]);
    }
}
