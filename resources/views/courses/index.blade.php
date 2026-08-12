<x-public-layout :title="'Courses'">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-6">Courses</h1>

        <form action="{{ route('courses.index') }}" method="GET" class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-4 mb-8 grid grid-cols-1 sm:grid-cols-4 gap-3 items-end">
            <div class="sm:col-span-2">
                <x-input-label for="q" value="Search" />
                <x-text-input id="q" name="q" class="mt-1 block w-full" value="{{ request('q') }}" placeholder="Course title" />
            </div>
            <div>
                <x-input-label for="subject" value="Subject" />
                <select id="subject" name="subject" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                    <option value="">All subjects</option>
                    @foreach ($categories as $category)
                        <optgroup label="{{ $category->name }}">
                            @foreach ($category->subjects as $subject)
                                <option value="{{ $subject->slug }}" @selected(request('subject') === $subject->slug)>{{ $subject->name }}</option>
                            @endforeach
                        </optgroup>
                    @endforeach
                </select>
            </div>
            <div>
                <x-primary-button type="submit" class="w-full justify-center">Search</x-primary-button>
            </div>
        </form>

        @if ($courses->isEmpty())
            <x-empty-state title="No courses found" description="Try a different search or check back soon." />
        @else
            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach ($courses as $course)
                    <a href="{{ route('courses.show', $course) }}" class="block bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 hover:shadow-md hover:border-indigo-300 dark:hover:border-indigo-700 transition overflow-hidden">
                        <div class="h-32 bg-indigo-50 dark:bg-indigo-950/40 flex items-center justify-center">
                            @if ($course->cover_image)
                                <img src="{{ asset('storage/'.$course->cover_image) }}" class="w-full h-full object-cover" alt="{{ $course->title }}">
                            @else
                                <span class="text-indigo-300 dark:text-indigo-700 text-3xl font-bold">{{ substr($course->title, 0, 1) }}</span>
                            @endif
                        </div>
                        <div class="p-4">
                            <p class="text-xs text-gray-400 dark:text-gray-500">{{ $course->subject->name ?? '' }} · {{ ucfirst(str_replace('_', ' ', $course->level)) }}</p>
                            <h3 class="font-semibold text-gray-900 dark:text-gray-100 mt-0.5">{{ $course->title }}</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">by {{ $course->tutorProfile->user->name }}</p>
                            <div class="mt-3 flex items-center justify-between">
                                <span class="font-semibold text-gray-900 dark:text-gray-100">₹{{ number_format($course->price, 0) }}</span>
                                <span class="text-xs text-gray-400">{{ $course->enrollments_count }} enrolled</span>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
            <div class="mt-8">{{ $courses->links() }}</div>
        @endif
    </div>
</x-public-layout>
