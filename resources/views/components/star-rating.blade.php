@props(['rating' => 0, 'count' => null, 'size' => 'w-4 h-4'])

<div class="inline-flex items-center gap-1">
    <div class="flex text-amber-400">
        @for ($i = 1; $i <= 5; $i++)
            <svg class="{{ $size }}" viewBox="0 0 20 20" fill="{{ $i <= round($rating) ? 'currentColor' : 'none' }}" stroke="currentColor" stroke-width="1">
                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.958a1 1 0 00.95.69h4.162c.969 0 1.371 1.24.588 1.81l-3.368 2.446a1 1 0 00-.363 1.118l1.287 3.957c.3.922-.755 1.688-1.538 1.118l-3.367-2.446a1 1 0 00-1.176 0l-3.367 2.446c-.783.57-1.838-.196-1.538-1.118l1.287-3.957a1 1 0 00-.363-1.118L2.063 9.385c-.783-.57-.38-1.81.588-1.81h4.163a1 1 0 00.95-.69l1.285-3.958z"/>
            </svg>
        @endfor
    </div>
    <span class="text-sm text-gray-600 dark:text-gray-400">{{ number_format($rating, 1) }}</span>
    @if (! is_null($count))
        <span class="text-sm text-gray-400 dark:text-gray-500">({{ $count }})</span>
    @endif
</div>
