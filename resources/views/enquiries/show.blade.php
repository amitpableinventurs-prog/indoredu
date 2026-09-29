<x-app-layout :title="$enquiry->title">
    @php
        $me = auth()->user();
        $student = $enquiry->student;
        $tutor = $enquiry->tutor;
        $textareaClass = 'mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm';
    @endphp
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-2">
            <div class="flex flex-wrap items-center gap-3 min-w-0">
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight truncate">{{ $enquiry->title }}</h2>
                <x-status-badge :status="$enquiry->status" />
            </div>
            <a href="{{ route('enquiries.index') }}" class="text-sm text-indigo-600 dark:text-indigo-400 hover:underline">&larr; All enquiries</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 grid lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 space-y-6">
                <!-- Student's enquiry -->
                <x-card>
                    <div class="flex items-start gap-3">
                        <div class="w-10 h-10 rounded-full bg-indigo-100 dark:bg-indigo-900/40 text-indigo-700 dark:text-indigo-300 flex items-center justify-center font-semibold text-sm shrink-0">
                            {{ $student?->initials() ?? '?' }}
                        </div>
                        <div class="min-w-0 flex-1">
                            <div class="flex flex-wrap items-baseline justify-between gap-2">
                                <p class="font-medium text-gray-900 dark:text-gray-100">{{ $student?->name ?? 'Deleted user' }} <span class="text-xs font-normal text-gray-400">(student)</span></p>
                                <span class="text-xs text-gray-400 dark:text-gray-500">{{ $enquiry->created_at->format('M j, Y · g:i A') }}</span>
                            </div>
                            <p class="mt-2 text-sm text-gray-700 dark:text-gray-300 whitespace-pre-line">{{ $enquiry->message }}</p>
                        </div>
                    </div>
                </x-card>

                <!-- Tutor's reply -->
                @if ($enquiry->tutor_reply)
                    <x-card class="{{ $enquiry->status === 'declined' ? 'border-red-200 dark:border-red-900' : 'border-indigo-200 dark:border-indigo-900' }}">
                        <div class="flex items-start gap-3">
                            <div class="w-10 h-10 rounded-full bg-green-100 dark:bg-green-900/40 text-green-700 dark:text-green-300 flex items-center justify-center font-semibold text-sm shrink-0">
                                {{ $tutor?->initials() ?? '?' }}
                            </div>
                            <div class="min-w-0 flex-1">
                                <div class="flex flex-wrap items-baseline justify-between gap-2">
                                    <p class="font-medium text-gray-900 dark:text-gray-100">{{ $tutor?->name ?? 'Deleted user' }} <span class="text-xs font-normal text-gray-400">(tutor)</span></p>
                                    <span class="text-xs text-gray-400 dark:text-gray-500">{{ $enquiry->replied_at?->format('M j, Y · g:i A') }}</span>
                                </div>
                                <p class="mt-2 text-sm text-gray-700 dark:text-gray-300 whitespace-pre-line">{{ $enquiry->tutor_reply }}</p>
                            </div>
                        </div>
                    </x-card>
                @elseif ($enquiry->status === 'pending' && $me->id === $enquiry->student_id)
                    <div class="rounded-lg border border-dashed border-amber-300 dark:border-amber-800 bg-amber-50 dark:bg-amber-950/30 p-4 text-sm text-amber-800 dark:text-amber-300">
                        Waiting for {{ $tutor?->name ?? 'the tutor' }} to reply. You'll get a notification as soon as they respond.
                    </div>
                @endif

                <!-- Connected: continue in Messages -->
                @if ($enquiry->conversation_id && $enquiry->involves($me))
                    <div class="rounded-lg border border-green-200 dark:border-green-900 bg-green-50 dark:bg-green-950/30 p-4 flex flex-wrap items-center justify-between gap-3">
                        <p class="text-sm text-green-800 dark:text-green-300">You're connected! Continue the conversation in Messages.</p>
                        <a href="{{ route('messages.show', $enquiry->conversation_id) }}"
                           class="inline-flex items-center px-4 py-2 bg-green-600 rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-500">
                            Open chat
                        </a>
                    </div>
                @endif

                <!-- Tutor actions -->
                @can('reply', $enquiry)
                    <x-card title="Reply to {{ $student?->name }}" subtitle="Your reply will also start a chat with the student in Messages.">
                        <form action="{{ route('enquiries.reply', $enquiry) }}" method="POST" class="space-y-4">
                            @csrf
                            <div>
                                <x-input-label for="tutor_reply" value="Your reply" />
                                <textarea id="tutor_reply" name="tutor_reply" rows="5" maxlength="3000" required class="{{ $textareaClass }}"
                                          placeholder="Answer the student's question, share your timings, fees, or suggest a trial session…">{{ old('tutor_reply') }}</textarea>
                                <x-input-error :messages="$errors->get('tutor_reply')" class="mt-1" />
                            </div>
                            <div class="flex flex-wrap items-center justify-between gap-3">
                                <x-primary-button type="submit">Send reply</x-primary-button>
                                @can('decline', $enquiry)
                                    <button type="button" x-data x-on:click="$dispatch('open-modal', 'decline-enquiry')"
                                            class="text-sm text-gray-500 hover:text-red-600 dark:text-gray-400 dark:hover:text-red-400">
                                        Can't help with this? Decline
                                    </button>
                                @endcan
                            </div>
                        </form>
                    </x-card>

                    @can('decline', $enquiry)
                        <x-modal name="decline-enquiry" maxWidth="md" focusable>
                            <form action="{{ route('enquiries.decline', $enquiry) }}" method="POST" class="p-6 space-y-4">
                                @csrf
                                <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Decline this enquiry?</h2>
                                <div>
                                    <x-input-label for="decline_reason" value="Short note for the student (optional)" />
                                    <textarea id="decline_reason" name="tutor_reply" rows="3" maxlength="1000" class="{{ $textareaClass }}"
                                              placeholder="e.g. Sorry, I don't teach this level right now."></textarea>
                                </div>
                                <div class="flex justify-end gap-3">
                                    <x-secondary-button type="button" x-on:click="$dispatch('close')">Cancel</x-secondary-button>
                                    <x-danger-button type="submit">Decline</x-danger-button>
                                </div>
                            </form>
                        </x-modal>
                    @endcan
                @endcan
            </div>

            <!-- Details sidebar -->
            <div class="space-y-6">
                <x-card title="Details">
                    <dl class="space-y-3 text-sm">
                        <div>
                            <dt class="text-gray-400 dark:text-gray-500">Student</dt>
                            <dd class="text-gray-900 dark:text-gray-100">{{ $student?->name ?? 'Deleted user' }}</dd>
                        </div>
                        <div>
                            <dt class="text-gray-400 dark:text-gray-500">Tutor</dt>
                            <dd class="text-gray-900 dark:text-gray-100">
                                @if ($tutor?->tutorProfile)
                                    <a href="{{ route('tutors.show', $tutor->tutorProfile) }}" class="text-indigo-600 dark:text-indigo-400 hover:underline">{{ $tutor->name }}</a>
                                @else
                                    {{ $tutor?->name ?? 'Deleted user' }}
                                @endif
                            </dd>
                        </div>
                        @if ($enquiry->course)
                            <div>
                                <dt class="text-gray-400 dark:text-gray-500">Course</dt>
                                <dd><a href="{{ route('courses.show', $enquiry->course) }}" class="text-indigo-600 dark:text-indigo-400 hover:underline">{{ $enquiry->course->title }}</a></dd>
                            </div>
                        @endif
                        @if ($enquiry->subject)
                            <div>
                                <dt class="text-gray-400 dark:text-gray-500">Subject</dt>
                                <dd class="text-gray-900 dark:text-gray-100">{{ $enquiry->subject->name }}</dd>
                            </div>
                        @endif
                        @if ($enquiry->grade)
                            <div>
                                <dt class="text-gray-400 dark:text-gray-500">Class / grade</dt>
                                <dd class="text-gray-900 dark:text-gray-100">{{ \App\Models\Course::GRADES[$enquiry->grade] ?? $enquiry->grade }}</dd>
                            </div>
                        @endif
                        @if ($enquiry->preferred_mode)
                            <div>
                                <dt class="text-gray-400 dark:text-gray-500">Preferred mode</dt>
                                <dd class="text-gray-900 dark:text-gray-100">{{ \App\Models\Enquiry::MODES[$enquiry->preferred_mode] ?? $enquiry->preferred_mode }}</dd>
                            </div>
                        @endif
                        @if ($enquiry->preferred_time)
                            <div>
                                <dt class="text-gray-400 dark:text-gray-500">Preferred timing</dt>
                                <dd class="text-gray-900 dark:text-gray-100">{{ $enquiry->preferred_time }}</dd>
                            </div>
                        @endif
                        @if ($enquiry->closed_at)
                            <div>
                                <dt class="text-gray-400 dark:text-gray-500">Closed on</dt>
                                <dd class="text-gray-900 dark:text-gray-100">{{ $enquiry->closed_at->format('M j, Y') }}</dd>
                            </div>
                        @endif
                    </dl>
                </x-card>

                @if ($me->id === $enquiry->student_id && $enquiry->status === 'replied' && $tutor?->tutorProfile)
                    <a href="{{ route('tutors.show', $tutor->tutorProfile) }}"
                       class="block text-center w-full px-4 py-2 bg-indigo-600 rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500">
                        Book a session with {{ $tutor->name }}
                    </a>
                @endif

                @can('close', $enquiry)
                    <form action="{{ route('enquiries.close', $enquiry) }}" method="POST" onsubmit="return confirm('Mark this enquiry as closed?');">
                        @csrf
                        <x-secondary-button type="submit" class="w-full justify-center">Mark as closed</x-secondary-button>
                    </form>
                @endcan
            </div>
        </div>
    </div>
</x-app-layout>
