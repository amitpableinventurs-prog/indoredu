@props(['label', 'value'])

<div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4 sm:p-5">
    <p class="text-sm text-gray-500 dark:text-gray-400">{{ $label }}</p>
    <p class="mt-1 text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ $value }}</p>
</div>
