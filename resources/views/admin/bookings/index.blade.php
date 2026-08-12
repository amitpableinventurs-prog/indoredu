<x-app-layout :title="'Bookings'">
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Bookings
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <form action="{{ route('admin.bookings.index') }}" method="GET" class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-4 grid grid-cols-1 sm:grid-cols-4 gap-3 items-end">
                <div class="sm:col-span-2">
                    <x-input-label for="q" value="Search" />
                    <x-text-input id="q" name="q" class="mt-1 block w-full" value="{{ request('q') }}" placeholder="Student or tutor name" />
                </div>
                <div>
                    <x-input-label for="status" value="Status" />
                    <select id="status" name="status" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                        <option value="">All statuses</option>
                        @foreach (['pending', 'confirmed', 'completed', 'cancelled', 'no_show'] as $s)
                            <option value="{{ $s }}" @selected(request('status') === $s)>{{ ucfirst(str_replace('_', ' ', $s)) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex justify-end gap-2">
                    <a href="{{ route('admin.bookings.index') }}" class="text-sm text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 self-center">Reset</a>
                    <x-primary-button type="submit">Filter</x-primary-button>
                </div>
            </form>

            @if ($bookings->isEmpty())
                <x-empty-state title="No bookings found" description="Try adjusting your filters." />
            @else
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead>
                                <tr class="text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    <th class="px-4 py-3">Student &rarr; Tutor</th>
                                    <th class="px-4 py-3">Subject</th>
                                    <th class="px-4 py-3">Date &amp; time</th>
                                    <th class="px-4 py-3">Status</th>
                                    <th class="px-4 py-3">Payment</th>
                                    <th class="px-4 py-3"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                @foreach ($bookings as $booking)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30">
                                        <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300 whitespace-nowrap">
                                            {{ $booking->student->name ?? '—' }} <span class="text-gray-400">&rarr;</span> {{ $booking->tutor->name ?? '—' }}
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-300 whitespace-nowrap">{{ $booking->subject->name ?? '—' }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400 whitespace-nowrap">
                                            {{ $booking->scheduled_date?->format('D, M j, Y') }} &middot; {{ substr($booking->start_time, 0, 5) }}&ndash;{{ substr($booking->end_time, 0, 5) }}
                                        </td>
                                        <td class="px-4 py-3"><x-status-badge :status="$booking->status" /></td>
                                        <td class="px-4 py-3">
                                            @if ($booking->payment)
                                                <x-status-badge :status="$booking->payment->status" />
                                            @else
                                                <span class="text-xs text-gray-400 dark:text-gray-500">No payment</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 text-right">
                                            <a href="{{ route('admin.bookings.show', $booking) }}" class="text-sm font-medium text-indigo-600 dark:text-indigo-400 hover:underline">View</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div>{{ $bookings->links() }}</div>
            @endif
        </div>
    </div>
</x-app-layout>
