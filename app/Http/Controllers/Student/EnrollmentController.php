<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseEnrollment;
use App\Models\Payment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EnrollmentController extends Controller
{
    public function index(Request $request): View
    {
        $enrollments = $request->user()->studentProfile->enrollments()
            ->with(['course.tutorProfile.user', 'course.subject'])
            ->latest()
            ->paginate(12);

        return view('student.courses.index', compact('enrollments'));
    }

    public function store(Request $request, Course $course): RedirectResponse
    {
        abort_unless($course->isPublished(), 404);

        $profile = $request->user()->studentProfile;

        if ($course->enrollments()->where('student_profile_id', $profile->id)->exists()) {
            return back()->with('error', 'You are already enrolled in this course.');
        }

        if (! $course->is_group && $course->enrollments()->count() >= $course->max_students) {
            return back()->with('error', 'This course is full.');
        }

        $enrollment = CourseEnrollment::create([
            'course_id' => $course->id,
            'student_profile_id' => $profile->id,
            'status' => 'active',
            'enrolled_at' => now(),
        ]);

        if ($course->price <= 0) {
            return redirect()->route('student.courses.index')->with('status', 'Enrolled!');
        }

        $platformFeePercent = config('payments.platform_fee_percent', 15);
        $fee = round($course->price * $platformFeePercent / 100, 2);

        $payment = Payment::create([
            'course_enrollment_id' => $enrollment->id,
            'payer_id' => $request->user()->id,
            'payee_id' => $course->tutorProfile->user_id,
            'gateway' => config('payments.default_gateway', 'stripe'),
            'amount' => $course->price,
            'currency' => config('payments.currency', 'INR'),
            'platform_fee' => $fee,
            'net_amount' => $course->price - $fee,
            'status' => Payment::STATUS_PENDING,
        ]);

        return redirect()->route('payments.checkout', $payment);
    }

    public function destroy(Request $request, CourseEnrollment $enrollment): RedirectResponse
    {
        abort_unless($enrollment->student_profile_id === $request->user()->studentProfile->id, 403);
        $enrollment->update(['status' => 'cancelled']);

        return back()->with('status', 'Enrollment cancelled.');
    }
}
