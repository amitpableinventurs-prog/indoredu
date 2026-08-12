<?php /* resources/views/bookings/show.blade.php */ ?>
@php
    $isStudent = auth()->id() === $booking->student_id;
    $isTutor = auth()->id() === $booking->tutor_id;
    $otherUser = $isStudent ? $booking->tutor : $booking->student;
@endphp
<x-app-layout :title="'Booking #'.$booking->id">
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-2">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ $booking->subject->name ?? 'Session' }}
                @if ($isStudent)
                    with
                    <a href="{{ route('tutors.show', $booking->tutor->tutorProfile) }}" class="text-indigo-600 dark:text-indigo-400 hover:underline">{{ $booking->tutor->name }}</a>
                @else
                    with {{ $booking->student->name }}
                @endif
            </h2>
            <x-status-badge :status="$booking->status" />
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Summary -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4 sm:p-6">
                <div class="grid sm:grid-cols-2 gap-4 text-sm">
                    <div>
                        <p class="text-gray-500 dark:text-gray-400">Date &amp; time</p>
                        <p class="font-medium text-gray-900 dark:text-gray-100">
                            {{ $booking->scheduled_date->format('D, M j, Y') }} &middot; {{ substr($booking->start_time, 0, 5) }}&ndash;{{ substr($booking->end_time, 0, 5) }}
                        </p>
                    </div>
                    <div>
                        <p class="text-gray-500 dark:text-gray-400">Duration</p>
                        <p class="font-medium text-gray-900 dark:text-gray-100">{{ $booking->duration_minutes }} minutes</p>
                    </div>
                    <div>
                        <p class="text-gray-500 dark:text-gray-400">Price</p>
                        <p class="font-medium text-gray-900 dark:text-gray-100">
                            @if ($booking->is_trial && (float) $booking->price === 0.0)
                                Free trial
                            @else
                                ₹{{ number_format($booking->price, 2) }}
                            @endif
                        </p>
                    </div>
                    <div>
                        <p class="text-gray-500 dark:text-gray-400">{{ $isStudent ? 'Tutor' : 'Student' }}</p>
                        <p class="font-medium text-gray-900 dark:text-gray-100">
                            @if ($isStudent)
                                <a href="{{ route('tutors.show', $booking->tutor->tutorProfile) }}" class="text-indigo-600 dark:text-indigo-400 hover:underline">{{ $booking->tutor->name }}</a>
                            @else
                                {{ $booking->student->name }}
                            @endif
                        </p>
                    </div>
                </div>

                @if ($booking->meeting_link)
                    <div class="mt-5">
                        <a href="{{ $booking->meeting_link }}" target="_blank" rel="noopener"
                           class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-sm text-white hover:bg-indigo-500">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                            Join video call
                        </a>
                    </div>
                @endif

                @if ($isStudent && $booking->status === \App\Models\Booking::STATUS_PENDING && $booking->payment && $booking->payment->status !== 'completed')
                    <div class="mt-5">
                        <a href="{{ route('payments.checkout', $booking->payment) }}"
                           class="inline-flex items-center px-4 py-2 bg-amber-500 border border-transparent rounded-md font-semibold text-sm text-white hover:bg-amber-400">
                            Complete payment
                        </a>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">This booking will be confirmed once payment is completed.</p>
                    </div>
                @endif

                @if ($booking->payment && $booking->payment->status === 'completed')
                    <div class="mt-5">
                        <a href="{{ route('payments.receipt', $booking->payment) }}" class="text-sm text-indigo-600 dark:text-indigo-400 hover:underline">View receipt</a>
                    </div>
                @endif

                @if ($booking->conversation)
                    <div class="mt-3">
                        <a href="{{ route('messages.show', $booking->conversation) }}" class="inline-flex items-center gap-1.5 text-sm text-indigo-600 dark:text-indigo-400 hover:underline">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8-1.06 0-2.076-.163-3.017-.463L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                            Message {{ $otherUser->name }}
                        </a>
                    </div>
                @endif
            </div>

            <!-- Cancel / cancellation reason -->
            @if (in_array($booking->status, [\App\Models\Booking::STATUS_PENDING, \App\Models\Booking::STATUS_CONFIRMED]))
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4 sm:p-6">
                    <h3 class="font-semibold text-gray-900 dark:text-gray-100 mb-3">Cancel this session</h3>
                    <form action="{{ route('bookings.cancel', $booking) }}" method="POST" onsubmit="return confirm('Cancel this session?')" class="space-y-3">
                        @csrf
                        <div>
                            <x-input-label for="cancellation_reason" value="Reason (optional)" />
                            <textarea id="cancellation_reason" name="cancellation_reason" rows="2"
                                      class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"></textarea>
                        </div>
                        <x-danger-button type="submit">Cancel session</x-danger-button>
                    </form>
                </div>
            @elseif ($booking->status === \App\Models\Booking::STATUS_CANCELLED && $booking->cancellation_reason)
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4 sm:p-6">
                    <h3 class="font-semibold text-gray-900 dark:text-gray-100 mb-1">Cancellation reason</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-300">{{ $booking->cancellation_reason }}</p>
                </div>
            @endif

            <!-- Attendance & completion (tutor only, confirmed) -->
            @if ($isTutor && $booking->status === \App\Models\Booking::STATUS_CONFIRMED)
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4 sm:p-6">
                    <h3 class="font-semibold text-gray-900 dark:text-gray-100 mb-1">Attendance &amp; completion</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-3">Submitting this will automatically mark the session as completed.</p>
                    <form action="{{ route('bookings.attendance', $booking) }}" method="POST" class="space-y-4">
                        @csrf
                        <div class="grid sm:grid-cols-2 gap-4">
                            <div>
                                <x-input-label for="student_status" value="Student attendance" />
                                <select id="student_status" name="student_status" required class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                    <option value="present">Present</option>
                                    <option value="absent">Absent</option>
                                    <option value="late">Late</option>
                                    <option value="excused">Excused</option>
                                </select>
                            </div>
                            <div>
                                <x-input-label for="tutor_status" value="Your attendance" />
                                <select id="tutor_status" name="tutor_status" required class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                    <option value="present">Present</option>
                                    <option value="absent">Absent</option>
                                    <option value="late">Late</option>
                                </select>
                            </div>
                        </div>
                        <div>
                            <x-input-label for="attendance_notes" value="Notes (optional)" />
                            <textarea id="attendance_notes" name="notes" rows="3"
                                      class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"></textarea>
                        </div>
                        <x-primary-button type="submit">Save attendance &amp; complete session</x-primary-button>
                    </form>

                    <div class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-700">
                        <form action="{{ route('bookings.complete', $booking) }}" method="POST" onsubmit="return confirm('Mark this session as complete?')">
                            @csrf
                            <x-secondary-button type="submit">Mark complete (skip attendance)</x-secondary-button>
                        </form>
                    </div>
                </div>
            @endif

            <!-- Notes -->
            @if ($booking->student_notes)
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4 sm:p-6">
                    <h3 class="font-semibold text-gray-900 dark:text-gray-100 mb-1">Student's notes</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-300 whitespace-pre-line">{{ $booking->student_notes }}</p>
                </div>
            @endif

            @if ($booking->tutor_notes)
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4 sm:p-6">
                    <h3 class="font-semibold text-gray-900 dark:text-gray-100 mb-1">Tutor's notes</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-300 whitespace-pre-line">{{ $booking->tutor_notes }}</p>
                    @if ($isTutor)
                        <p class="text-xs text-gray-400 dark:text-gray-500 mt-2">You can edit these notes from this student's detail page.</p>
                    @endif
                </div>
            @endif

            <!-- Review -->
            @if ($booking->status === \App\Models\Booking::STATUS_COMPLETED)
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4 sm:p-6">
                    <h3 class="font-semibold text-gray-900 dark:text-gray-100 mb-3">Review</h3>

                    @if ($booking->review)
                        <x-star-rating :rating="$booking->review->rating" />
                        @if ($booking->review->comment)
                            <p class="text-sm text-gray-600 dark:text-gray-300 mt-2">{{ $booking->review->comment }}</p>
                        @endif

                        @if ($booking->review->tutor_response)
                            <div class="mt-3 ml-4 pl-3 border-l-2 border-indigo-200 dark:border-indigo-800 text-sm text-gray-500 dark:text-gray-400">
                                <span class="font-medium">Tutor response:</span> {{ $booking->review->tutor_response }}
                            </div>
                        @elseif ($isTutor)
                            <form action="{{ route('reviews.respond', $booking->review) }}" method="POST" class="mt-4 space-y-3">
                                @csrf
                                <x-input-label for="tutor_response" value="Respond to this review" />
                                <textarea id="tutor_response" name="tutor_response" rows="3" required
                                          class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"></textarea>
                                <x-primary-button type="submit">Post response</x-primary-button>
                            </form>
                        @endif
                    @elseif ($isStudent)
                        <form action="{{ route('bookings.review', $booking) }}" method="POST" class="space-y-4">
                            @csrf
                            <div>
                                <x-input-label for="rating" value="Rating" />
                                <select id="rating" name="rating" required class="mt-1 block w-full sm:w-40 rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                    <option value="5">5 &ndash; Excellent</option>
                                    <option value="4">4 &ndash; Good</option>
                                    <option value="3">3 &ndash; Average</option>
                                    <option value="2">2 &ndash; Below average</option>
                                    <option value="1">1 &ndash; Poor</option>
                                </select>
                            </div>
                            <div>
                                <x-input-label for="comment" value="Comment (optional)" />
                                <textarea id="comment" name="comment" rows="3"
                                          class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"></textarea>
                            </div>
                            <x-primary-button type="submit">Submit review</x-primary-button>
                        </form>
                    @else
                        <p class="text-sm text-gray-500 dark:text-gray-400">The student hasn't left a review yet.</p>
                    @endif
                </div>
            @endif

            <!-- Safety -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4 sm:p-6">
                <h3 class="font-semibold text-gray-900 dark:text-gray-100 mb-3 text-sm">Safety</h3>
                <div class="flex flex-wrap gap-4">
                    <form action="{{ route('reports.store') }}" method="POST" onsubmit="return confirm('Report {{ $otherUser->name }} to our moderation team?');">
                        @csrf
                        <input type="hidden" name="reportable_type" value="user">
                        <input type="hidden" name="reportable_id" value="{{ $otherUser->id }}">
                        <input type="hidden" name="reason" value="Inappropriate behavior during booking #{{ $booking->id }}">
                        <button type="submit" class="text-sm text-gray-400 hover:text-red-600 dark:hover:text-red-400">Report {{ $otherUser->name }}</button>
                    </form>

                    @if (auth()->user()->hasBlocked($otherUser))
                        <form action="{{ route('users.unblock', $otherUser) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-sm text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400">Unblock {{ $otherUser->name }}</button>
                        </form>
                    @else
                        <form action="{{ route('users.block', $otherUser) }}" method="POST" onsubmit="return confirm('Block {{ $otherUser->name }}? You will no longer be able to message or book with them.');">
                            @csrf
                            <button type="submit" class="text-sm text-gray-400 hover:text-red-600 dark:hover:text-red-400">Block {{ $otherUser->name }}</button>
                        </form>
                    @endif
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
