@props([
    'type' => 'info',
    'dismissible' => false,
])

@php
$types = [
    'info' => ['bg' => 'bg-blue-50 dark:bg-blue-900/20', 'border' => 'border-blue-200 dark:border-blue-800', 'text' => 'text-blue-800 dark:text-blue-300', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>'],
    'success' => ['bg' => 'bg-green-50 dark:bg-green-900/20', 'border' => 'border-green-200 dark:border-green-800', 'text' => 'text-green-800 dark:text-green-300', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>'],
    'warning' => ['bg' => 'bg-yellow-50 dark:bg-yellow-900/20', 'border' => 'border-yellow-200 dark:border-yellow-800', 'text' => 'text-yellow-800 dark:text-yellow-300', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>'],
    'error' => ['bg' => 'bg-red-50 dark:bg-red-900/20', 'border' => 'border-red-200 dark:border-red-800', 'text' => 'text-red-800 dark:text-red-300', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>'],
];
$config = $types[$type] ?? $types['info'];
@endphp

<div
    {{ $attributes->merge(['class' => "rounded-lg border p-4 {$config['bg']} {$config['border']}"]) }}
    @if($dismissible) x-data="{ show: true }" x-show="show" x-transition @endif
>
    <div class="flex">
        <svg class="h-5 w-5 {{ $config['text'] }} shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">{!! $config['icon'] !!}</svg>
        <div class="ml-3 flex-1">
            <p class="text-sm {{ $config['text'] }}">{{ $slot }}</p>
        </div>
        @if($dismissible)
            <button @click="show = false" class="ml-3 {{ $config['text'] }} hover:opacity-75">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        @endif
    </div>
</div>
