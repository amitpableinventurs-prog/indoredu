<x-public-layout :title="'Find Tutors'">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-6">Find your tutor</h1>

        <form action="{{ route('tutors.index') }}" method="GET" class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-4 mb-8 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-3 items-end">
            <div class="lg:col-span-2">
                <x-input-label for="q" value="Search" />
                <x-text-input id="q" name="q" class="mt-1 block w-full" value="{{ request('q') }}" placeholder="Subject or tutor name" />
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
                <x-input-label for="city" value="City" />
                <x-text-input id="city" name="city" class="mt-1 block w-full" value="{{ request('city') }}" placeholder="Any" />
            </div>
            <div>
                <x-input-label for="max_rate" value="Max rate (₹/hr)" />
                <x-text-input id="max_rate" name="max_rate" type="number" class="mt-1 block w-full" value="{{ request('max_rate') }}" placeholder="Any" />
            </div>
            <div>
                <x-input-label for="sort" value="Sort by" />
                <select id="sort" name="sort" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                    <option value="rating" @selected(request('sort', 'rating') === 'rating')>Top rated</option>
                    <option value="rate_low" @selected(request('sort') === 'rate_low')>Price: low to high</option>
                    <option value="rate_high" @selected(request('sort') === 'rate_high')>Price: high to low</option>
                    <option value="experience" @selected(request('sort') === 'experience')>Most experienced</option>
                </select>
            </div>
            <div class="lg:col-span-6 flex items-center justify-between">
                <label class="inline-flex items-center gap-2 text-sm text-gray-600 dark:text-gray-300">
                    <input type="checkbox" name="trial_only" value="1" @checked(request('trial_only')) class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                    Offers free/trial session
                </label>
                <div class="flex gap-2">
                    <a href="{{ route('tutors.index') }}" class="text-sm text-gray-500 hover:text-gray-700 dark:hover:text-gray-300">Reset</a>
                    <x-primary-button type="submit">Search</x-primary-button>
                </div>
            </div>
        </form>

        @if ($tutors->isEmpty())
            <x-empty-state title="No tutors match your filters" description="Try widening your search criteria." />
        @else
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">{{ $tutors->total() }} tutors found</p>
            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach ($tutors as $tutor)
                    @include('tutors._card', ['tutor' => $tutor])
                @endforeach
            </div>
            <div class="mt-8">{{ $tutors->links() }}</div>
        @endif
    </div>
</x-public-layout>
