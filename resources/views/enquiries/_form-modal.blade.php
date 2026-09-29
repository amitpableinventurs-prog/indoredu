{{--
    Enquiry form in a modal. Expects: $tutor (User), optional $subjects (collection),
    optional $course (Course) to tie the enquiry to a specific course.
--}}
@php
    $course = $course ?? null;
    $subjects = $subjects ?? collect();
    $selectClass = 'mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm';
    $picked = old('questions', []);
@endphp

<x-modal name="send-enquiry" :show="$errors->hasAny(['questions', 'questions.*', 'message', 'grade', 'preferred_mode', 'preferred_time'])" focusable>
    <form action="{{ route('enquiries.store') }}" method="POST" class="p-6 space-y-5">
        @csrf
        <input type="hidden" name="tutor_id" value="{{ $tutor->id }}">
        @if ($course)
            <input type="hidden" name="course_id" value="{{ $course->id }}">
            <input type="hidden" name="subject_id" value="{{ $course->subject_id }}">
        @endif

        <div>
            <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Send an enquiry to {{ $tutor->name }}</h2>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                Tick the questions you want answered. Only you and {{ $tutor->name }} can see this enquiry and the replies.
            </p>
        </div>

        <fieldset>
            <legend class="block font-medium text-sm text-gray-700 dark:text-gray-300">What would you like to know? <span class="font-normal text-gray-400">(select any)</span></legend>
            <div class="mt-2 grid sm:grid-cols-2 gap-2">
                @foreach (\App\Models\Enquiry::QUESTIONS as $key => $label)
                    <label class="flex items-start gap-2.5 rounded-md border border-gray-200 dark:border-gray-700 px-3 py-2 text-sm text-gray-700 dark:text-gray-300 cursor-pointer hover:border-indigo-300 dark:hover:border-indigo-700 has-[:checked]:border-indigo-500 has-[:checked]:bg-indigo-50 dark:has-[:checked]:bg-indigo-950/40">
                        <input type="checkbox" name="questions[]" value="{{ $key }}" @checked(in_array($key, $picked, true))
                               class="mt-0.5 rounded border-gray-300 dark:border-gray-600 dark:bg-gray-900 text-indigo-600 focus:ring-indigo-500">
                        <span>{{ $label }}</span>
                    </label>
                @endforeach
            </div>
            <x-input-error :messages="$errors->get('questions.*')" class="mt-1" />
        </fieldset>

        <div>
            <x-input-label for="enquiry_message" value="Any other question? (optional)" />
            <textarea id="enquiry_message" name="message" rows="3" maxlength="3000"
                      class="{{ $selectClass }}" placeholder="Write your own question here…">{{ old('message') }}</textarea>
            <x-input-error :messages="$errors->get('message')" class="mt-1" />
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

        <div class="flex justify-end gap-3">
            <x-secondary-button type="button" x-on:click="$dispatch('close')">Cancel</x-secondary-button>
            <x-primary-button type="submit">Send enquiry</x-primary-button>
        </div>
    </form>
</x-modal>
