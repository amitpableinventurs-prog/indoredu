<x-app-layout :title="'My Bookings'">
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            My Bookings
        </h2>
    </x-slot>

    @php
        $tabs = ['upcoming' => 'Upcoming', 'completed' => 'Completed', 'cancelled' => 'Cancelled', 'all' => 'All'];
    @endphp

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="flex gap-1 border-b border-gray-200 dark:border-gray-700 mb-6 overflow-x-auto">
                @foreach ($tabs as $key => $label)
                    <a href="{{ route('student.bookings.index', ['status' => $key]) }}"
                       class="px-4 py-2 text-sm font-medium border-b-2 -mb-px whitespace-nowrap {{ $status === $key ? 'border-indigo-600 text-indigo-600 dark:text-indigo-400 dark:border-indigo-400' : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300' }}">
                        {{ $label }}
                    </a>
                @endforeach
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
                @forelse ($bookings as $booking)
                    <a href="{{ route('bookings.show', $booking) }}"
                       class="flex flex-col sm:flex-row sm:items-center gap-2 sm:gap-4 p-4 sm:px-6 hover:bg-gray-50 dark:hover:bg-gray-700/30 {{ !$loop->last ? 'border-b border-gray-100 dark:border-gray-700' : '' }}">
                        <div class="min-w-0 flex-1">
                            <p class="font-medium text-gray-900 dark:text-gray-100 truncate">
                                {{ $booking->subject->name ?? 'Session' }} with {{ $booking->tutor->name }}
                            </p>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                {{ $booking->scheduled_date->format('D, M j, Y') }} · {{ substr($booking->start_time, 0, 5) }}&ndash;{{ substr($booking->end_time, 0, 5) }}
                            </p>
                            @if ($booking->status === \App\Models\Booking::STATUS_COMPLETED && $booking->review)
                                <p class="text-xs text-green-600 dark:text-green-400 mt-0.5">Review submitted</p>
                            @endif
                        </div>
                        <div class="flex items-center gap-4 sm:gap-6 shrink-0">
                            <span class="text-sm font-semibold text-gray-900 dark:text-gray-100">₹{{ number_format($booking->price, 2) }}</span>
                            <x-status-badge :status="$booking->status" />
                        </div>
                    </a>
                @empty
                    <x-empty-state
                        :title="match($status) {
                            'completed' => 'No completed sessions yet',
                            'cancelled' => 'No cancelled sessions',
                            'all' => 'No bookings yet',
                            default => 'No upcoming sessions',
                        }"
                        description="Browse tutors to book your next session.">
                        <x-slot name="action">
                            <a href="{{ route('tutors.index') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500">Find a tutor</a>
                        </x-slot>
                    </x-empty-state>
                @endforelse
            </div>

            @if ($bookings->hasPages())
                <div class="mt-6">{{ $bookings->links() }}</div>
            @endif
        </div>
    </div>
</x-app-layout>
