@php $u = $tutor->user; @endphp
<a href="{{ route('tutors.show', $tutor) }}" class="block bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 hover:shadow-md hover:border-indigo-300 dark:hover:border-indigo-700 transition p-5">
    <div class="flex items-start gap-4">
        <div class="w-14 h-14 rounded-full bg-indigo-100 dark:bg-indigo-900/40 text-indigo-700 dark:text-indigo-300 flex items-center justify-center font-semibold text-lg shrink-0 overflow-hidden">
            @if ($u->avatar)
                <img src="{{ asset('storage/'.$u->avatar) }}" alt="{{ $u->name }}" class="w-full h-full object-cover">
            @else
                {{ $u->initials() }}
            @endif
        </div>
        <div class="min-w-0 flex-1">
            <div class="flex items-center justify-between gap-2">
                <h3 class="font-semibold text-gray-900 dark:text-gray-100 truncate">{{ $u->name }}</h3>
                @if ($tutor->is_featured)
                    <span class="shrink-0 text-xs font-medium text-amber-600 dark:text-amber-400">★ Featured</span>
                @endif
            </div>
            <p class="text-sm text-gray-500 dark:text-gray-400 truncate">{{ $tutor->headline ?: 'Tutor' }}</p>
            @if ($u->city || $u->country)
                <p class="text-xs text-gray-400 dark:text-gray-500 truncate">{{ collect([$u->city, $u->country])->filter()->join(', ') }}</p>
            @endif
            <div class="mt-2">
                <x-star-rating :rating="$tutor->rating_avg" :count="$tutor->rating_count" size="w-3.5 h-3.5" />
            </div>
        </div>
    </div>

    <div class="mt-3 flex flex-wrap gap-1.5">
        @foreach ($tutor->subjects->take(3) as $subject)
            <span class="px-2 py-0.5 rounded-full text-xs bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300">{{ $subject->name }}</span>
        @endforeach
    </div>

    <div class="mt-4 flex items-center justify-between">
        <span class="text-lg font-semibold text-gray-900 dark:text-gray-100">₹{{ number_format($tutor->hourly_rate, 0) }}<span class="text-sm font-normal text-gray-500 dark:text-gray-400">/hr</span></span>
        @if ($tutor->offers_trial)
            <span class="text-xs font-medium text-green-600 dark:text-green-400">Trial available</span>
        @endif
    </div>
</a>
