<x-app-layout :title="'Courses'">
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Course Moderation
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @php $tabs = ['pending' => 'Pending', 'published' => 'Published', 'rejected' => 'Rejected', 'archived' => 'Archived', 'all' => 'All']; @endphp
            <div class="flex flex-wrap gap-2">
                @foreach ($tabs as $key => $label)
                    <a href="{{ route('admin.courses.index', ['status' => $key]) }}"
                       class="px-3 py-1.5 rounded-full text-sm font-medium {{ $status === $key ? 'bg-indigo-600 text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600' }}">
                        {{ $label }}
                    </a>
                @endforeach
            </div>

            @if ($courses->isEmpty())
                <x-empty-state title="No courses found" description="There are no courses matching this filter." />
            @else
                <div class="space-y-4">
                    @foreach ($courses as $course)
                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4 sm:p-6">
                            <div class="flex flex-wrap items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <h3 class="font-semibold text-gray-900 dark:text-gray-100">{{ $course->title }}</h3>
                                        <x-status-badge :status="$course->status" />
                                    </div>
                                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                        by {{ $course->tutorProfile->user->name ?? 'Unknown tutor' }} &middot; {{ $course->subject->name ?? 'No subject' }}
                                    </p>
                                    @if ($course->status === 'rejected' && $course->rejection_reason)
                                        <p class="text-sm text-red-600 dark:text-red-400 mt-1">Rejected: {{ $course->rejection_reason }}</p>
                                    @endif
                                </div>
                                <p class="font-semibold text-gray-900 dark:text-gray-100 shrink-0">₹{{ number_format($course->price, 2) }}</p>
                            </div>

                            <div class="mt-4 flex flex-wrap items-center gap-2">
                                @if ($course->status === 'pending')
                                    <form action="{{ route('admin.courses.approve', $course) }}" method="POST"
                                          onsubmit="return confirm('Publish this course?');">
                                        @csrf
                                        <x-primary-button type="submit">Approve</x-primary-button>
                                    </form>

                                    <details class="inline-block">
                                        <summary class="list-none">
                                            <span class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-500 cursor-pointer select-none">Reject</span>
                                        </summary>
                                        <form action="{{ route('admin.courses.reject', $course) }}" method="POST" class="mt-3 max-w-sm space-y-2">
                                            @csrf
                                            <textarea name="rejection_reason" rows="2" required maxlength="500"
                                                      class="block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                                                      placeholder="Reason for rejection"></textarea>
                                            <x-danger-button type="submit">Confirm reject</x-danger-button>
                                        </form>
                                    </details>
                                @endif

                                @if ($course->status !== 'archived')
                                    <form action="{{ route('admin.courses.archive', $course) }}" method="POST"
                                          onsubmit="return confirm('Archive this course? It will no longer be visible to students.');">
                                        @csrf
                                        <x-secondary-button type="submit">Archive</x-secondary-button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
                <div>{{ $courses->links() }}</div>
            @endif
        </div>
    </div>
</x-app-layout>
