<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Dashboard
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if ($profile->status !== 'approved')
                @if ($profile->status === 'pending')
                    <div class="rounded-lg border border-amber-200 dark:border-amber-800 bg-amber-50 dark:bg-amber-900/20 p-4 sm:p-6">
                        <div class="flex items-start gap-3">
                            <svg class="w-5 h-5 text-amber-500 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" /></svg>
                            <div>
                                <p class="font-semibold text-amber-800 dark:text-amber-300">Your tutor application is under review</p>
                                <p class="text-sm text-amber-700 dark:text-amber-400 mt-1">Our team is reviewing your profile and documents. We'll notify you as soon as a decision is made. You can still explore your dashboard in the meantime.</p>
                            </div>
                        </div>
                    </div>
                @elseif ($profile->status === 'rejected')
                    <div class="rounded-lg border border-red-200 dark:border-red-800 bg-red-50 dark:bg-red-900/20 p-4 sm:p-6">
                        <div class="flex items-start gap-3">
                            <svg class="w-5 h-5 text-red-500 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                            <div>
                                <p class="font-semibold text-red-800 dark:text-red-300">Your tutor application was rejected</p>
                                @if ($profile->rejection_reason)
                                    <p class="text-sm text-red-700 dark:text-red-400 mt-1">Reason: {{ $profile->rejection_reason }}</p>
                                @endif
                                <a href="{{ route('tutor.profile.edit') }}" class="inline-block mt-3 text-sm font-medium text-red-700 dark:text-red-300 hover:underline">Update your profile and resubmit &rarr;</a>
                            </div>
                        </div>
                    </div>
                @elseif ($profile->status === 'suspended')
                    <div class="rounded-lg border border-red-200 dark:border-red-800 bg-red-50 dark:bg-red-900/20 p-4 sm:p-6">
                        <div class="flex items-start gap-3">
                            <svg class="w-5 h-5 text-red-500 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" /></svg>
                            <div>
                                <p class="font-semibold text-red-800 dark:text-red-300">Your tutor account is suspended</p>
                                <p class="text-sm text-red-700 dark:text-red-400 mt-1">You currently can't accept new bookings or appear in search. Please contact support for more information.</p>
                            </div>
                        </div>
                    </div>
                @endif
            @endif

            @if ($stats['pending_enquiries'] > 0)
                <a href="{{ route('enquiries.index', ['status' => 'pending']) }}"
                   class="flex items-center justify-between gap-3 rounded-lg border border-amber-200 dark:border-amber-800 bg-amber-50 dark:bg-amber-950/30 px-4 py-3 hover:border-amber-300">
                    <span class="text-sm font-medium text-amber-800 dark:text-amber-300">
                        {{ $stats['pending_enquiries'] }} student {{ \Illuminate\Support\Str::plural('enquiry', $stats['pending_enquiries']) }} waiting for your reply
                    </span>
                    <span class="text-sm text-amber-700 dark:text-amber-400">View &rarr;</span>
                </a>
            @endif

            <div class="grid grid-cols-2 lg:grid-cols-5 gap-4">
                <x-stat-tile label="Total sessions" :value="$stats['total_sessions']" />
                <x-stat-tile label="Rating" :value="number_format($stats['rating_avg'], 1).' ('.$stats['rating_count'].')'" />
                <x-stat-tile label="Upcoming" :value="$stats['upcoming_count']" />
                <x-stat-tile label="Earnings this month" :value="'₹'.number_format($stats['earnings_this_month'], 2)" />
                <x-stat-tile label="Unread messages" :value="$stats['unread_messages']" />
            </div>

            <x-card title="Upcoming sessions">
                @if ($upcomingBookings->isEmpty())
                    <x-empty-state title="No upcoming sessions" description="Set your availability to start getting booked by students.">
                        <x-slot name="action">
                            <a href="{{ route('tutor.availability.index') }}" class="text-sm font-medium text-indigo-600 dark:text-indigo-400 hover:underline">Set your availability &rarr;</a>
                        </x-slot>
                    </x-empty-state>
                @else
                    <div class="divide-y divide-gray-100 dark:divide-gray-700">
                        @foreach ($upcomingBookings as $booking)
                            <a href="{{ route('bookings.show', $booking) }}" class="flex items-center justify-between gap-4 py-3 first:pt-0 last:pb-0 hover:bg-gray-50 dark:hover:bg-gray-700/40 -mx-2 px-2 rounded-md">
                                <div class="min-w-0">
                                    <p class="font-medium text-gray-900 dark:text-gray-100 truncate">{{ $booking->student->name }}</p>
                                    <p class="text-sm text-gray-500 dark:text-gray-400 truncate">{{ $booking->subject->name ?? '—' }} &middot; {{ $booking->scheduled_date->format('D, M j, Y') }} at {{ substr($booking->start_time, 0, 5) }}</p>
                                </div>
                                <x-status-badge :status="$booking->status" />
                            </a>
                        @endforeach
                    </div>
                @endif
            </x-card>

        </div>
    </div>
</x-app-layout>
