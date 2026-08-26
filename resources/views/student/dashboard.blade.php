<x-app-layout :title="'Dashboard'">
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Welcome back, {{ auth()->user()->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Stats -->
            <div class="grid sm:grid-cols-3 gap-4">
                <x-stat-tile label="Completed sessions" :value="$stats['total_sessions']" />
                <x-stat-tile label="Hours learned" :value="number_format($stats['hours_learned'], 1)" />
                <x-stat-tile label="Upcoming sessions" :value="$stats['upcoming_count']" />
            </div>

            <div class="grid lg:grid-cols-3 gap-6">
                <div class="lg:col-span-2 space-y-6">
                    <!-- Upcoming sessions -->
                    <x-card title="Upcoming sessions" subtitle="Your next tutoring sessions">
                        <x-slot name="action">
                            <a href="{{ route('tutors.index') }}" class="text-sm font-medium text-indigo-600 dark:text-indigo-400 hover:underline">Book a session</a>
                        </x-slot>

                        @forelse ($upcomingBookings as $booking)
                            <a href="{{ route('bookings.show', $booking) }}"
                               class="flex items-center justify-between gap-4 py-3 -mx-2 px-2 rounded-md hover:bg-gray-50 dark:hover:bg-gray-700/30 {{ !$loop->last ? 'border-b border-gray-100 dark:border-gray-700' : '' }}">
                                <div class="min-w-0">
                                    <p class="font-medium text-gray-900 dark:text-gray-100 truncate">
                                        {{ $booking->subject->name ?? 'Session' }} with {{ $booking->tutor->name }}
                                    </p>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">
                                        {{ $booking->scheduled_date->format('D, M j, Y') }} · {{ substr($booking->start_time, 0, 5) }}&ndash;{{ substr($booking->end_time, 0, 5) }}
                                    </p>
                                </div>
                                <x-status-badge :status="$booking->status" />
                            </a>
                        @empty
                            <x-empty-state title="No upcoming sessions" description="Find a tutor and book your next session.">
                                <x-slot name="action">
                                    <a href="{{ route('tutors.index') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500">Find a tutor</a>
                                </x-slot>
                            </x-empty-state>
                        @endforelse
                    </x-card>

                    <!-- Pending reviews -->
                    @if ($pendingReviews->isNotEmpty())
                        <x-card title="Leave a review" subtitle="Tell us how your recent sessions went">
                            @foreach ($pendingReviews as $booking)
                                <a href="{{ route('bookings.show', $booking) }}"
                                   class="flex items-center justify-between gap-4 py-3 -mx-2 px-2 rounded-md hover:bg-gray-50 dark:hover:bg-gray-700/30 {{ !$loop->last ? 'border-b border-gray-100 dark:border-gray-700' : '' }}">
                                    <div class="min-w-0">
                                        <p class="font-medium text-gray-900 dark:text-gray-100 truncate">
                                            {{ $booking->subject->name ?? 'Session' }} with {{ $booking->tutor->name }}
                                        </p>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ $booking->scheduled_date->format('D, M j, Y') }}</p>
                                    </div>
                                    <span class="text-sm font-medium text-indigo-600 dark:text-indigo-400 shrink-0">Leave a review</span>
                                </a>
                            @endforeach
                        </x-card>
                    @endif
                </div>

                <!-- Recommended tutors -->
                <div class="lg:col-span-1 space-y-6">
                    <a href="{{ route('student.games.index') }}"
                       class="block bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-5 hover:border-indigo-300 dark:hover:border-indigo-500/50 hover:shadow-md transition">
                        <div class="flex items-center gap-3">
                            <div class="flex items-center justify-center w-11 h-11 rounded-lg bg-indigo-100 text-indigo-700 dark:bg-indigo-500/20 dark:text-indigo-300 text-xl shrink-0">🎮</div>
                            <div>
                                <p class="font-semibold text-gray-900 dark:text-gray-100">Educational Games</p>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Practice math, vocabulary & more</p>
                            </div>
                        </div>
                    </a>

                    <x-card title="Recommended tutors">
                        @if ($recommended->isEmpty())
                            <x-empty-state title="No recommendations yet" description="Browse all tutors to find the right fit.">
                                <x-slot name="action">
                                    <a href="{{ route('tutors.index') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500">Browse tutors</a>
                                </x-slot>
                            </x-empty-state>
                        @else
                            <div class="space-y-4">
                                @foreach ($recommended as $tutor)
                                    @include('tutors._card', ['tutor' => $tutor])
                                @endforeach
                            </div>
                            <a href="{{ route('tutors.index') }}" class="mt-4 block text-center text-sm font-medium text-indigo-600 dark:text-indigo-400 hover:underline">See all tutors</a>
                        @endif
                    </x-card>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
