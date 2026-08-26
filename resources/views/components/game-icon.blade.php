@props(['icon', 'color' => 'indigo'])
@php
    $classes = match ($color) {
        'indigo' => 'bg-indigo-100 text-indigo-700 dark:bg-indigo-500/20 dark:text-indigo-300',
        'emerald' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-300',
        'amber' => 'bg-amber-100 text-amber-700 dark:bg-amber-500/20 dark:text-amber-300',
        'rose' => 'bg-rose-100 text-rose-700 dark:bg-rose-500/20 dark:text-rose-300',
        default => 'bg-gray-100 text-gray-700 dark:bg-gray-500/20 dark:text-gray-300',
    };
@endphp
<div {{ $attributes->merge(['class' => "flex items-center justify-center rounded-lg shrink-0 $classes"]) }}>
    {{ $icon }}
</div>
