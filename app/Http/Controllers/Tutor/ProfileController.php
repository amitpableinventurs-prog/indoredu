<?php

namespace App\Http\Controllers\Tutor;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use App\Models\TutorCertificate;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function edit(Request $request): View
    {
        $profile = $request->user()->tutorProfile;
        $subjects = Subject::with('category')->where('is_active', true)->orderBy('name')->get();
        $mySubjectIds = $profile->subjects()->pluck('subjects.id')->all();
        $certificates = $profile->certificates()->latest()->get();

        return view('tutor.profile', compact('profile', 'subjects', 'mySubjectIds', 'certificates'));
    }

    public function update(Request $request): RedirectResponse
    {
        $profile = $request->user()->tutorProfile;

        $data = $request->validate([
            'headline' => ['nullable', 'string', 'max:255'],
            'bio' => ['nullable', 'string', 'max:5000'],
            'hourly_rate' => ['required', 'numeric', 'min:1', 'max:1000'],
            'offers_trial' => ['sometimes', 'boolean'],
            'trial_price' => ['nullable', 'numeric', 'min:0', 'max:1000'],
            'experience_years' => ['required', 'integer', 'min:0', 'max:60'],
            'education' => ['nullable', 'string', 'max:2000'],
            'video_intro_url' => ['nullable', 'url', 'max:255'],
            'languages' => ['nullable', 'array'],
            'languages.*' => ['string', 'max:50'],
            'identity_document' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
        ]);

        $data['offers_trial'] = $request->boolean('offers_trial');

        if ($request->hasFile('identity_document')) {
            if ($profile->identity_document) {
                Storage::disk('local')->delete($profile->identity_document);
            }
            $data['identity_document'] = $request->file('identity_document')->store('identity-documents', 'local');
            // Re-submitting identity documentation triggers another admin review.
            $data['status'] = \App\Models\TutorProfile::STATUS_PENDING;
        }

        $profile->update($data);

        return back()->with('status', 'Profile updated.');
    }

    public function updateSubjects(Request $request): RedirectResponse
    {
        $profile = $request->user()->tutorProfile;

        $data = $request->validate([
            'subjects' => ['required', 'array', 'min:1'],
            'subjects.*.id' => ['required', 'exists:subjects,id'],
            'subjects.*.level' => ['required', 'in:beginner,intermediate,advanced,expert'],
            'subjects.*.hourly_rate' => ['nullable', 'numeric', 'min:1', 'max:1000'],
        ]);

        $sync = [];
        foreach ($data['subjects'] as $subject) {
            $sync[$subject['id']] = [
                'level' => $subject['level'],
                'hourly_rate' => $subject['hourly_rate'] ?? null,
            ];
        }

        $profile->subjects()->sync($sync);

        return back()->with('status', 'Subjects updated.');
    }

    public function storeCertificate(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'issuer' => ['nullable', 'string', 'max:255'],
            'file' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
        ]);

        TutorCertificate::create([
            'tutor_profile_id' => $request->user()->tutorProfile->id,
            'title' => $data['title'],
            'issuer' => $data['issuer'] ?? null,
            'file_path' => $request->file('file')->store('certificates', 'local'),
        ]);

        return back()->with('status', 'Certificate uploaded — pending verification.');
    }

    public function destroyCertificate(Request $request, TutorCertificate $certificate): RedirectResponse
    {
        abort_unless($certificate->tutor_profile_id === $request->user()->tutorProfile->id, 403);

        Storage::disk('local')->delete($certificate->file_path);
        $certificate->delete();

        return back()->with('status', 'Certificate removed.');
    }
}
