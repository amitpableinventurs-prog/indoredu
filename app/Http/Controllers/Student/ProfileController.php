<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function edit(Request $request): View
    {
        $profile = $request->user()->studentProfile;
        $subjects = Subject::with('category')->where('is_active', true)->orderBy('name')->get();
        $mySubjectIds = $profile->subjects()->pluck('subjects.id')->all();

        return view('student.profile', compact('profile', 'subjects', 'mySubjectIds'));
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'grade_level' => ['nullable', 'string', 'max:100'],
            'bio' => ['nullable', 'string', 'max:2000'],
            'learning_goals' => ['nullable', 'string', 'max:2000'],
            'guardian_name' => ['nullable', 'string', 'max:255'],
            'guardian_email' => ['nullable', 'email', 'max:255'],
            'guardian_phone' => ['nullable', 'string', 'max:30'],
            'subjects' => ['nullable', 'array'],
            'subjects.*' => ['exists:subjects,id'],
        ]);

        $profile = $request->user()->studentProfile;
        $profile->update(collect($data)->except('subjects')->all());
        $profile->subjects()->sync($data['subjects'] ?? []);

        return back()->with('status', 'Profile updated.');
    }
}
