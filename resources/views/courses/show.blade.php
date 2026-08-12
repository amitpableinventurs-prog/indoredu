<x-public-layout :title="$course->title">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-10 grid md:grid-cols-3 gap-8">
        <div class="md:col-span-2 space-y-6">
            <div>
                <p class="text-sm text-indigo-600 dark:text-indigo-400 font-medium">{{ $course->subject->name ?? '' }} · {{ ucfirst(str_replace('_', ' ', $course->level)) }}</p>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mt-1">{{ $course->title }}</h1>
                <a href="{{ route('tutors.show', $course->tutorProfile) }}" class="text-sm text-gray-500 dark:text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400 mt-1 inline-block">
                    by {{ $course->tutorProfile->user->name }}
                </a>
            </div>

            @if ($course->cover_image)
                <img src="{{ asset('storage/'.$course->cover_image) }}" class="w-full rounded-lg" alt="{{ $course->title }}">
            @endif

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <h2 class="font-semibold text-gray-900 dark:text-gray-100 mb-2">About this course</h2>
                <p class="text-sm text-gray-600 dark:text-gray-300 whitespace-pre-line">{{ $course->description ?: 'No description provided.' }}</p>
            </div>

            @if ($course->subject && ($course->subject->syllabus_pdf || $course->subject->syllabus))
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <h2 class="font-semibold text-gray-900 dark:text-gray-100 mb-2">Syllabus</h2>
                    @if ($course->subject->syllabus_pdf)
                        <a href="{{ asset('storage/'.$course->subject->syllabus_pdf) }}" target="_blank" rel="noopener"
                           class="inline-flex items-center gap-2 text-sm font-medium text-indigo-600 dark:text-indigo-400 hover:underline">
                            View syllabus PDF
                        </a>
                    @endif
                    @if ($course->subject->syllabus)
                        <p class="text-sm text-gray-600 dark:text-gray-300 whitespace-pre-line mt-3">{{ $course->subject->syllabus }}</p>
                    @endif
                </div>
            @endif
        </div>

        <div class="md:col-span-1">
            <div class="sticky top-6 bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">₹{{ number_format($course->price, 0) }}</p>
                <ul class="mt-4 space-y-2 text-sm text-gray-600 dark:text-gray-300">
                    <li>{{ $course->total_sessions }} session{{ $course->total_sessions > 1 ? 's' : '' }}</li>
                    <li>{{ $course->duration_minutes }} minutes each</li>
                    <li>{{ $course->is_group ? 'Group course (max '.$course->max_students.' students)' : '1-on-1' }}</li>
                </ul>

                @auth
                    @if (auth()->user()->isStudent())
                        <form action="{{ route('courses.enroll', $course) }}" method="POST" class="mt-5">
                            @csrf
                            <x-primary-button type="submit" class="w-full justify-center">Enroll now</x-primary-button>
                        </form>
                    @else
                        <p class="mt-5 text-sm text-gray-500 dark:text-gray-400">Only student accounts can enroll.</p>
                    @endif
                @else
                    <a href="{{ route('login') }}" class="mt-5 block text-center w-full px-4 py-2 bg-indigo-600 rounded-md font-semibold text-sm text-white uppercase tracking-widest hover:bg-indigo-500">
                        Log in to enroll
                    </a>
                @endauth
            </div>
        </div>
    </div>
</x-public-layout>
