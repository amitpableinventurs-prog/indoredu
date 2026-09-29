<x-public-layout :title="$tutorProfile->user->name">
    @php $u = $tutorProfile->user; @endphp
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 grid lg:grid-cols-3 gap-8">
        <div class="lg:col-span-2 space-y-6">
            <!-- Header -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <div class="flex items-start gap-5">
                    <div class="w-20 h-20 rounded-full bg-indigo-100 dark:bg-indigo-900/40 text-indigo-700 dark:text-indigo-300 flex items-center justify-center font-semibold text-2xl shrink-0 overflow-hidden">
                        @if ($u->avatar)
                            <img src="{{ asset('storage/'.$u->avatar) }}" alt="{{ $u->name }}" class="w-full h-full object-cover">
                        @else
                            {{ $u->initials() }}
                        @endif
                    </div>
                    <div class="min-w-0 flex-1">
                        <div class="flex flex-wrap items-center gap-2">
                            <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $u->name }}</h1>
                            <span class="inline-flex items-center gap-1 text-xs font-medium text-green-600 dark:text-green-400">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" /></svg>
                                Verified tutor
                            </span>
                        </div>
                        <p class="text-gray-600 dark:text-gray-300 mt-0.5">{{ $tutorProfile->headline }}</p>
                        @if ($u->city || $u->country)
                            <p class="text-sm text-gray-400 dark:text-gray-500 mt-0.5">{{ collect([$u->city, $u->country])->filter()->join(', ') }} · {{ $u->timezone }}</p>
                        @endif
                        <div class="mt-2"><x-star-rating :rating="$tutorProfile->rating_avg" :count="$tutorProfile->rating_count" /></div>
                        <div class="mt-3 flex flex-wrap gap-2 text-sm text-gray-500 dark:text-gray-400">
                            <span>{{ $tutorProfile->experience_years }} yrs experience</span>
                            <span>&middot;</span>
                            <span>{{ $tutorProfile->total_sessions }} sessions taught</span>
                        </div>
                    </div>
                </div>

                @auth
                    @if (auth()->id() !== $u->id)
                        <div class="mt-4 flex flex-wrap items-center gap-3">
                            @if (auth()->user()->isStudent())
                                <x-primary-button type="button" x-data x-on:click="$dispatch('open-modal', 'send-enquiry')">Send enquiry</x-primary-button>
                            @endif
                            <form action="{{ route('messages.start') }}" method="POST">
                                @csrf
                                <input type="hidden" name="user_id" value="{{ $u->id }}">
                                <input type="hidden" name="body" value="Hi {{ $u->name }}, I'm interested in your tutoring sessions.">
                                <x-secondary-button type="submit">Message</x-secondary-button>
                            </form>
                            <form action="{{ route('reports.store') }}" method="POST" onsubmit="return confirm('Report this tutor to our moderation team?');">
                                @csrf
                                <input type="hidden" name="reportable_type" value="user">
                                <input type="hidden" name="reportable_id" value="{{ $u->id }}">
                                <input type="hidden" name="reason" value="Inappropriate profile or behavior">
                                <button type="submit" class="text-sm text-gray-400 hover:text-red-600 dark:hover:text-red-400">Report</button>
                            </form>
                        </div>
                        @if (auth()->user()->isStudent())
                            @include('enquiries._form-modal', ['tutor' => $u, 'subjects' => $tutorProfile->subjects])
                        @endif
                    @endif
                @endauth
            </div>

            <!-- Video intro -->
            @if ($tutorProfile->video_intro_url)
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <h2 class="font-semibold text-gray-900 dark:text-gray-100 mb-3">Introduction video</h2>
                    <a href="{{ $tutorProfile->video_intro_url }}" target="_blank" rel="noopener" class="text-indigo-600 dark:text-indigo-400 hover:underline text-sm break-all">{{ $tutorProfile->video_intro_url }}</a>
                </div>
            @endif

            <!-- About -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <h2 class="font-semibold text-gray-900 dark:text-gray-100 mb-2">About</h2>
                <p class="text-sm text-gray-600 dark:text-gray-300 whitespace-pre-line">{{ $tutorProfile->bio ?: 'This tutor hasn\'t written a bio yet.' }}</p>
                @if ($tutorProfile->education)
                    <h3 class="font-medium text-gray-900 dark:text-gray-100 mt-4 mb-1 text-sm">Education & Qualifications</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-300 whitespace-pre-line">{{ $tutorProfile->education }}</p>
                @endif
                @if ($tutorProfile->languages)
                    <h3 class="font-medium text-gray-900 dark:text-gray-100 mt-4 mb-1 text-sm">Languages</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-300">{{ implode(', ', $tutorProfile->languages) }}</p>
                @endif
            </div>

            <!-- Subjects -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <h2 class="font-semibold text-gray-900 dark:text-gray-100 mb-3">Subjects taught</h2>
                <div class="flex flex-wrap gap-2">
                    @foreach ($tutorProfile->subjects as $subject)
                        <span class="px-3 py-1 rounded-full text-sm bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300">
                            {{ $subject->name }} <span class="text-gray-400 dark:text-gray-500">· {{ ucfirst($subject->pivot->level) }}</span>
                        </span>
                    @endforeach
                </div>
            </div>

            <!-- Courses -->
            @if ($tutorProfile->courses->isNotEmpty())
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <h2 class="font-semibold text-gray-900 dark:text-gray-100 mb-3">Courses by {{ $u->name }}</h2>
                    <div class="space-y-3">
                        @foreach ($tutorProfile->courses as $course)
                            <a href="{{ route('courses.show', $course) }}" class="block p-3 rounded-md border border-gray-200 dark:border-gray-700 hover:border-indigo-300 dark:hover:border-indigo-700">
                                <div class="flex justify-between items-center">
                                    <span class="font-medium text-gray-900 dark:text-gray-100">{{ $course->title }}</span>
                                    <span class="text-sm font-semibold text-gray-700 dark:text-gray-300">₹{{ number_format($course->price, 0) }}</span>
                                </div>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ $course->total_sessions }} sessions · {{ $course->duration_minutes }} min each</p>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Reviews -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <h2 class="font-semibold text-gray-900 dark:text-gray-100 mb-4">Reviews ({{ $reviews->total() }})</h2>
                @forelse ($reviews as $review)
                    <div class="py-4 {{ !$loop->last ? 'border-b border-gray-100 dark:border-gray-700' : '' }}">
                        <div class="flex items-center justify-between">
                            <span class="font-medium text-gray-900 dark:text-gray-100 text-sm">{{ $review->student->name }}</span>
                            <x-star-rating :rating="$review->rating" size="w-3.5 h-3.5" />
                        </div>
                        @if ($review->comment)
                            <p class="text-sm text-gray-600 dark:text-gray-300 mt-1">{{ $review->comment }}</p>
                        @endif
                        @if ($review->tutor_response)
                            <div class="mt-2 ml-4 pl-3 border-l-2 border-indigo-200 dark:border-indigo-800 text-sm text-gray-500 dark:text-gray-400">
                                <span class="font-medium">Tutor response:</span> {{ $review->tutor_response }}
                            </div>
                        @endif
                    </div>
                @empty
                    <p class="text-sm text-gray-500 dark:text-gray-400">No reviews yet.</p>
                @endforelse
                @if ($reviews->hasPages())
                    <div class="mt-4">{{ $reviews->links() }}</div>
                @endif
            </div>
        </div>

        <!-- Booking widget -->
        <div class="lg:col-span-1">
            <div class="sticky top-6 bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">₹{{ number_format($tutorProfile->hourly_rate, 0) }}<span class="text-sm font-normal text-gray-500 dark:text-gray-400">/hour</span></p>
                @if ($tutorProfile->offers_trial)
                    <p class="text-sm text-green-600 dark:text-green-400 mt-1">Trial session: ₹{{ number_format($tutorProfile->trial_price, 0) }}</p>
                @endif

                @auth
                    @if (auth()->user()->isStudent())
                        <form action="{{ route('bookings.store') }}" method="POST" class="mt-5 space-y-4"
                              x-data="bookingWidget('{{ route('tutors.slots', $tutorProfile) }}')">
                            @csrf
                            <input type="hidden" name="tutor_id" value="{{ $u->id }}">
                            <input type="hidden" name="start_time" x-model="selectedSlot">

                            <div>
                                <x-input-label for="subject_id" value="Subject" />
                                <select id="subject_id" name="subject_id" required class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                    @foreach ($tutorProfile->subjects as $subject)
                                        <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <x-input-label for="duration_minutes" value="Duration" />
                                <select id="duration_minutes" name="duration_minutes" required class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                    <option value="30">30 minutes</option>
                                    <option value="60" selected>60 minutes</option>
                                    <option value="90">90 minutes</option>
                                    <option value="120">120 minutes</option>
                                </select>
                            </div>

                            <div>
                                <x-input-label for="scheduled_date" value="Date" />
                                <input type="date" id="scheduled_date" name="scheduled_date" required
                                       x-model="date" @change="fetchSlots()"
                                       min="{{ now()->toDateString() }}"
                                       class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                            </div>

                            <div>
                                <x-input-label value="Available times" />
                                <div class="mt-1 grid grid-cols-3 gap-2" x-show="slots.length">
                                    <template x-for="slot in slots" :key="slot">
                                        <button type="button" @click="selectedSlot = slot"
                                                :class="selectedSlot === slot ? 'bg-indigo-600 text-white border-indigo-600' : 'bg-white dark:bg-gray-900 text-gray-700 dark:text-gray-300 border-gray-300 dark:border-gray-700'"
                                                class="text-xs py-1.5 rounded-md border hover:border-indigo-400" x-text="slot"></button>
                                    </template>
                                </div>
                                <p class="text-xs text-gray-400 mt-1" x-show="!slots.length" x-text="loaded ? 'No available slots this day.' : 'Pick a date to see available times.'"></p>
                            </div>

                            @if ($tutorProfile->offers_trial)
                                <label class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-300">
                                    <input type="checkbox" name="is_trial" value="1" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                    Book as trial session (₹{{ number_format($tutorProfile->trial_price, 0) }})
                                </label>
                            @endif

                            <div>
                                <x-input-label for="student_notes" value="Notes for your tutor (optional)" />
                                <textarea id="student_notes" name="student_notes" rows="2" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"></textarea>
                            </div>

                            <x-primary-button type="submit" class="w-full justify-center">Book session</x-primary-button>
                        </form>
                    @else
                        <p class="mt-5 text-sm text-gray-500 dark:text-gray-400">Only student accounts can book sessions.</p>
                    @endif
                @else
                    <a href="{{ route('login') }}" class="mt-5 block text-center w-full px-4 py-2 bg-indigo-600 rounded-md font-semibold text-sm text-white uppercase tracking-widest hover:bg-indigo-500">
                        Log in to book
                    </a>
                    <p class="mt-2 text-xs text-center text-gray-400">New here? <a href="{{ route('register') }}" class="text-indigo-600 dark:text-indigo-400 hover:underline">Create an account</a></p>
                @endauth
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function bookingWidget(slotsUrl) {
            return {
                date: '',
                slots: [],
                selectedSlot: '',
                loaded: false,
                async fetchSlots() {
                    if (!this.date) return;
                    this.loaded = false;
                    this.selectedSlot = '';
                    const res = await fetch(`${slotsUrl}?date=${this.date}`);
                    this.slots = await res.json();
                    this.loaded = true;
                },
            };
        }
    </script>
    @endpush
</x-public-layout>
