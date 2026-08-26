<x-app-layout :title="'Educational Games'">
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Educational Games') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4 sm:p-6">
                <p class="text-sm text-gray-600 dark:text-gray-300">
                    Sharpen what you're learning between tutoring sessions. Each game takes a couple of minutes,
                    tracks your personal best, and is built to reinforce the subjects tutors on IndorEdu teach.
                </p>
            </div>

            <div class="grid sm:grid-cols-2 gap-4 sm:gap-6">
                @foreach ($games as $slug => $meta)
                    @php $score = $scores->get($slug); @endphp
                    <a href="{{ route('student.games.show', $slug) }}"
                       class="group bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-5 hover:border-indigo-300 dark:hover:border-indigo-500/50 hover:shadow-md transition">
                        <div class="flex items-start gap-4">
                            <x-game-icon :icon="$meta['icon']" :color="$meta['color']" class="w-14 h-14 text-2xl" />
                            <div class="min-w-0 flex-1">
                                <div class="flex items-center justify-between gap-2">
                                    <h3 class="font-semibold text-gray-900 dark:text-gray-100 group-hover:text-indigo-600 dark:group-hover:text-indigo-400">
                                        {{ $meta['title'] }}
                                    </h3>
                                    <span class="shrink-0 text-[11px] font-medium uppercase tracking-wide text-gray-400 dark:text-gray-500">{{ $meta['subject'] }}</span>
                                </div>
                                <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">{{ $meta['tagline'] }}</p>
                                <p class="text-sm text-gray-600 dark:text-gray-300 mt-2">{{ $meta['description'] }}</p>

                                <div class="mt-4 flex items-center justify-between">
                                    @if ($score)
                                        <span class="inline-flex items-center gap-1 text-xs font-medium text-amber-600 dark:text-amber-400">
                                            🏆 Best: {{ $score->best_score }} · {{ $score->plays }} {{ Str::plural('play', $score->plays) }}
                                        </span>
                                    @else
                                        <span class="text-xs text-gray-400 dark:text-gray-500">Not played yet</span>
                                    @endif
                                    <span class="text-sm font-medium text-indigo-600 dark:text-indigo-400">Play &rarr;</span>
                                </div>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    </div>
</x-app-layout>
