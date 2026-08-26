<x-app-layout :title="$meta['title']">
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <x-game-icon :icon="$meta['icon']" :color="$meta['color']" class="w-11 h-11 text-xl" />
            <div>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">{{ $meta['title'] }}</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400">{{ $meta['tagline'] }}</p>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-4">
            <div class="flex items-center justify-between">
                <a href="{{ route('student.games.index') }}" class="text-sm font-medium text-indigo-600 dark:text-indigo-400 hover:underline">&larr; All games</a>
                <span class="inline-flex items-center gap-1 text-sm font-medium text-amber-600 dark:text-amber-400" data-best-score-display>
                    🏆 Best score: <span id="game-best-score">{{ $bestScore }}</span>
                </span>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-5 sm:p-6">
                @include('student.games.partials.'.$slug)
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            const GAME_SCORE_URL = @json(route('student.games.score', $slug));
            const GAME_CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]').content;

            async function saveGameScore(score) {
                try {
                    const res = await fetch(GAME_SCORE_URL, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': GAME_CSRF_TOKEN,
                            Accept: 'application/json',
                        },
                        body: JSON.stringify({ score: Math.max(0, Math.round(score)) }),
                    });
                    if (!res.ok) return null;
                    const data = await res.json();
                    const el = document.getElementById('game-best-score');
                    if (el) el.textContent = data.best_score;
                    return data;
                } catch (e) {
                    return null;
                }
            }
        </script>
    @endpush
</x-app-layout>
