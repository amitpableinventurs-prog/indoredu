<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminActionLog;
use App\Models\TutorProfile;
use App\Notifications\TutorApplicationStatusNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TutorApplicationController extends Controller
{
    public function index(Request $request): View
    {
        $status = $request->query('status', 'pending');

        $applications = TutorProfile::with(['user', 'certificates'])
            ->when($status !== 'all', fn ($q) => $q->where('status', $status))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.tutor-applications.index', compact('applications', 'status'));
    }

    public function show(TutorProfile $tutorProfile): View
    {
        $tutorProfile->load(['user', 'certificates', 'subjects']);

        return view('admin.tutor-applications.show', compact('tutorProfile'));
    }

    public function approve(Request $request, TutorProfile $tutorProfile): RedirectResponse
    {
        $tutorProfile->update([
            'status' => TutorProfile::STATUS_APPROVED,
            'approved_at' => now(),
            'approved_by' => $request->user()->id,
            'rejection_reason' => null,
        ]);

        AdminActionLog::record($request->user(), 'tutor.approved', $tutorProfile);
        $tutorProfile->user->notify(new TutorApplicationStatusNotification($tutorProfile));

        return back()->with('status', "{$tutorProfile->user->name} approved as a tutor.");
    }

    public function reject(Request $request, TutorProfile $tutorProfile): RedirectResponse
    {
        $data = $request->validate(['rejection_reason' => ['required', 'string', 'max:500']]);

        $tutorProfile->update([
            'status' => TutorProfile::STATUS_REJECTED,
            'rejection_reason' => $data['rejection_reason'],
        ]);

        AdminActionLog::record($request->user(), 'tutor.rejected', $tutorProfile, $data);
        $tutorProfile->user->notify(new TutorApplicationStatusNotification($tutorProfile));

        return back()->with('status', 'Application rejected.');
    }

    public function verifyCertificate(Request $request, \App\Models\TutorCertificate $certificate): RedirectResponse
    {
        $data = $request->validate(['status' => ['required', 'in:verified,rejected']]);

        $certificate->update([
            'status' => $data['status'],
            'verified_by' => $request->user()->id,
            'verified_at' => now(),
        ]);

        AdminActionLog::record($request->user(), "certificate.{$data['status']}", $certificate);

        return back()->with('status', 'Certificate updated.');
    }
}
