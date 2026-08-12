@props(['status'])

@php
$colors = [
    'pending' => 'bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-300',
    'confirmed' => 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900/40 dark:text-indigo-300',
    'completed' => 'bg-green-100 text-green-800 dark:bg-green-900/40 dark:text-green-300',
    'cancelled' => 'bg-gray-100 text-gray-700 dark:bg-gray-700/60 dark:text-gray-300',
    'no_show' => 'bg-red-100 text-red-800 dark:bg-red-900/40 dark:text-red-300',
    'approved' => 'bg-green-100 text-green-800 dark:bg-green-900/40 dark:text-green-300',
    'rejected' => 'bg-red-100 text-red-800 dark:bg-red-900/40 dark:text-red-300',
    'suspended' => 'bg-red-100 text-red-800 dark:bg-red-900/40 dark:text-red-300',
    'banned' => 'bg-red-100 text-red-800 dark:bg-red-900/40 dark:text-red-300',
    'active' => 'bg-green-100 text-green-800 dark:bg-green-900/40 dark:text-green-300',
    'published' => 'bg-green-100 text-green-800 dark:bg-green-900/40 dark:text-green-300',
    'draft' => 'bg-gray-100 text-gray-700 dark:bg-gray-700/60 dark:text-gray-300',
    'archived' => 'bg-gray-100 text-gray-700 dark:bg-gray-700/60 dark:text-gray-300',
    'processing' => 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900/40 dark:text-indigo-300',
    'completed_payment' => 'bg-green-100 text-green-800',
    'failed' => 'bg-red-100 text-red-800 dark:bg-red-900/40 dark:text-red-300',
    'refunded' => 'bg-gray-100 text-gray-700 dark:bg-gray-700/60 dark:text-gray-300',
    'paid' => 'bg-green-100 text-green-800 dark:bg-green-900/40 dark:text-green-300',
    'resolved' => 'bg-green-100 text-green-800 dark:bg-green-900/40 dark:text-green-300',
    'dismissed' => 'bg-gray-100 text-gray-700 dark:bg-gray-700/60 dark:text-gray-300',
    'verified' => 'bg-green-100 text-green-800 dark:bg-green-900/40 dark:text-green-300',
];
$class = $colors[$status] ?? 'bg-gray-100 text-gray-700 dark:bg-gray-700/60 dark:text-gray-300';
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium capitalize $class"]) }}>
    {{ str_replace('_', ' ', $status) }}
</span>
