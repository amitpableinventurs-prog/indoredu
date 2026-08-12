<?php

namespace App\Http\Controllers\Tutor;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StudentController extends Controller
{
    public function index(Request $request): View
    {
        $studentIds = Booking::where('tutor_id', $request->user()->id)->pluck('student_id')->unique();

        $students = User::whereIn('id', $studentIds)
            ->withCount(['bookingsAsStudent as sessions_with_me' => fn ($q) => $q->where('tutor_id', $request->user()->id)])
            ->with(['studentProfile'])
            ->get();

        return view('tutor.students.index', compact('students'));
    }

    public function show(Request $request, User $student): View
    {
        abort_unless($student->isStudent(), 404);

        $bookings = Booking::where('tutor_id', $request->user()->id)
            ->where('student_id', $student->id)
            ->with(['subject', 'attendance', 'review'])
            ->orderByDesc('scheduled_date')
            ->get();

        abort_if($bookings->isEmpty(), 404);

        return view('tutor.students.show', compact('student', 'bookings'));
    }

    public function updateNotes(Request $request, Booking $booking)
    {
        abort_unless($request->user()->id === $booking->tutor_id, 403);

        $data = $request->validate(['tutor_notes' => ['nullable', 'string', 'max:3000']]);
        $booking->update($data);

        return back()->with('status', 'Progress notes saved.');
    }
}
