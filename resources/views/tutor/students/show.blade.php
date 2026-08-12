<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ $student->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4 sm:p-6">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 rounded-full bg-indigo-100 dark:bg-indigo-900/40 text-indigo-700 dark:text-indigo-300 flex items-center justify-center font-semibold text-lg shrink-0 overflow-hidden">
                        @if ($student->avatar)
                            <img src="{{ asset('storage/'.$student->avatar) }}" alt="{{ $student->name }}" class="w-full h-full object-cover">
                        @else
                            {{ $student->initials() }}
                        @endif
                    </div>
                    <div class="min-w-0">
                        <p class="font-semibold text-lg text-gray-900 dark:text-gray-100">{{ $student->name }}</p>
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ $student->email }}</p>
                        @if ($student->studentProfile?->grade_level)
                            <p class="text-sm text-gray-500 dark:text-gray-400">Grade level: {{ $student->studentProfile->grade_level }}</p>
                        @endif
                    </div>
                </div>
                @if ($student->studentProfile?->learning_goals)
                    <div class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-700">
                        <p class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Learning goals</p>
                        <p class="text-sm text-gray-600 dark:text-gray-300 whitespace-pre-line">{{ $student->studentProfile->learning_goals }}</p>
                    </div>
                @endif
            </div>

            <x-card title="Sessions together" :subtitle="$bookings->count().' total'">
                <div class="space-y-4">
                    @foreach ($bookings as $booking)
                        <div class="border border-gray-200 dark:border-gray-700 rounded-md p-4">
                            <div class="flex flex-wrap items-center justify-between gap-2">
                                <div>
                                    <p class="font-medium text-gray-900 dark:text-gray-100">{{ $booking->subject->name ?? '—' }}</p>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ $booking->scheduled_date->format('D, M j, Y') }} at {{ substr($booking->start_time, 0, 5) }}</p>
                                </div>
                                <div class="flex items-center gap-2">
                                    <x-status-badge :status="$booking->status" />
                                    @if ($booking->attendance)
                                        <x-status-badge :status="$booking->attendance->student_status ?? 'pending'" />
                                    @endif
                                </div>
                            </div>

                            @if ($booking->review)
                                <div class="mt-2">
                                    <x-star-rating :rating="$booking->review->rating" size="w-3.5 h-3.5" />
                                    @if ($booking->review->comment)
                                        <p class="text-sm text-gray-600 dark:text-gray-300 mt-1">"{{ $booking->review->comment }}"</p>
                                    @endif
                                </div>
                            @endif

                            @if ($booking->status === 'completed')
                                <form action="{{ route('tutor.bookings.notes', $booking) }}" method="POST" class="mt-3 pt-3 border-t border-gray-100 dark:border-gray-700">
                                    @csrf
                                    @method('PUT')
                                    <x-input-label :for="'tutor_notes_'.$booking->id" value="Progress notes (visible to student)" />
                                    <textarea id="tutor_notes_{{ $booking->id }}" name="tutor_notes" rows="2" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">{{ old('tutor_notes', $booking->tutor_notes) }}</textarea>
                                    <div class="mt-2 flex justify-end">
                                        <x-secondary-button type="submit">Save notes</x-secondary-button>
                                    </div>
                                </form>
                            @endif
                        </div>
                    @endforeach
                </div>
            </x-card>

        </div>
    </div>
</x-app-layout>
