<x-app-layout :title="'Progress Reports'">
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Progress Reports
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Date range filter -->
            <form action="{{ route('student.reports.index') }}" method="GET"
                  class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-4 flex flex-wrap items-end gap-3">
                <div>
                    <x-input-label for="from" value="From" />
                    <x-text-input id="from" name="from" type="date" class="mt-1 block w-full" value="{{ $from->toDateString() }}" />
                </div>
                <div>
                    <x-input-label for="to" value="To" />
                    <x-text-input id="to" name="to" type="date" class="mt-1 block w-full" value="{{ $to->toDateString() }}" />
                </div>
                <x-primary-button type="submit">Apply</x-primary-button>
                <a href="{{ route('student.reports.export', request()->only(['from', 'to'])) }}"
                   class="ms-auto inline-flex items-center px-4 py-2 bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-700 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest hover:bg-gray-50 dark:hover:bg-gray-800">
                    Export CSV
                </a>
            </form>

            <!-- Summary stats -->
            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
                <x-stat-tile label="Total sessions" :value="$summary['total_sessions']" />
                <x-stat-tile label="Completed" :value="$summary['completed']" />
                <x-stat-tile label="Cancelled" :value="$summary['cancelled']" />
                <x-stat-tile label="Hours learned" :value="number_format($summary['hours_learned'], 1)" />
                <x-stat-tile label="Attendance rate" :value="number_format($summary['attendance_rate'], 1).'%'" />
                <x-stat-tile label="Total spent" :value="'₹'.number_format($summary['total_spent'], 2)" />
            </div>

            <!-- Sessions by subject -->
            <x-card title="Sessions by subject" subtitle="Completed sessions in this period">
                @if ($bySubject->isEmpty())
                    <p class="text-sm text-gray-500 dark:text-gray-400">No completed sessions in this period.</p>
                @else
                    @php $maxCount = $bySubject->max(); @endphp
                    <div class="space-y-3">
                        @foreach ($bySubject as $subjectName => $count)
                            <div>
                                <div class="flex items-center justify-between text-sm mb-1">
                                    <span class="text-gray-700 dark:text-gray-300">{{ $subjectName }}</span>
                                    <span class="text-gray-500 dark:text-gray-400">{{ $count }}</span>
                                </div>
                                <div class="h-2 rounded-full bg-gray-100 dark:bg-gray-700 overflow-hidden">
                                    <div class="h-full bg-indigo-600 dark:bg-indigo-500 rounded-full" style="width: {{ $maxCount > 0 ? round($count / $maxCount * 100) : 0 }}%"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </x-card>

            <!-- Sessions table -->
            <x-card title="Session history">
                @if ($bookings->isEmpty())
                    <x-empty-state title="No sessions in this period" description="Try widening the date range." />
                @else
                    <div class="overflow-x-auto -mx-4 sm:-mx-6">
                        <table class="min-w-full text-sm">
                            <thead>
                                <tr class="text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide border-b border-gray-200 dark:border-gray-700">
                                    <th class="px-4 sm:px-6 py-2">Date</th>
                                    <th class="px-4 sm:px-6 py-2">Tutor</th>
                                    <th class="px-4 sm:px-6 py-2">Subject</th>
                                    <th class="px-4 sm:px-6 py-2">Duration</th>
                                    <th class="px-4 sm:px-6 py-2">Status</th>
                                    <th class="px-4 sm:px-6 py-2">Attendance</th>
                                    <th class="px-4 sm:px-6 py-2">Progress notes</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($bookings as $booking)
                                    <tr class="border-b border-gray-100 dark:border-gray-700 last:border-0">
                                        <td class="px-4 sm:px-6 py-3 whitespace-nowrap text-gray-900 dark:text-gray-100">{{ $booking->scheduled_date->format('M j, Y') }}</td>
                                        <td class="px-4 sm:px-6 py-3 whitespace-nowrap text-gray-700 dark:text-gray-300">{{ $booking->tutor->name }}</td>
                                        <td class="px-4 sm:px-6 py-3 whitespace-nowrap text-gray-700 dark:text-gray-300">{{ $booking->subject->name ?? '—' }}</td>
                                        <td class="px-4 sm:px-6 py-3 whitespace-nowrap text-gray-700 dark:text-gray-300">{{ $booking->duration_minutes }} min</td>
                                        <td class="px-4 sm:px-6 py-3 whitespace-nowrap"><x-status-badge :status="$booking->status" /></td>
                                        <td class="px-4 sm:px-6 py-3 whitespace-nowrap text-gray-700 dark:text-gray-300">
                                            {{ $booking->attendance?->student_status ? ucfirst(str_replace('_', ' ', $booking->attendance->student_status)) : '—' }}
                                        </td>
                                        <td class="px-4 sm:px-6 py-3 text-gray-700 dark:text-gray-300 max-w-xs truncate" title="{{ $booking->tutor_notes }}">
                                            {{ $booking->tutor_notes ?: '—' }}
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
