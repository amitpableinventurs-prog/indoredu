<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-3">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                My Courses
            </h2>
            <a href="{{ route('tutor.courses.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                Create course
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            <x-card title="Your courses">
                @if ($courses->isEmpty())
                    <x-empty-state title="You haven't created any courses yet" description="Courses let students enroll in a structured series of sessions.">
                        <x-slot name="action">
                            <a href="{{ route('tutor.courses.create') }}" class="text-sm font-medium text-indigo-600 dark:text-indigo-400 hover:underline">Create your first course &rarr;</a>
                        </x-slot>
                    </x-empty-state>
                @else
                    <div class="space-y-4">
                        @foreach ($courses as $course)
                            <div class="border border-gray-200 dark:border-gray-700 rounded-md p-4">
                                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                                    <div class="min-w-0">
                                        <div class="flex items-center gap-2 flex-wrap">
                                            <p class="font-semibold text-gray-900 dark:text-gray-100">{{ $course->title }}</p>
                                            <x-status-badge :status="$course->status" />
                                        </div>
                                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">{{ $course->subject?->name ?? '—' }} &middot; ₹{{ number_format($course->price, 2) }} &middot; {{ $course->enrollments_count }} enrolled</p>
                                        @if ($course->status === 'rejected' && $course->rejection_reason)
                                            <p class="text-sm text-red-600 dark:text-red-400 mt-1">Reason: {{ $course->rejection_reason }}</p>
                                        @endif
                                    </div>
                                    <div class="flex items-center gap-3 shrink-0">
                                        <a href="{{ route('tutor.courses.edit', $course) }}" class="text-sm text-indigo-600 dark:text-indigo-400 hover:underline">Edit</a>
                                        <form action="{{ route('tutor.courses.destroy', $course) }}" method="POST" onsubmit="return confirm('Delete this course? This cannot be undone.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-sm text-gray-400 hover:text-red-600 dark:hover:text-red-400">Delete</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </x-card>
        </div>
    </div>
</x-app-layout>
