{{--
    Enquiry form in a modal. Expects: $tutor (User), optional $subjects (collection),
    optional $course (Course) to pre-fill the enquiry for a specific course.
--}}
@php
    $course = $course ?? null;
    $subjects = $subjects ?? collect();
    $defaultTitle = $course ? "Enquiry about \"{$course->title}\"" : '';
    $selectClass = 'mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm';
@endphp

<x-modal name="send-enquiry" :show="$errors->hasAny(['title', 'message', 'grade', 'preferred_mode', 'preferred_time'])" focusable>
    <form action="{{ route('enquiries.store') }}" method="POST" class="p-6 space-y-4">
        @csrf
        <input type="hidden" name="tutor_id" value="{{ $tutor->id }}">
        @if ($course)
            <input type="hidden" name="course_id" value="{{ $course->id }}">
            <input type="hidden" name="subject_id" value="{{ $course->subject_id }}">
        @endif

        <div>
            <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Send an enquiry to {{ $tutor->name }}</h2>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Ask about subjects, timings, fees or anything else before you book. The tutor will be notified and can reply directly.</p>
        </div>

        <div>
            <x-input-label for="enquiry_title" value="Topic" />
            <x-text-input id="enquiry_title" name="title" type="text" class="mt-1 block w-full" maxlength="150" required
                          :value="old('title', $defaultTitle)" placeholder="e.g. Need help with Class 10 Maths" />
            <x-input-error :messages="$errors->get('title')" class="mt-1" />
        </div>

        <div class="grid sm:grid-cols-2 gap-4">
            @if (! $course && $subjects->isNotEmpty())
                <div>
                    <x-input-label for="enquiry_subject" value="Subject (optional)" />
                    <select id="enquiry_subject" name="subject_id" class="{{ $selectClass }}">
                        <option value="">— Any —</option>
                        @foreach ($subjects as $subject)
                            <option value="{{ $subject->id }}" @selected(old('subject_id') == $subject->id)>{{ $subject->name }}</option>
                        @endforeach
                    </select>
                </div>
            @endif

            <div>
                <x-input-label for="enquiry_grade" value="Your class / grade (optional)" />
                <select id="enquiry_grade" name="grade" class="{{ $selectClass }}">
                    <option value="">— Select —</option>
                    @foreach (\App\Models\Course::GRADES as $value => $label)
                        @continue($value === \App\Models\Course::GRADE_ALL_GRADES)
                        <option value="{{ $value }}" @selected(old('grade', auth()->user()?->studentProfile?->grade_level) === $value)>{{ $label }}</option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('grade')" class="mt-1" />
            </div>

            <div>
                <x-input-label for="enquiry_mode" value="Preferred mode (optional)" />
                <select id="enquiry_mode" name="preferred_mode" class="{{ $selectClass }}">
                    <option value="">— Select —</option>
                    @foreach (\App\Models\Enquiry::MODES as $value => $label)
                        <option value="{{ $value }}" @selected(old('preferred_mode') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <x-input-label for="enquiry_time" value="Preferred timing (optional)" />
                <x-text-input id="enquiry_time" name="preferred_time" type="text" class="mt-1 block w-full" maxlength="100"
                              :value="old('preferred_time')" placeholder="e.g. Weekdays after 5 PM" />
            </div>
        </div>

        <div>
            <x-input-label for="enquiry_message" value="Your question" />
            <textarea id="enquiry_message" name="message" rows="5" maxlength="3000" required
                      class="{{ $selectClass }}" placeholder="Tell the tutor what you need help with…">{{ old('message') }}</textarea>
            <x-input-error :messages="$errors->get('message')" class="mt-1" />
        </div>

        <div class="flex justify-end gap-3">
            <x-secondary-button type="button" x-on:click="$dispatch('close')">Cancel</x-secondary-button>
            <x-primary-button type="submit">Send enquiry</x-primary-button>
        </div>
    </form>
</x-modal>
