<x-app-layout :title="'Admin Dashboard'">
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Admin Dashboard
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @php
                $statTiles = [
                    ['key' => 'total_users', 'label' => 'Total Users'],
                    ['key' => 'total_students', 'label' => 'Students'],
                    ['key' => 'total_tutors', 'label' => 'Tutors'],
                    ['key' => 'pending_tutors', 'label' => 'Pending Tutor Applications', 'href' => route('admin.tutor-applications.index', ['status' => 'pending'])],
                    ['key' => 'total_bookings', 'label' => 'Total Bookings'],
                    ['key' => 'bookings_this_month', 'label' => 'Bookings This Month'],
                    ['key' => 'revenue_total', 'label' => 'Total Revenue', 'money' => true],
                    ['key' => 'revenue_this_month', 'label' => 'Revenue This Month', 'money' => true],
                    ['key' => 'platform_fees_total', 'label' => 'Platform Fees Earned', 'money' => true],
                    ['key' => 'pending_reports', 'label' => 'Pending Reports', 'href' => route('admin.reports.index')],
                    ['key' => 'flagged_reviews', 'label' => 'Flagged Reviews', 'href' => route('admin.reviews.index', ['filter' => 'flagged'])],
                    ['key' => 'avg_rating', 'label' => 'Average Rating'],
                ];
            @endphp

            <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-4">
                @foreach ($statTiles as $tile)
                    @php
                        $value = $stats[$tile['key']] ?? 0;
                        $display = ($tile['money'] ?? false)
                            ? '₹'.number_format($value, 2)
                            : ($tile['key'] === 'avg_rating' ? number_format($value, 2) : number_format($value));
                    @endphp
                    @if (isset($tile['href']))
                        <a href="{{ $tile['href'] }}" class="block rounded-lg hover:shadow-md hover:border-indigo-300 dark:hover:border-indigo-700 transition">
                            <x-stat-tile :label="$tile['label']" :value="$display" />
                        </a>
                    @else
                        <x-stat-tile :label="$tile['label']" :value="$display" />
                    @endif
                @endforeach
            </div>

            <!-- Daily trends -->
            <div class="grid lg:grid-cols-3 gap-6">
                <x-card title="New signups" subtitle="Last 30 days">
                    @if ($signupsByDay->isEmpty())
                        <x-empty-state title="No signups yet" description="New user signups will appear here." />
                    @else
                        @php $maxSignups = $signupsByDay->max('total') ?: 1; @endphp
                        <div class="flex items-end gap-1 h-28 overflow-x-auto pb-1">
                            @foreach ($signupsByDay as $row)
                                <div class="flex flex-col items-center justify-end shrink-0 w-4 h-full" title="{{ \Carbon\Carbon::parse($row->date)->format('M j, Y') }}: {{ $row->total }} signups">
                                    <div class="w-full bg-indigo-500 dark:bg-indigo-400 rounded-t" style="height: {{ max(4, round($row->total / $maxSignups * 100)) }}%"></div>
                                    <span class="text-[9px] text-gray-400 dark:text-gray-500 mt-1">{{ \Carbon\Carbon::parse($row->date)->format('j') }}</span>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </x-card>

                <x-card title="New bookings" subtitle="Last 30 days">
                    @if ($bookingsByDay->isEmpty())
                        <x-empty-state title="No bookings yet" description="New bookings will appear here." />
                    @else
                        @php $maxBookings = $bookingsByDay->max('total') ?: 1; @endphp
                        <div class="flex items-end gap-1 h-28 overflow-x-auto pb-1">
                            @foreach ($bookingsByDay as $row)
                                <div class="flex flex-col items-center justify-end shrink-0 w-4 h-full" title="{{ \Carbon\Carbon::parse($row->date)->format('M j, Y') }}: {{ $row->total }} bookings">
                                    <div class="w-full bg-emerald-500 dark:bg-emerald-400 rounded-t" style="height: {{ max(4, round($row->total / $maxBookings * 100)) }}%"></div>
                                    <span class="text-[9px] text-gray-400 dark:text-gray-500 mt-1">{{ \Carbon\Carbon::parse($row->date)->format('j') }}</span>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </x-card>

                <x-card title="Revenue" subtitle="Last 30 days">
                    @if ($revenueByDay->isEmpty())
                        <x-empty-state title="No revenue yet" description="Completed payments will appear here." />
                    @else
                        @php $maxRevenue = $revenueByDay->max('total') ?: 1; @endphp
                        <div class="flex items-end gap-1 h-28 overflow-x-auto pb-1">
                            @foreach ($revenueByDay as $row)
                                <div class="flex flex-col items-center justify-end shrink-0 w-4 h-full" title="{{ \Carbon\Carbon::parse($row->date)->format('M j, Y') }}: ₹{{ number_format($row->total, 2) }}">
                                    <div class="w-full bg-amber-500 dark:bg-amber-400 rounded-t" style="height: {{ max(4, round($row->total / $maxRevenue * 100)) }}%"></div>
                                    <span class="text-[9px] text-gray-400 dark:text-gray-500 mt-1">{{ \Carbon\Carbon::parse($row->date)->format('j') }}</span>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </x-card>
            </div>

            <!-- Top subjects / tutors -->
            <div class="grid lg:grid-cols-2 gap-6">
                <x-card title="Top subjects" subtitle="By number of bookings">
                    @if ($topSubjects->isEmpty())
                        <x-empty-state title="No booking data yet" />
                    @else
                        @php $maxSubject = $topSubjects->max('total') ?: 1; @endphp
                        <div class="space-y-3">
                            @foreach ($topSubjects as $subject)
                                <div>
                                    <div class="flex justify-between text-sm mb-1">
                                        <span class="text-gray-700 dark:text-gray-300">{{ $subject->name }}</span>
                                        <span class="text-gray-400 dark:text-gray-500">{{ $subject->total }}</span>
                                    </div>
                                    <div class="h-2 bg-gray-100 dark:bg-gray-700 rounded-full overflow-hidden">
                                        <div class="h-full bg-indigo-500 dark:bg-indigo-400 rounded-full" style="width: {{ round($subject->total / $maxSubject * 100) }}%"></div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </x-card>

                <x-card title="Top tutors" subtitle="Approved tutors by rating & sessions">
                    @if ($topTutors->isEmpty())
                        <x-empty-state title="No approved tutors yet" />
                    @else
                        <div class="divide-y divide-gray-100 dark:divide-gray-700">
                            @foreach ($topTutors as $tutor)
                                <a href="{{ route('tutors.show', $tutor) }}" class="flex items-center justify-between gap-4 py-3 -mx-2 px-2 rounded-md hover:bg-gray-50 dark:hover:bg-gray-700/30">
                                    <div class="min-w-0">
                                        <p class="font-medium text-gray-900 dark:text-gray-100 truncate">{{ $tutor->user->name ?? 'Unknown tutor' }}</p>
                                        <x-star-rating :rating="$tutor->rating_avg" :count="$tutor->rating_count" size="w-3.5 h-3.5" />
                                    </div>
                                    <span class="text-sm text-gray-500 dark:text-gray-400 shrink-0">{{ $tutor->total_sessions }} sessions</span>
                                </a>
                            @endforeach
                        </div>
                    @endif
                </x-card>
            </div>
        </div>
    </div>
</x-app-layout>
