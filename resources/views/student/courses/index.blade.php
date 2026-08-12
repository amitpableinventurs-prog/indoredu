<x-app-layout :title="'My Courses'">
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            My Courses
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if ($enrollments->isEmpty())
                <x-empty-state title="You're not enrolled in any courses" description="Browse our course catalog to find something to learn.">
                    <x-slot name="action">
                        <a href="{{ route('courses.index') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500">Browse courses</a>
                    </x-slot>
                </x-empty-state>
            @else
                <div class="space-y-4">
                    @foreach ($enrollments as $enrollment)
                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4 sm:p-6 flex flex-col sm:flex-row sm:items-center gap-4">
                            <div class="min-w-0 flex-1">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <h3 class="font-semibold text-gray-900 dark:text-gray-100">{{ $enrollment->course->title }}</h3>
                                    <x-status-badge :status="$enrollment->status" />
                                </div>
                                <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">
                                    {{ $enrollment->course->subject->name ?? '' }} · by {{ $enrollment->course->tutorProfile->user->name }}
                                </p>
                                <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Enrolled {{ $enrollment->enrolled_at->format('D, M j, Y') }}</p>
                            </div>
                            <div class="flex items-center gap-3 shrink-0">
                                <a href="{{ route('courses.show', $enrollment->course) }}" class="text-sm font-medium text-indigo-600 dark:text-indigo-400 hover:underline">View course</a>
                                @if ($enrollment->status === 'active')
                                    <form action="{{ route('student.enrollments.destroy', $enrollment) }}" method="POST"
                                          onsubmit="return confirm('Cancel your enrollment in this course? This cannot be undone.');">
                                        @csrf
                                        @method('DELETE')
                                        <x-danger-button type="submit">Cancel enrollment</x-danger-button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>

                @if ($enrollments->hasPages())
                    <div class="mt-6">{{ $enrollments->links() }}</div>
                @endif
            @endif
        </div>
    </div>
</x-app-layout>
