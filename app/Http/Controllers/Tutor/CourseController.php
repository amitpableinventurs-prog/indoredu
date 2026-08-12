<?php

namespace App\Http\Controllers\Tutor;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\SubjectCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CourseController extends Controller
{
    public function index(Request $request): View
    {
        $courses = $request->user()->tutorProfile->courses()->withCount('enrollments')->latest()->get();

        return view('tutor.courses.index', compact('courses'));
    }

    public function create(Request $request): View
    {
        $categories = $this->activeCategories();

        return view('tutor.courses.create', compact('categories'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateCourse($request);

        $data['tutor_profile_id'] = $request->user()->tutorProfile->id;
        $data['slug'] = $this->uniqueSlug($data['title']);
        $data['status'] = Course::STATUS_PENDING;

        if ($request->hasFile('cover_image')) {
            $data['cover_image'] = $request->file('cover_image')->store('course-covers', 'public');
        }

        $course = Course::create($data);

        return redirect()->route('tutor.courses.index')->with('status', "\"{$course->title}\" submitted for review.");
    }

    public function edit(Request $request, Course $course): View
    {
        $this->authorize('update', $course);
        $categories = $this->activeCategories();

        return view('tutor.courses.edit', compact('course', 'categories'));
    }

    public function update(Request $request, Course $course): RedirectResponse
    {
        $this->authorize('update', $course);

        $data = $this->validateCourse($request, $course);

        if ($request->hasFile('cover_image')) {
            if ($course->cover_image) {
                Storage::disk('public')->delete($course->cover_image);
            }
            $data['cover_image'] = $request->file('cover_image')->store('course-covers', 'public');
        }

        // Substantive edits to a published course send it back through moderation.
        if ($course->status === Course::STATUS_PUBLISHED) {
            $data['status'] = Course::STATUS_PENDING;
        }

        $course->update($data);

        return redirect()->route('tutor.courses.index')->with('status', 'Course updated.');
    }

    public function destroy(Request $request, Course $course): RedirectResponse
    {
        $this->authorize('delete', $course);
        $course->delete();

        return back()->with('status', 'Course deleted.');
    }

    protected function validateCourse(Request $request, ?Course $course = null): array
    {
        return $request->validate([
            'subject_id' => ['required', 'exists:subjects,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000'],
            'level' => ['required', 'in:beginner,intermediate,advanced,all_levels'],
            'price' => ['required', 'numeric', 'min:0', 'max:10000'],
            'duration_minutes' => ['required', 'integer', 'in:30,60,90,120'],
            'total_sessions' => ['required', 'integer', 'min:1', 'max:100'],
            'is_group' => ['sometimes', 'boolean'],
            'max_students' => ['required', 'integer', 'min:1', 'max:100'],
            'cover_image' => ['nullable', 'image', 'max:2048'],
        ]) + ['is_group' => $request->boolean('is_group')];
    }

    protected function activeCategories()
    {
        return SubjectCategory::with(['subjects' => fn ($q) => $q->where('is_active', true)->orderBy('name')])
            ->orderBy('education_level')
            ->orderBy('board')
            ->orderBy('name')
            ->get()
            ->filter(fn ($category) => $category->subjects->isNotEmpty());
    }

    protected function uniqueSlug(string $title): string
    {
        $base = Str::slug($title);
        $slug = $base;
        $i = 1;
        while (Course::where('slug', $slug)->exists()) {
            $slug = "{$base}-".$i++;
        }

        return $slug;
    }
}
