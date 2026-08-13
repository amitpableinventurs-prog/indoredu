<x-public-layout :title="'Courses'">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-2 mb-6">
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-gray-100">Courses</h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Structured, multi-session courses from verified tutors — including fast-paced crash courses for exam prep.</p>
            </div>
            <p class="text-sm text-gray-400 dark:text-gray-500 shrink-0">{{ $courses->total() }} course{{ $courses->total() === 1 ? '' : 's' }} found</p>
        </div>

        <form action="{{ route('courses.index') }}" method="GET" class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm p-4 sm:p-5 mb-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3 items-end">
                <div class="lg:col-span-2">
                    <x-input-label for="q" value="Search" />
                    <x-text-input id="q" name="q" class="mt-1 block w-full" value="{{ request('q') }}" placeholder="Course title" />
                </div>
                <div>
                    <x-input-label for="subject" value="Subject" />
                    <select id="subject" name="subject" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                        <option value="">All subjects</option>
                        @foreach ($categories as $category)
                            <optgroup label="{{ $category->name }}">
                                @foreach ($category->subjects as $subject)
                                    <option value="{{ $subject->slug }}" @selected(request('subject') === $subject->slug)>{{ $subject->name }}</option>
                                @endforeach
                            </optgroup>
                        @endforeach
                    </select>
                </div>
                <div>
                    <x-input-label for="grade" value="Grade / class" />
                    <select id="grade" name="grade" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                        <option value="">All grades</option>
                        @foreach (\App\Models\Course::GRADES as $value => $label)
                            @if ($value !== 'all_grades')
                                <option value="{{ $value }}" @selected(request('grade') === $value)>{{ $label }}</option>
                            @endif
                        @endforeach
                    </select>
                </div>
                <div>
                    <x-input-label for="sort" value="Sort by" />
                    <select id="sort" name="sort" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                        <option value="" @selected(request('sort', '') === '')>Newest</option>
                        <option value="rating" @selected(request('sort') === 'rating')>Highest rated</option>
                        <option value="price_asc" @selected(request('sort') === 'price_asc')>Price: Low to high</option>
                        <option value="price_desc" @selected(request('sort') === 'price_desc')>Price: High to low</option>
                    </select>
                </div>
            </div>

            <div class="mt-4 flex flex-wrap items-center justify-between gap-3">
                <div class="flex flex-wrap gap-2">
                    @foreach (['' => 'All'] + \App\Models\Course::LEVELS as $value => $label)
                        <a href="{{ route('courses.index', array_filter(request()->except('level', 'page') + ['level' => $value])) }}"
                           class="px-3 py-1 rounded-full text-xs font-medium border transition {{ request('level', '') === $value ? 'bg-indigo-600 border-indigo-600 text-white' : 'bg-white dark:bg-gray-900 border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-300 hover:border-indigo-400 hover:text-indigo-600 dark:hover:text-indigo-400' }}">
                            @if ($value === 'crash_course') ⚡ @endif{{ $label }}
                        </a>
                    @endforeach
                </div>
                <div class="flex items-center gap-3">
                    @if (request()->anyFilled(['q', 'subject', 'level', 'grade', 'sort']))
                        <a href="{{ route('courses.index') }}" class="text-sm text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">Reset</a>
                    @endif
                    <x-primary-button type="submit">Search</x-primary-button>
                </div>
            </div>
        </form>

        @if ($courses->isEmpty())
            <x-empty-state title="No courses found" description="Try a different search or check back soon." />
        @else
            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach ($courses as $course)
                    <a href="{{ route('courses.show', $course) }}" class="group flex flex-col bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 hover:shadow-lg hover:-translate-y-0.5 hover:border-indigo-300 dark:hover:border-indigo-700 transition overflow-hidden">
                        <div class="relative h-36 bg-indigo-50 dark:bg-indigo-950/40 flex items-center justify-center overflow-hidden">
                            @if ($course->cover_image)
                                <img src="{{ asset('storage/'.$course->cover_image) }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-300" alt="{{ $course->title }}" loading="lazy">
                            @else
                                <span class="text-indigo-300 dark:text-indigo-700 text-3xl font-bold">{{ substr($course->title, 0, 1) }}</span>
                            @endif
                            <div class="absolute top-2 left-2">
                                <x-level-badge :level="$course->level" class="shadow-sm" />
                            </div>
                        </div>
                        <div class="p-4 flex flex-col flex-1">
                            <p class="text-xs text-gray-400 dark:text-gray-500">{{ $course->subject->name ?? '' }}</p>
                            <h3 class="font-semibold text-gray-900 dark:text-gray-100 mt-0.5 leading-snug group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition">{{ $course->title }}</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">by {{ $course->tutorProfile->user->name }}</p>
                            @if ($course->rating_count > 0)
                                <div class="mt-1.5">
                                    <x-star-rating :rating="$course->rating_avg" :count="$course->rating_count" size="w-3.5 h-3.5" />
                                </div>
                            @endif
                            <div class="mt-3 pt-3 border-t border-gray-100 dark:border-gray-700 flex items-center justify-between">
                                <span class="font-semibold text-gray-900 dark:text-gray-100">₹{{ number_format($course->price, 0) }}</span>
                                <span class="text-xs text-gray-400">{{ $course->enrollments_count }} enrolled</span>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
            <div class="mt-8">{{ $courses->links() }}</div>
        @endif
    </div>
</x-public-layout>
