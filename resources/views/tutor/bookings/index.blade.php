<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Bookings
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-6 flex flex-wrap gap-2">
                @foreach (['upcoming' => 'Upcoming', 'completed' => 'Completed', 'cancelled' => 'Cancelled', 'all' => 'All'] as $key => $label)
                    <a href="{{ route('tutor.bookings.index', ['status' => $key]) }}"
                       class="px-3 py-1.5 rounded-md text-sm font-medium {{ $status === $key ? 'bg-indigo-600 text-white' : 'bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-300 border border-gray-200 dark:border-gray-700 hover:border-indigo-300 dark:hover:border-indigo-700' }}">
                        {{ $label }}
                    </a>
                @endforeach
            </div>

            <x-card>
                @if ($bookings->isEmpty())
                    <x-empty-state title="No bookings found" description="Bookings matching this filter will show up here." />
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="text-left text-xs uppercase text-gray-400 dark:text-gray-500 border-b border-gray-200 dark:border-gray-700">
                                    <th class="py-2 pr-4">Student</th>
                                    <th class="py-2 pr-4">Subject</th>
                                    <th class="py-2 pr-4">Date</th>
                                    <th class="py-2 pr-4">Time</th>
                                    <th class="py-2 pr-4">Price</th>
                                    <th class="py-2 pr-4">Status</th>
                                    <th class="py-2 pr-4">Review</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                @foreach ($bookings as $booking)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/40 cursor-pointer" onclick="window.location='{{ route('bookings.show', $booking) }}'">
                                        <td class="py-3 pr-4 font-medium text-gray-900 dark:text-gray-100">{{ $booking->student->name }}</td>
                                        <td class="py-3 pr-4 text-gray-600 dark:text-gray-300">{{ $booking->subject->name ?? '—' }}</td>
                                        <td class="py-3 pr-4 text-gray-600 dark:text-gray-300">{{ $booking->scheduled_date->format('D, M j, Y') }}</td>
                                        <td class="py-3 pr-4 text-gray-600 dark:text-gray-300">{{ substr($booking->start_time, 0, 5) }} – {{ substr($booking->end_time, 0, 5) }}</td>
                                        <td class="py-3 pr-4 text-gray-600 dark:text-gray-300">₹{{ number_format($booking->price, 2) }}</td>
                                        <td class="py-3 pr-4"><x-status-badge :status="$booking->status" /></td>
                                        <td class="py-3 pr-4 text-gray-600 dark:text-gray-300">
                                            @if ($booking->review)
                                                <x-star-rating :rating="$booking->review->rating" size="w-3.5 h-3.5" />
                                            @else
                                                <span class="text-gray-300 dark:text-gray-600">—</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-6">{{ $bookings->links() }}</div>
                @endif
            </x-card>
        </div>
    </div>
</x-app-layout>
