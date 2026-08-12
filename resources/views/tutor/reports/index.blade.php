<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Reports
        </h2>
    </x-slot>

    @php
        $maxBySubject = $bySubject->max() ?: 1;
    @endphp

    <div class="py-12">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <x-card>
                <form action="{{ route('tutor.reports.index') }}" method="GET" class="grid grid-cols-2 sm:grid-cols-4 gap-3 items-end">
                    <div>
                        <x-input-label for="from" value="From" />
                        <x-text-input id="from" name="from" type="date" class="mt-1 block w-full" value="{{ $from->toDateString() }}" />
                    </div>
                    <div>
                        <x-input-label for="to" value="To" />
                        <x-text-input id="to" name="to" type="date" class="mt-1 block w-full" value="{{ $to->toDateString() }}" />
                    </div>
                    <div>
                        <x-primary-button type="submit" class="w-full justify-center">Apply</x-primary-button>
                    </div>
                    <div>
                        <a href="{{ route('tutor.reports.export', request()->only(['from', 'to'])) }}" class="inline-flex items-center justify-center w-full px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-500 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700">
                            Export CSV
                        </a>
                    </div>
                </form>
            </x-card>

            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                <x-stat-tile label="Total sessions" :value="$summary['total_sessions']" />
                <x-stat-tile label="Completed" :value="$summary['completed']" />
                <x-stat-tile label="Cancelled" :value="$summary['cancelled']" />
                <x-stat-tile label="No shows" :value="$summary['no_show']" />
                <x-stat-tile label="Unique students" :value="$summary['unique_students']" />
                <x-stat-tile label="Hours taught" :value="number_format($summary['hours_taught'], 1)" />
                <x-stat-tile label="Earnings" :value="'₹'.number_format($summary['earnings'], 2)" />
            </div>

            <x-card title="Completed sessions by subject">
                @if ($bySubject->isEmpty())
                    <p class="text-sm text-gray-500 dark:text-gray-400">No completed sessions in this period.</p>
                @else
                    <div class="space-y-3">
                        @foreach ($bySubject as $subjectName => $count)
                            <div>
                                <div class="flex items-center justify-between text-sm mb-1">
                                    <span class="text-gray-700 dark:text-gray-300">{{ $subjectName ?: 'Unknown' }}</span>
                                    <span class="text-gray-500 dark:text-gray-400">{{ $count }}</span>
                                </div>
                                <div class="h-2 rounded-full bg-gray-100 dark:bg-gray-700 overflow-hidden">
                                    <div class="h-full bg-indigo-500 dark:bg-indigo-400 rounded-full" style="width: {{ round($count / $maxBySubject * 100) }}%"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </x-card>

            <x-card title="Sessions" :subtitle="$from->format('M j, Y').' – '.$to->format('M j, Y')">
                @if ($bookings->isEmpty())
                    <x-empty-state title="No sessions in this period" description="Try widening your date range." />
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="text-left text-xs uppercase text-gray-400 dark:text-gray-500 border-b border-gray-200 dark:border-gray-700">
                                    <th class="py-2 pr-4">Date</th>
                                    <th class="py-2 pr-4">Student</th>
                                    <th class="py-2 pr-4">Subject</th>
                                    <th class="py-2 pr-4">Duration</th>
                                    <th class="py-2 pr-4">Status</th>
                                    <th class="py-2 pr-4">Attendance</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                @foreach ($bookings as $booking)
                                    <tr>
                                        <td class="py-3 pr-4 text-gray-600 dark:text-gray-300">{{ $booking->scheduled_date->format('D, M j, Y') }}</td>
                                        <td class="py-3 pr-4 font-medium text-gray-900 dark:text-gray-100">{{ $booking->student->name }}</td>
                                        <td class="py-3 pr-4 text-gray-600 dark:text-gray-300">{{ $booking->subject->name ?? '—' }}</td>
                                        <td class="py-3 pr-4 text-gray-600 dark:text-gray-300">{{ $booking->duration_minutes }} min</td>
                                        <td class="py-3 pr-4"><x-status-badge :status="$booking->status" /></td>
                                        <td class="py-3 pr-4 text-gray-600 dark:text-gray-300">
                                            @if ($booking->attendance)
                                                <x-status-badge :status="$booking->attendance->student_status ?? 'pending'" />
                                            @else
                                                <span class="text-gray-300 dark:text-gray-600">—</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </x-card>

        </div>
    </div>
</x-app-layout>
