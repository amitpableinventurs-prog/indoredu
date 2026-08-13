<x-public-layout :title="$course->title">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-10 grid md:grid-cols-3 gap-8">
        <div class="md:col-span-2 space-y-6">
            <div>
                <div class="flex items-center gap-2 flex-wrap">
                    <p class="text-sm text-indigo-600 dark:text-indigo-400 font-medium">
                        {{ $course->subject->name ?? '' }}
                        @if ($course->grade !== \App\Models\Course::GRADE_ALL_GRADES)
                            &middot; {{ $course->grade_label }}
                        @endif
                    </p>
                    <x-level-badge :level="$course->level" />
                </div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mt-1">{{ $course->title }}</h1>
                <div class="flex items-center gap-3 mt-1 flex-wrap">
                    <a href="{{ route('tutors.show', $course->tutorProfile) }}" class="text-sm text-gray-500 dark:text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400 inline-block">
                        by {{ $course->tutorProfile->user->name }}
                    </a>
                    @if ($course->rating_count > 0)
                        <a href="#reviews" class="inline-block">
                            <x-star-rating :rating="$course->rating_avg" :count="$course->rating_count" size="w-4 h-4" />
                        </a>
                    @endif
                </div>
            </div>

            @if ($course->isCrashCourse())
                <div class="flex items-start gap-3 rounded-lg border border-orange-200 dark:border-orange-800 bg-orange-50 dark:bg-orange-900/20 p-4 text-sm text-orange-800 dark:text-orange-300">
                    <svg class="w-5 h-5 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z"/></svg>
                    <p>This is a <strong>crash course</strong> — an intensive, fast-paced track designed to cover key concepts quickly ahead of exams.</p>
                </div>
            @endif

            @if ($course->cover_image)
                <img src="{{ asset('storage/'.$course->cover_image) }}" class="w-full rounded-lg" alt="{{ $course->title }}" loading="lazy">
            @endif

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <h2 class="font-semibold text-gray-900 dark:text-gray-100 mb-2">About this course</h2>
                <p class="text-sm text-gray-600 dark:text-gray-300 whitespace-pre-line">{{ $course->description ?: 'No description provided.' }}</p>
            </div>

            @if ($course->subject && ($course->subject->syllabus_pdf || $course->subject->syllabus))
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <h2 class="font-semibold text-gray-900 dark:text-gray-100 mb-2">Syllabus</h2>
                    @if ($course->subject->syllabus_pdf)
                        <a href="{{ asset('storage/'.$course->subject->syllabus_pdf) }}" target="_blank" rel="noopener"
                           class="inline-flex items-center gap-2 text-sm font-medium text-indigo-600 dark:text-indigo-400 hover:underline">
                            View syllabus PDF
                        </a>
                    @endif
                    @if ($course->subject->syllabus)
                        <p class="text-sm text-gray-600 dark:text-gray-300 whitespace-pre-line mt-3">{{ $course->subject->syllabus }}</p>
                    @endif
                </div>
            @endif

            <div id="reviews" class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="font-semibold text-gray-900 dark:text-gray-100">Reviews</h2>
                    @if ($course->rating_count > 0)
                        <x-star-rating :rating="$course->rating_avg" :count="$course->rating_count" />
                    @endif
                </div>

                @auth
                    @if ($isEnrolled)
                        <form action="{{ route('courses.reviews.store', $course) }}" method="POST" class="space-y-4 mb-6 pb-6 border-b border-gray-100 dark:border-gray-700">
                            @csrf
                            <div>
                                <x-input-label for="rating" value="Your rating" />
                                <select id="rating" name="rating" required class="mt-1 block w-full sm:w-40 rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                    @foreach ([5 => 'Excellent', 4 => 'Good', 3 => 'Average', 2 => 'Below average', 1 => 'Poor'] as $value => $label)
                                        <option value="{{ $value }}" @selected(old('rating', $myReview->rating ?? 5) == $value)>{{ $value }} &ndash; {{ $label }}</option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('rating')" class="mt-1" />
                            </div>
                            <div>
                                <x-input-label for="comment" value="Comment (optional)" />
                                <textarea id="comment" name="comment" rows="3" maxlength="2000"
                                          class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">{{ old('comment', $myReview->comment ?? '') }}</textarea>
                                <x-input-error :messages="$errors->get('comment')" class="mt-1" />
                            </div>
                            <x-primary-button type="submit">{{ $myReview ? 'Update review' : 'Submit review' }}</x-primary-button>
                        </form>
                    @endif
                @endauth

                @if ($course->reviews->isEmpty())
                    <p class="text-sm text-gray-500 dark:text-gray-400">No reviews yet.</p>
                @else
                    <div class="space-y-5">
                        @foreach ($course->reviews as $review)
                            <div class="{{ ! $loop->last ? 'pb-5 border-b border-gray-100 dark:border-gray-700' : '' }}">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-indigo-100 dark:bg-indigo-900/40 text-indigo-700 dark:text-indigo-300 flex items-center justify-center text-xs font-semibold shrink-0">
                                        {{ $review->studentProfile->user->initials() }}
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $review->studentProfile->user->name }}</p>
                                        <div class="flex items-center gap-2">
                                            <x-star-rating :rating="$review->rating" size="w-3.5 h-3.5" />
                                            <span class="text-xs text-gray-400 dark:text-gray-500">{{ $review->created_at->format('M j, Y') }}</span>
                                        </div>
                                    </div>
                                </div>
                                @if ($review->comment)
                                    <p class="text-sm text-gray-600 dark:text-gray-300 mt-2 ml-11">{{ $review->comment }}</p>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        <div class="md:col-span-1">
            <div class="sticky top-6 bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">₹{{ number_format($course->price, 0) }}</p>
                <ul class="mt-4 space-y-2 text-sm text-gray-600 dark:text-gray-300">
                    <li>{{ $course->total_sessions }} session{{ $course->total_sessions > 1 ? 's' : '' }}</li>
                    <li>{{ $course->duration_minutes }} minutes each</li>
                    <li>{{ $course->is_group ? 'Group course (max '.$course->max_students.' students)' : '1-on-1' }}</li>
                </ul>

                @auth
                    @if (auth()->user()->isStudent())
                        <form action="{{ route('courses.enroll', $course) }}" method="POST" class="mt-5">
                            @csrf
                            <x-primary-button type="submit" class="w-full justify-center">Enroll now</x-primary-button>
                        </form>
                    @else
                        <p class="mt-5 text-sm text-gray-500 dark:text-gray-400">Only student accounts can enroll.</p>
                    @endif
                @else
                    <a href="{{ route('login') }}" class="mt-5 block text-center w-full px-4 py-2 bg-indigo-600 rounded-md font-semibold text-sm text-white uppercase tracking-widest hover:bg-indigo-500">
                        Log in to enroll
                    </a>
                @endauth
            </div>
        </div>
    </div>
</x-public-layout>
