@props(['title', 'description' => null])

<div class="text-center py-12">
    <p class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $title }}</p>
    @if ($description)
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ $description }}</p>
    @endif
    @isset($action)
        <div class="mt-4">{{ $action }}</div>
    @endisset
</div>
