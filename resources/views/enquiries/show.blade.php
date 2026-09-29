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
                @php
                    $questions = $enquiry->questionList();
                    $answers = $enquiry->answers ?? [];
                    $canReply = $me->can('reply', $enquiry);
                    $isDeclined = $enquiry->status === 'declined';
                @endphp

                <!-- Who asked -->
                <div class="flex flex-wrap items-center justify-between gap-2 text-sm">
                    <p class="text-gray-600 dark:text-gray-300">
                        Asked by <span class="font-medium text-gray-900 dark:text-gray-100">{{ $student?->name ?? 'Deleted user' }}</span>
                        to <span class="font-medium text-gray-900 dark:text-gray-100">{{ $tutor?->name ?? 'Deleted user' }}</span>
                        &middot; {{ $enquiry->created_at->format('M j, Y · g:i A') }}
                    </p>
                    <span class="inline-flex items-center gap-1 text-xs text-gray-400 dark:text-gray-500">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                        Private between student and tutor
                    </span>
                </div>

                @if ($enquiry->status === 'pending' && $me->id === $enquiry->student_id)
                    <div class="rounded-lg border border-dashed border-amber-300 dark:border-amber-800 bg-amber-50 dark:bg-amber-950/30 p-4 text-sm text-amber-800 dark:text-amber-300">
                        Waiting for {{ $tutor?->name ?? 'the tutor' }} to answer. You'll get a notification as soon as they respond.
                    </div>
                @endif

                <!-- Questions & answers (FAQ style). For the tutor, each answer is an input. -->
                @if ($canReply)
                    <form action="{{ route('enquiries.reply', $enquiry) }}" method="POST" class="space-y-6">
                        @csrf
                @endif

                <x-card title="Questions & answers" :subtitle="$canReply ? 'Answer each question. Your answers will also start a chat with the student in Messages.' : null">
                    <div class="divide-y divide-gray-100 dark:divide-gray-700 -my-2">
                        @foreach ($questions as $key => $label)
                            <div class="py-4">
                                <p class="flex gap-2 font-medium text-gray-900 dark:text-gray-100 text-sm">
                                    <span class="shrink-0 text-indigo-600 dark:text-indigo-400">Q.</span>{{ $label }}
                                </p>
                                <div class="mt-2 flex gap-2 text-sm">
                                    <span class="shrink-0 font-medium text-green-600 dark:text-green-400">A.</span>
                                    @if ($canReply)
                                        <div class="flex-1">
                                            <textarea name="answers[{{ $key }}]" rows="2" maxlength="1000" required
                                                      class="block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">{{ old("answers.$key") }}</textarea>
                                            <x-input-error :messages="$errors->get('answers.'.$key)" class="mt-1" />
                                        </div>
                                    @elseif (isset($answers[$key]))
                                        <p class="text-gray-700 dark:text-gray-300 whitespace-pre-line">{{ $answers[$key] }}</p>
                                    @else
                                        <p class="italic text-gray-400 dark:text-gray-500">{{ $isDeclined ? 'Not answered' : 'Awaiting answer' }}</p>
                                    @endif
                                </div>
                            </div>
                        @endforeach

                        @if ($enquiry->message || ! $questions)
                            <div class="py-4">
                                <p class="flex gap-2 font-medium text-gray-900 dark:text-gray-100 text-sm">
                                    <span class="shrink-0 text-indigo-600 dark:text-indigo-400">Q.</span>
                                    <span class="whitespace-pre-line">{{ $enquiry->message ?: 'General enquiry' }}</span>
                                </p>
                                <div class="mt-2 flex gap-2 text-sm">
                                    <span class="shrink-0 font-medium text-green-600 dark:text-green-400">A.</span>
                                    @if ($canReply)
                                        <div class="flex-1">
                                            <textarea name="tutor_reply" rows="3" maxlength="3000" required
                                                      class="block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">{{ old('tutor_reply') }}</textarea>
                                            <x-input-error :messages="$errors->get('tutor_reply')" class="mt-1" />
                                        </div>
                                    @elseif ($enquiry->tutor_reply && ! $isDeclined)
                                        <p class="text-gray-700 dark:text-gray-300 whitespace-pre-line">{{ $enquiry->tutor_reply }}</p>
                                    @else
                                        <p class="italic text-gray-400 dark:text-gray-500">{{ $isDeclined ? 'Not answered' : 'Awaiting answer' }}</p>
                                    @endif
                                </div>
                            </div>
                        @elseif ($canReply)
                            <div class="py-4">
                                <x-input-label for="tutor_reply" value="Anything else to add? (optional)" />
                                <textarea id="tutor_reply" name="tutor_reply" rows="2" maxlength="3000" class="{{ $textareaClass }}"
                                          placeholder="e.g. Happy to schedule a trial class this week.">{{ old('tutor_reply') }}</textarea>
                            </div>
                        @elseif ($enquiry->tutor_reply && ! $isDeclined)
                            <div class="py-4 text-sm">
                                <p class="font-medium text-gray-900 dark:text-gray-100">Note from {{ $tutor?->name ?? 'the tutor' }}</p>
                                <p class="mt-1 text-gray-700 dark:text-gray-300 whitespace-pre-line">{{ $enquiry->tutor_reply }}</p>
                            </div>
                        @endif
                    </div>

                    @if ($enquiry->replied_at && ! $isDeclined)
                        <p class="mt-4 text-xs text-gray-400 dark:text-gray-500">Answered by {{ $tutor?->name }} on {{ $enquiry->replied_at->format('M j, Y · g:i A') }}</p>
                    @endif
                </x-card>

                @if ($canReply)
                        <div class="flex flex-wrap items-center justify-between gap-3">
                            <x-primary-button type="submit">Send answers</x-primary-button>
                            @can('decline', $enquiry)
                                <button type="button" x-data x-on:click="$dispatch('open-modal', 'decline-enquiry')"
                                        class="text-sm text-gray-500 hover:text-red-600 dark:text-gray-400 dark:hover:text-red-400">
                                    Can't help with this? Decline
                                </button>
                            @endcan
                        </div>
                    </form>
                @endif

                @if ($isDeclined)
                    <div class="rounded-lg border border-red-200 dark:border-red-900 bg-red-50 dark:bg-red-950/30 p-4 text-sm text-red-800 dark:text-red-300">
                        <p class="font-medium">{{ $tutor?->name ?? 'The tutor' }} declined this enquiry.</p>
                        @if ($enquiry->tutor_reply)
                            <p class="mt-1 whitespace-pre-line">{{ $enquiry->tutor_reply }}</p>
                        @endif
                    </div>
                @endif

                <!-- Connected: continue in Messages -->
                @if ($enquiry->conversation_id && $enquiry->involves($me))
                    <div class="rounded-lg border border-green-200 dark:border-green-900 bg-green-50 dark:bg-green-950/30 p-4 flex flex-wrap items-center justify-between gap-3">
                        <p class="text-sm text-green-800 dark:text-green-300">You're connected! Ask follow-up questions in Messages.</p>
                        <a href="{{ route('messages.show', $enquiry->conversation_id) }}"
                           class="inline-flex items-center px-4 py-2 bg-green-600 rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-500">
                            Open chat
                        </a>
                    </div>
                @endif

                @if ($canReply)
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
                @endif
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
