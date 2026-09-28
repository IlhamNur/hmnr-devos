@props([
    'variant' => 'default',
])

@php
$variants = [
    'default' => 'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300 border border-transparent dark:border-gray-700',
    'primary' => 'bg-indigo-50 text-indigo-700 dark:bg-indigo-500/10 dark:text-indigo-400 border border-indigo-200 dark:border-indigo-500/20',
    'success' => 'bg-green-50 text-green-700 dark:bg-green-500/10 dark:text-green-400 border border-green-200 dark:border-green-500/20',
    'warning' => 'bg-yellow-50 text-yellow-700 dark:bg-yellow-500/10 dark:text-yellow-400 border border-yellow-200 dark:border-yellow-500/20',
    'danger' => 'bg-red-50 text-red-700 dark:bg-red-500/10 dark:text-red-400 border border-red-200 dark:border-red-500/20',
];
$classes = $variants[$variant] ?? $variants['default'];
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {$classes}"]) }}>
    {{ $slot }}
</span>
