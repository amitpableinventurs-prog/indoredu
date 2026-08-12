@props(['title' => null, 'subtitle' => null])

<div {{ $attributes->merge(['class' => 'bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700']) }}>
    @if ($title)
        <div class="px-4 sm:px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
            <div>
                <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100">{{ $title }}</h3>
                @if ($subtitle)
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ $subtitle }}</p>
                @endif
            </div>
            @isset($action)
                <div>{{ $action }}</div>
            @endisset
        </div>
    @endif
    <div class="p-4 sm:p-6">
        {{ $slot }}
    </div>
</div>
