<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class PrivacyController extends Controller
{
    /**
     * Export the authenticated user's personal data as a downloadable JSON file.
     */
    public function export(Request $request)
    {
        $user = $request->user();

        $data = [
            'account' => $user->only(['id', 'name', 'email', 'phone', 'role', 'timezone', 'created_at']),
            'tutor_profile' => $user->tutorProfile,
            'student_profile' => $user->studentProfile,
            'bookings_as_student' => $user->bookingsAsStudent()->with(['tutor:id,name', 'subject:id,name'])->get(),
            'bookings_as_tutor' => $user->bookingsAsTutor()->with(['student:id,name', 'subject:id,name'])->get(),
            'payments_made' => $user->paymentsMade,
            'reviews_written' => \App\Models\Review::where('student_id', $user->id)->get(),
            'notification_preferences' => $user->notificationPreference,
        ];

        $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

        return Response::make($json, 200, [
            'Content-Type' => 'application/json',
            'Content-Disposition' => 'attachment; filename="tutorhub-my-data-'.now()->format('Y-m-d').'.json"',
        ]);
    }
}
