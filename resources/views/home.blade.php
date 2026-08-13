<x-public-layout>
    <!-- Hero -->
    <section class="bg-gradient-to-b from-indigo-50 to-white dark:from-gray-800 dark:to-gray-900">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 sm:py-24 text-center">
            <h1 class="text-4xl sm:text-5xl font-bold tracking-tight text-gray-900 dark:text-gray-50">
                Learn from tutors who <span class="text-indigo-600 dark:text-indigo-400">get results</span>
            </h1>
            <p class="mt-4 max-w-2xl mx-auto text-lg text-gray-600 dark:text-gray-300">
                Book 1-on-1 sessions or courses with verified tutors in math, science, languages, coding, test prep and more — on your schedule, at your budget.
            </p>

            <form action="{{ route('tutors.index') }}" method="GET" class="mt-8 max-w-xl mx-auto flex flex-col sm:flex-row gap-2">
                <input type="text" name="q" placeholder="Search by subject or tutor name…"
                       class="flex-1 rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                <button type="submit" class="inline-flex items-center justify-center px-6 py-2.5 bg-indigo-600 border border-transparent rounded-md font-semibold text-sm text-white uppercase tracking-widest hover:bg-indigo-500 transition">
                    Search
                </button>
            </form>

            <div class="mt-6 flex flex-wrap justify-center gap-2">
                @foreach ($categories->take(6) as $category)
                    <a href="{{ route('tutors.index', ['subject' => $category->subjects->first()?->slug]) }}"
                       class="px-3 py-1.5 rounded-full text-sm bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-300 hover:border-indigo-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition">
                        {{ $category->name }}
                    </a>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Stats strip -->
    <section class="border-y border-gray-100 dark:border-gray-800 bg-white dark:bg-gray-800/50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 grid grid-cols-2 sm:grid-cols-4 gap-6 text-center">
            <div>
                <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $stats['tutors'] }}+</p>
                <p class="text-sm text-gray-500 dark:text-gray-400">Verified tutors</p>
            </div>
            <div>
                <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $stats['subjects'] }}+</p>
                <p class="text-sm text-gray-500 dark:text-gray-400">Subjects</p>
            </div>
            <div>
                <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $stats['sessions_completed'] }}+</p>
                <p class="text-sm text-gray-500 dark:text-gray-400">Sessions completed</p>
            </div>
            <div>
                <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ number_format($stats['avg_rating'], 1) }}/5</p>
                <p class="text-sm text-gray-500 dark:text-gray-400">Average rating</p>
            </div>
        </div>
    </section>

    <!-- Browse by category -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <div class="text-center mb-10">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Browse by category</h2>
            <p class="mt-2 text-gray-500 dark:text-gray-400">Whatever you're learning, there's a tutor ready to help.</p>
        </div>

        @php
            $categoryIcons = [
                'Mathematics' => '📐',
                'Science' => '🔬',
                'Languages' => '🗣️',
                'Computer Science' => '💻',
                'Test Prep' => '📝',
                'Arts & Music' => '🎨',
            ];
        @endphp

        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4">
            @foreach ($categories as $category)
                <a href="{{ route('tutors.index', ['subject' => $category->subjects->first()?->slug]) }}"
                   class="group flex flex-col items-center text-center gap-2 p-5 rounded-xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 hover:border-indigo-400 dark:hover:border-indigo-500 hover:shadow-md transition">
                    <span class="text-3xl">{{ $categoryIcons[$category->name] ?? '📚' }}</span>
                    <span class="font-medium text-gray-900 dark:text-gray-100 text-sm group-hover:text-indigo-600 dark:group-hover:text-indigo-400">{{ $category->name }}</span>
                    <span class="text-xs text-gray-400 dark:text-gray-500">{{ $category->subjects_count }} subjects</span>
                </a>
            @endforeach
        </div>
    </section>

    <!-- Featured tutors -->
    <section class="bg-white dark:bg-gray-800/50 border-y border-gray-100 dark:border-gray-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Featured tutors</h2>
                <a href="{{ route('tutors.index') }}" class="text-sm font-medium text-indigo-600 dark:text-indigo-400 hover:underline">Browse all &rarr;</a>
            </div>

            @if ($featuredTutors->isEmpty())
                <x-empty-state title="No tutors yet" description="Check back soon — new tutors are joining every day." />
            @else
                <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach ($featuredTutors as $tutor)
                        @include('tutors._card', ['tutor' => $tutor])
                    @endforeach
                </div>
            @endif
        </div>
    </section>

    <!-- How it works -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100 text-center mb-10">How IndorEdu works</h2>
        <div class="grid sm:grid-cols-3 gap-8 text-center">
            <div>
                <div class="mx-auto w-10 h-10 rounded-full bg-indigo-100 dark:bg-indigo-900/40 text-indigo-600 dark:text-indigo-400 flex items-center justify-center font-bold">1</div>
                <h3 class="mt-4 font-semibold text-gray-900 dark:text-gray-100">Find your tutor</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Filter by subject, budget, location and rating to find the right fit — try a trial session first.</p>
            </div>
            <div>
                <div class="mx-auto w-10 h-10 rounded-full bg-indigo-100 dark:bg-indigo-900/40 text-indigo-600 dark:text-indigo-400 flex items-center justify-center font-bold">2</div>
                <h3 class="mt-4 font-semibold text-gray-900 dark:text-gray-100">Book & pay securely</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Pick a time from their live availability and pay safely through Stripe or PayPal.</p>
            </div>
            <div>
                <div class="mx-auto w-10 h-10 rounded-full bg-indigo-100 dark:bg-indigo-900/40 text-indigo-600 dark:text-indigo-400 flex items-center justify-center font-bold">3</div>
                <h3 class="mt-4 font-semibold text-gray-900 dark:text-gray-100">Learn & track progress</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Meet over video, message your tutor anytime, and track attendance and progress reports.</p>
            </div>
        </div>
    </section>

    <!-- Why IndorEdu -->
    <section class="bg-white dark:bg-gray-800/50 border-y border-gray-100 dark:border-gray-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100 text-center mb-10">Why learners choose IndorEdu</h2>
            <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="p-5">
                    <div class="w-10 h-10 rounded-lg bg-indigo-100 dark:bg-indigo-900/40 text-indigo-600 dark:text-indigo-400 flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <h3 class="mt-4 font-semibold text-gray-900 dark:text-gray-100">Verified tutors</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Every tutor is identity-checked and credential-reviewed before they can teach.</p>
                </div>
                <div class="p-5">
                    <div class="w-10 h-10 rounded-lg bg-indigo-100 dark:bg-indigo-900/40 text-indigo-600 dark:text-indigo-400 flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    </div>
                    <h3 class="mt-4 font-semibold text-gray-900 dark:text-gray-100">Secure payments</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Pay safely through Stripe or PayPal — funds are only released after your session.</p>
                </div>
                <div class="p-5">
                    <div class="w-10 h-10 rounded-lg bg-indigo-100 dark:bg-indigo-900/40 text-indigo-600 dark:text-indigo-400 flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    </div>
                    <h3 class="mt-4 font-semibold text-gray-900 dark:text-gray-100">Flexible scheduling</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Book around your life with live tutor calendars and instant confirmations.</p>
                </div>
                <div class="p-5">
                    <div class="w-10 h-10 rounded-lg bg-indigo-100 dark:bg-indigo-900/40 text-indigo-600 dark:text-indigo-400 flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    </div>
                    <h3 class="mt-4 font-semibold text-gray-900 dark:text-gray-100">Try before you commit</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Many tutors offer a low-cost or free trial session, so there's no risk finding your fit.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonials -->
    @if ($testimonials->isNotEmpty())
        <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
            <div class="text-center mb-10">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100">What our students say</h2>
                <p class="mt-2 text-gray-500 dark:text-gray-400">Real reviews from real sessions on IndorEdu.</p>
            </div>
            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach ($testimonials as $review)
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6 flex flex-col">
                        <x-star-rating :rating="$review->rating" size="w-4 h-4" />
                        <p class="mt-3 text-sm text-gray-600 dark:text-gray-300 flex-1">&ldquo;{{ $review->comment }}&rdquo;</p>
                        <div class="mt-4 flex items-center gap-3">
                            <div class="w-9 h-9 rounded-full bg-indigo-100 dark:bg-indigo-900/40 text-indigo-700 dark:text-indigo-300 flex items-center justify-center text-xs font-semibold shrink-0">
                                {{ $review->student->initials() }}
                            </div>
                            <div class="min-w-0">
                                <p class="text-sm font-medium text-gray-900 dark:text-gray-100 truncate">{{ $review->student->name }}</p>
                                <p class="text-xs text-gray-400 dark:text-gray-500 truncate">Student of {{ $review->tutor->name }}</p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>
    @endif

    <!-- FAQ -->
    <section class="bg-white dark:bg-gray-800/50 border-y border-gray-100 dark:border-gray-800">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100 text-center mb-10">Frequently asked questions</h2>

            <div class="space-y-3" x-data="{ open: 1 }">
                @foreach ([
                    ['q' => 'How do I know a tutor is qualified?', 'a' => 'Every tutor goes through identity verification and can upload certificates or credentials, which our team reviews before their profile goes live. You can see their verification status, ratings, and reviews on every profile.'],
                    ['q' => 'What if I don\'t like my first session?', 'a' => 'Many tutors offer a discounted or free trial session so you can find the right fit with no risk. If a paid session doesn\'t go well, you can message support and we\'ll help make it right.'],
                    ['q' => 'How do payments and refunds work?', 'a' => 'Payments are processed securely through Stripe or PayPal at the time of booking. If you cancel an upcoming session, eligible refunds are issued automatically back through the original payment method.'],
                    ['q' => 'Can I message my tutor before booking?', 'a' => 'Yes — once you\'re signed in, you can message any tutor directly from their profile to ask questions before you book a session.'],
                    ['q' => 'How do I become a tutor on IndorEdu?', 'a' => 'Sign up as a tutor, complete your profile with your subjects and experience, and submit an identity document for verification. Once approved, you can set your availability and start accepting bookings.'],
                ] as $i => $faq)
                    <div class="border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden">
                        <button type="button" @click="open = (open === {{ $i }} ? null : {{ $i }})"
                                class="w-full flex items-center justify-between gap-4 px-5 py-4 text-left bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700/40">
                            <span class="font-medium text-gray-900 dark:text-gray-100">{{ $faq['q'] }}</span>
                            <svg class="w-4 h-4 shrink-0 text-gray-400 transition-transform" :class="{ 'rotate-180': open === {{ $i }} }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="open === {{ $i }}" x-collapse class="px-5 pb-4 text-sm text-gray-600 dark:text-gray-300 bg-white dark:bg-gray-800">
                            {{ $faq['a'] }}
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- CTA for tutors -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <div class="rounded-2xl bg-indigo-600 px-8 py-12 text-center">
            <h2 class="text-2xl font-bold text-white">Are you a tutor?</h2>
            <p class="mt-2 text-indigo-100 max-w-xl mx-auto">Set your own rate, manage your own schedule, and get paid securely. Join IndorEdu and start teaching students worldwide.</p>
            <a href="{{ route('register', ['role' => 'tutor']) }}" class="mt-6 inline-flex items-center px-6 py-2.5 bg-white border border-transparent rounded-md font-semibold text-sm text-indigo-600 uppercase tracking-widest hover:bg-indigo-50 transition">
                Start teaching
            </a>
        </div>
    </section>
</x-public-layout>
