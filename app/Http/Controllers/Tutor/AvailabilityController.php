<?php

namespace App\Http\Controllers\Tutor;

use App\Http\Controllers\Controller;
use App\Models\TutorAvailability;
use App\Models\TutorTimeOff;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AvailabilityController extends Controller
{
    public function index(Request $request): View
    {
        $profile = $request->user()->tutorProfile;
        $availabilities = $profile->availabilities()->orderBy('day_of_week')->orderBy('start_time')->get()->groupBy('day_of_week');
        $timeOffs = $profile->timeOffs()->where('date', '>=', now()->toDateString())->orderBy('date')->get();

        return view('tutor.availability', compact('availabilities', 'timeOffs'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'day_of_week' => ['required', 'integer', 'min:0', 'max:6'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
        ]);

        $request->user()->tutorProfile->availabilities()->create($data);

        return back()->with('status', 'Availability added.');
    }

    public function destroy(Request $request, TutorAvailability $availability): RedirectResponse
    {
        abort_unless($availability->tutor_profile_id === $request->user()->tutorProfile->id, 403);
        $availability->delete();

        return back()->with('status', 'Availability removed.');
    }

    public function storeTimeOff(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'date' => ['required', 'date', 'after_or_equal:today'],
            'start_time' => ['nullable', 'date_format:H:i'],
            'end_time' => ['nullable', 'date_format:H:i', 'after:start_time'],
            'reason' => ['nullable', 'string', 'max:255'],
        ]);

        $request->user()->tutorProfile->timeOffs()->create($data);

        return back()->with('status', 'Time off added.');
    }

    public function destroyTimeOff(Request $request, TutorTimeOff $timeOff): RedirectResponse
    {
        abort_unless($timeOff->tutor_profile_id === $request->user()->tutorProfile->id, 403);
        $timeOff->delete();

        return back()->with('status', 'Time off removed.');
    }
}
