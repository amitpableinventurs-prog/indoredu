@props(['level'])

@php
$colors = [
    'beginner' => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-300',
    'intermediate' => 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900/40 dark:text-indigo-300',
    'advanced' => 'bg-purple-100 text-purple-800 dark:bg-purple-900/40 dark:text-purple-300',
    'crash_course' => 'bg-orange-100 text-orange-800 dark:bg-orange-900/40 dark:text-orange-300',
    'all_levels' => 'bg-gray-100 text-gray-700 dark:bg-gray-700/60 dark:text-gray-300',
];
$class = $colors[$level] ?? 'bg-gray-100 text-gray-700 dark:bg-gray-700/60 dark:text-gray-300';
$label = \App\Models\Course::LEVELS[$level] ?? ucfirst(str_replace('_', ' ', $level));
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium $class"]) }}>
    @if ($level === 'crash_course')
        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z"/></svg>
    @endif
    {{ $label }}
</span>
