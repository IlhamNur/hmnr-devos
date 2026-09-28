@props([
    'type' => 'button',
    'variant' => 'primary',
    'size' => 'md',
    'disabled' => false,
])

@php
$variants = [
    'primary' => 'bg-indigo-600 text-white hover:bg-indigo-700 focus:ring-indigo-500 dark:bg-indigo-500/20 dark:text-indigo-400 dark:border-indigo-500/30 dark:hover:bg-indigo-500/30 border border-transparent',
    'secondary' => 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50 focus:ring-indigo-500 dark:bg-[#161925] dark:text-gray-300 dark:border-gray-700 dark:hover:bg-gray-800 border',
    'danger' => 'bg-red-600 text-white hover:bg-red-700 focus:ring-red-500 dark:bg-red-500/20 dark:text-red-400 dark:border-red-500/30 dark:hover:bg-red-500/30 border border-transparent',
    'ghost' => 'text-gray-700 hover:bg-gray-100 focus:ring-gray-500 dark:text-gray-400 dark:hover:bg-gray-800 dark:hover:text-gray-200 border border-transparent',
];
$sizes = [
    'sm' => 'px-2.5 py-1.5 text-xs',
    'md' => 'px-4 py-2 text-sm',
    'lg' => 'px-5 py-2.5 text-sm',
];
$classes = $variants[$variant] ?? $variants['primary'];
$sizeClasses = $sizes[$size] ?? $sizes['md'];
@endphp

<button
    type="{{ $type }}"
    {{ $disabled ? 'disabled' : '' }}
    {{ $attributes->merge(['class' => "inline-flex items-center justify-center gap-2 font-medium rounded-md transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 dark:focus:ring-offset-[#0f111a] disabled:opacity-50 disabled:cursor-not-allowed {$classes} {$sizeClasses}"]) }}
>
    {{ $slot }}
</button>
