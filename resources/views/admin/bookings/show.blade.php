<x-app-layout :title="'Booking #'.$booking->id">
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Booking #{{ $booking->id }}
            </h2>
            <a href="{{ route('admin.bookings.index') }}" class="text-sm text-gray-500 hover:text-gray-700 dark:hover:text-gray-300">&larr; Back to bookings</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Overview -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <div class="flex flex-wrap items-start justify-between gap-3">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            {{ $booking->student->name ?? 'Unknown student' }} <span class="text-gray-400">&rarr;</span> {{ $booking->tutor->name ?? 'Unknown tutor' }}
                        </p>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 mt-0.5">{{ $booking->subject->name ?? 'Session' }}</h3>
                    </div>
                    <x-status-badge :status="$booking->status" />
                </div>

                <div class="mt-4 grid sm:grid-cols-3 gap-4 text-sm">
                    <div>
                        <p class="text-gray-400 dark:text-gray-500">Date &amp; time</p>
                        <p class="text-gray-800 dark:text-gray-200">
                            {{ $booking->scheduled_date?->format('D, M j, Y') }}<br>
                            {{ substr($booking->start_time, 0, 5) }}&ndash;{{ substr($booking->end_time, 0, 5) }} ({{ $booking->timezone }})
                        </p>
                    </div>
                    <div>
                        <p class="text-gray-400 dark:text-gray-500">Duration</p>
                        <p class="text-gray-800 dark:text-gray-200">{{ $booking->duration_minutes }} minutes {{ $booking->is_trial ? '(trial)' : '' }}</p>
                    </div>
                    <div>
                        <p class="text-gray-400 dark:text-gray-500">Price</p>
                        <p class="text-gray-800 dark:text-gray-200">₹{{ number_format($booking->price, 2) }}</p>
                    </div>
                </div>

                @if ($booking->student_notes)
                    <div class="mt-4">
                        <p class="text-gray-400 dark:text-gray-500 text-sm">Student notes</p>
                        <p class="text-sm text-gray-700 dark:text-gray-300 whitespace-pre-line">{{ $booking->student_notes }}</p>
                    </div>
                @endif

                @if ($booking->tutor_notes)
                    <div class="mt-4">
                        <p class="text-gray-400 dark:text-gray-500 text-sm">Tutor notes</p>
                        <p class="text-sm text-gray-700 dark:text-gray-300 whitespace-pre-line">{{ $booking->tutor_notes }}</p>
                    </div>
                @endif

                @if ($booking->status === 'cancelled')
                    <div class="mt-4 p-3 rounded-md bg-gray-50 dark:bg-gray-900/40 text-sm">
                        <p class="text-gray-500 dark:text-gray-400">
                            Cancelled {{ $booking->cancelled_at?->diffForHumans() }}
                            @if ($booking->cancelled_by)
                                by {{ \App\Models\User::find($booking->cancelled_by)?->name ?? 'unknown user' }}
                            @endif
                        </p>
                        @if ($booking->cancellation_reason)
                            <p class="text-gray-700 dark:text-gray-300 mt-1">Reason: {{ $booking->cancellation_reason }}</p>
                        @endif
                    </div>
                @endif
            </div>

            <!-- Attendance -->
            <x-card title="Attendance">
                @if ($booking->attendance)
                    <div class="grid sm:grid-cols-2 gap-4 text-sm">
                        <div>
                            <p class="text-gray-400 dark:text-gray-500">Student</p>
                            <p class="text-gray-800 dark:text-gray-200 capitalize">{{ str_replace('_', ' ', $booking->attendance->student_status ?? '—') }}</p>
                            @if ($booking->attendance->student_check_in)
                                <p class="text-xs text-gray-400 dark:text-gray-500">Checked in {{ $booking->attendance->student_check_in->format('D, M j, Y g:ia') }}</p>
                            @endif
                        </div>
                        <div>
                            <p class="text-gray-400 dark:text-gray-500">Tutor</p>
                            <p class="text-gray-800 dark:text-gray-200 capitalize">{{ str_replace('_', ' ', $booking->attendance->tutor_status ?? '—') }}</p>
                            @if ($booking->attendance->tutor_check_in)
                                <p class="text-xs text-gray-400 dark:text-gray-500">Checked in {{ $booking->attendance->tutor_check_in->format('D, M j, Y g:ia') }}</p>
                            @endif
                        </div>
                        @if ($booking->attendance->notes)
                            <div class="sm:col-span-2">
                                <p class="text-gray-400 dark:text-gray-500">Notes</p>
                                <p class="text-gray-700 dark:text-gray-300 whitespace-pre-line">{{ $booking->attendance->notes }}</p>
                            </div>
                        @endif
                    </div>
                @else
                    <p class="text-sm text-gray-500 dark:text-gray-400">No attendance record for this session.</p>
                @endif
            </x-card>

            <!-- Review -->
            <x-card title="Review">
                @if ($booking->review)
                    <div class="flex items-center gap-2">
                        <x-star-rating :rating="$booking->review->rating" size="w-4 h-4" />
                        @if ($booking->review->is_flagged)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 dark:bg-red-900/40 text-red-700 dark:text-red-300">Flagged</span>
                        @endif
                        @if (! $booking->review->is_approved)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300">Hidden</span>
                        @endif
                    </div>
                    @if ($booking->review->comment)
                        <p class="text-sm text-gray-700 dark:text-gray-300 mt-2">{{ $booking->review->comment }}</p>
                    @endif
                    @if ($booking->review->tutor_response)
                        <div class="mt-2 ml-4 pl-3 border-l-2 border-indigo-200 dark:border-indigo-800 text-sm text-gray-500 dark:text-gray-400">
                            <span class="font-medium">Tutor response:</span> {{ $booking->review->tutor_response }}
                        </div>
                    @endif
                @else
                    <p class="text-sm text-gray-500 dark:text-gray-400">No review has been left for this session.</p>
                @endif
            </x-card>

            <!-- Payment -->
            <x-card title="Payment">
                @if ($booking->payment)
                    <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-4 text-sm">
                        <div>
                            <p class="text-gray-400 dark:text-gray-500">Amount</p>
                            <p class="text-gray-800 dark:text-gray-200">{{ strtoupper($booking->payment->currency) }} {{ number_format($booking->payment->amount, 2) }}</p>
                        </div>
                        <div>
                            <p class="text-gray-400 dark:text-gray-500">Platform fee</p>
                            <p class="text-gray-800 dark:text-gray-200">{{ number_format($booking->payment->platform_fee, 2) }}</p>
                        </div>
                        <div>
                            <p class="text-gray-400 dark:text-gray-500">Net amount</p>
                            <p class="text-gray-800 dark:text-gray-200">{{ number_format($booking->payment->net_amount, 2) }}</p>
                        </div>
                        <div>
                            <p class="text-gray-400 dark:text-gray-500">Gateway</p>
                            <p class="text-gray-800 dark:text-gray-200 capitalize">{{ $booking->payment->gateway }}</p>
                        </div>
                        <div>
                            <p class="text-gray-400 dark:text-gray-500">Status</p>
                            <x-status-badge :status="$booking->payment->status" />
                        </div>
                        <div>
                            <p class="text-gray-400 dark:text-gray-500">Paid</p>
                            <p class="text-gray-800 dark:text-gray-200">{{ $booking->payment->paid_at?->format('D, M j, Y') ?? '—' }}</p>
                        </div>
                    </div>
                @else
                    <p class="text-sm text-gray-500 dark:text-gray-400">No payment recorded for this session.</p>
                @endif
            </x-card>

            <!-- Message thread -->
            <x-card title="Message thread" subtitle="Read-only view of the conversation between student and tutor">
                @if (! $booking->conversation || $booking->conversation->messages->isEmpty())
                    <x-empty-state title="No messages" description="Student and tutor haven't exchanged any messages for this booking." />
                @else
                    <div class="space-y-4 max-h-[32rem] overflow-y-auto">
                        @foreach ($booking->conversation->messages as $message)
                            <div class="flex gap-3">
                                <div class="w-8 h-8 rounded-full bg-indigo-100 dark:bg-indigo-900/40 text-indigo-700 dark:text-indigo-300 flex items-center justify-center text-xs font-semibold shrink-0">
                                    {{ $message->sender?->initials() ?? '?' }}
                                </div>
                                <div class="min-w-0 flex-1">
                                    <div class="flex items-baseline gap-2">
                                        <span class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $message->sender->name ?? 'Unknown user' }}</span>
                                        <span class="text-xs text-gray-400 dark:text-gray-500">{{ $message->created_at->format('D, M j, Y g:ia') }}</span>
                                    </div>
                                    <p class="text-sm text-gray-600 dark:text-gray-300 whitespace-pre-line break-words">{{ $message->body }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </x-card>
        </div>
    </div>
</x-app-layout>
