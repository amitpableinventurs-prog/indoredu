<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminActionLog;
use App\Models\Course;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CourseModerationController extends Controller
{
    public function index(Request $request): View
    {
        $status = $request->query('status', 'pending');

        $courses = Course::with(['tutorProfile.user', 'subject'])
            ->when($status !== 'all', fn ($q) => $q->where('status', $status))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.courses.index', compact('courses', 'status'));
    }

    public function approve(Request $request, Course $course): RedirectResponse
    {
        $course->update(['status' => Course::STATUS_PUBLISHED, 'rejection_reason' => null]);
        AdminActionLog::record($request->user(), 'course.approved', $course);

        return back()->with('status', "\"{$course->title}\" published.");
    }

    public function reject(Request $request, Course $course): RedirectResponse
    {
        $data = $request->validate(['rejection_reason' => ['required', 'string', 'max:500']]);
        $course->update(['status' => Course::STATUS_REJECTED, 'rejection_reason' => $data['rejection_reason']]);
        AdminActionLog::record($request->user(), 'course.rejected', $course, $data);

        return back()->with('status', 'Course rejected.');
    }

    public function archive(Request $request, Course $course): RedirectResponse
    {
        $course->update(['status' => Course::STATUS_ARCHIVED]);
        AdminActionLog::record($request->user(), 'course.archived', $course);

        return back()->with('status', 'Course archived.');
    }
}
