<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use App\Models\SubjectCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class SubjectController extends Controller
{
    public function index(): View
    {
        $categories = SubjectCategory::withCount('subjects')->orderBy('education_level')->orderBy('board')->orderBy('name')->get();
        $subjects = Subject::with('category')->withCount('tutorProfiles')->orderBy('name')->get();

        return view('admin.subjects.index', compact('categories', 'subjects'));
    }

    public function storeCategory(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:subject_categories,name'],
            'board' => ['nullable', 'string', 'max:255'],
            'university' => ['nullable', 'string', 'max:255'],
            'education_level' => ['nullable', 'in:school,undergraduate,postgraduate'],
        ]);

        SubjectCategory::create([
            'name' => $data['name'],
            'slug' => Str::slug($data['name']),
            'board' => $data['board'] ?? null,
            'university' => $data['university'] ?? null,
            'education_level' => $data['education_level'] ?? null,
        ]);

        return back()->with('status', 'Category added.');
    }

    public function storeSubject(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'subject_category_id' => ['required', 'exists:subject_categories,id'],
            'name' => ['required', 'string', 'max:255', 'unique:subjects,name'],
            'syllabus' => ['nullable', 'string', 'max:10000'],
            'syllabus_pdf' => ['nullable', 'file', 'mimes:pdf', 'max:10240'],
        ]);

        Subject::create([
            'subject_category_id' => $data['subject_category_id'],
            'name' => $data['name'],
            'slug' => Str::slug($data['name']),
            'syllabus' => $data['syllabus'] ?? null,
            'syllabus_pdf' => $request->hasFile('syllabus_pdf')
                ? $request->file('syllabus_pdf')->store('syllabus', 'public')
                : null,
            'is_active' => true,
        ]);

        return back()->with('status', 'Subject added.');
    }

    public function toggleSubject(Subject $subject): RedirectResponse
    {
        $subject->update(['is_active' => ! $subject->is_active]);

        return back()->with('status', 'Subject updated.');
    }

    public function uploadSyllabus(Request $request, Subject $subject): RedirectResponse
    {
        $data = $request->validate([
            'syllabus_pdf' => ['required', 'file', 'mimes:pdf', 'max:10240'],
        ]);

        if ($subject->syllabus_pdf) {
            Storage::disk('public')->delete($subject->syllabus_pdf);
        }

        $subject->update(['syllabus_pdf' => $request->file('syllabus_pdf')->store('syllabus', 'public')]);

        return back()->with('status', 'Syllabus PDF uploaded.');
    }

    public function destroySyllabus(Subject $subject): RedirectResponse
    {
        if ($subject->syllabus_pdf) {
            Storage::disk('public')->delete($subject->syllabus_pdf);
            $subject->update(['syllabus_pdf' => null]);
        }

        return back()->with('status', 'Syllabus PDF removed.');
    }

    public function destroySubject(Subject $subject): RedirectResponse
    {
        if ($subject->syllabus_pdf) {
            Storage::disk('public')->delete($subject->syllabus_pdf);
        }

        $subject->delete();

        return back()->with('status', 'Subject deleted.');
    }
}
