<x-app-layout :title="'Enquiries'">
    @php $me = auth()->user(); @endphp
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-2">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ $me->isTutor() ? 'Student Enquiries' : ($me->isAdmin() ? 'All Enquiries' : 'My Enquiries') }}
            </h2>
            @if ($me->isStudent())
                <a href="{{ route('tutors.index') }}" class="text-sm text-indigo-600 dark:text-indigo-400 hover:underline">Find a tutor to ask &rarr;</a>
            @endif
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-6 flex flex-wrap gap-2">
                @foreach (['open' => 'Open', 'pending' => 'Awaiting reply', 'replied' => 'Replied', 'closed' => 'Closed', 'all' => 'All'] as $key => $label)
                    <a href="{{ route('enquiries.index', ['status' => $key]) }}"
                       class="px-3 py-1.5 rounded-md text-sm font-medium {{ $status === $key ? 'bg-indigo-600 text-white' : 'bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-300 border border-gray-200 dark:border-gray-700 hover:border-indigo-300 dark:hover:border-indigo-700' }}">
                        {{ $label }}
                    </a>
                @endforeach
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
                @forelse ($enquiries as $enquiry)
                    @php
                        $other = $me->isTutor() ? $enquiry->student : $enquiry->tutor;
                        $needsAction = $me->id === $enquiry->tutor_id && $enquiry->status === \App\Models\Enquiry::STATUS_PENDING;
                    @endphp
                    <a href="{{ route('enquiries.show', $enquiry) }}"
                       class="flex items-start gap-4 p-4 sm:px-6 hover:bg-gray-50 dark:hover:bg-gray-700/30 {{ !$loop->last ? 'border-b border-gray-100 dark:border-gray-700' : '' }}">
                        <div class="w-10 h-10 rounded-full bg-indigo-100 dark:bg-indigo-900/40 text-indigo-700 dark:text-indigo-300 flex items-center justify-center font-semibold text-sm shrink-0">
                            {{ $other?->initials() ?? '?' }}
                        </div>
                        <div class="min-w-0 flex-1">
                            <div class="flex flex-wrap items-center gap-2">
                                <p class="text-gray-900 dark:text-gray-100 truncate {{ $needsAction ? 'font-bold' : 'font-medium' }}">{{ $enquiry->title }}</p>
                                <x-status-badge :status="$enquiry->status" />
                            </div>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">
                                @if ($me->isAdmin())
                                    {{ $enquiry->student?->name }} &rarr; {{ $enquiry->tutor?->name }}
                                @else
                                    {{ $me->isTutor() ? 'From' : 'To' }} {{ $other?->name ?? 'Deleted user' }}
                                @endif
                                @if ($enquiry->subject) &middot; {{ $enquiry->subject->name }} @endif
                                @if ($enquiry->grade) &middot; {{ \App\Models\Course::GRADES[$enquiry->grade] ?? $enquiry->grade }} @endif
                            </p>
                            <p class="text-sm text-gray-600 dark:text-gray-300 mt-1 truncate">{{ \Illuminate\Support\Str::limit($enquiry->message, 120) }}</p>
                        </div>
                        <span class="text-xs text-gray-400 dark:text-gray-500 whitespace-nowrap shrink-0">{{ $enquiry->created_at->diffForHumans() }}</span>
                    </a>
                @empty
                    <x-empty-state title="No enquiries here"
                        :description="$me->isTutor() ? 'When students send you questions, they will show up here.' : 'Have a question for a tutor? Open their profile and click “Send enquiry”.'" />
                @endforelse
            </div>

            @if ($enquiries->hasPages())
                <div class="mt-6">{{ $enquiries->links() }}</div>
            @endif
        </div>
    </div>
</x-app-layout>
