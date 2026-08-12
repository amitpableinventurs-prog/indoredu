<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Booking;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function update(Request $request, Booking $booking): RedirectResponse
    {
        $this->authorize('markAttendance', $booking);

        $data = $request->validate([
            'student_status' => ['required', 'in:present,absent,late,excused'],
            'tutor_status' => ['required', 'in:present,absent,late'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $booking->attendance()->updateOrCreate([], [
            ...$data,
            'marked_by' => $request->user()->id,
            'student_check_in' => $data['student_status'] === 'present' ? now() : null,
            'tutor_check_in' => $data['tutor_status'] === 'present' ? now() : null,
        ]);

        if ($booking->status !== Booking::STATUS_COMPLETED) {
            $booking->update(['status' => Booking::STATUS_COMPLETED, 'completed_at' => now()]);
            $booking->tutor->tutorProfile()->increment('total_sessions');
        }

        return back()->with('status', 'Attendance recorded.');
    }
}
