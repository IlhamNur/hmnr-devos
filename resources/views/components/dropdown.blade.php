@props([
    'align' => 'right',
    'width' => '48',
])

@php
$alignClasses = $align === 'left' ? 'left-0 origin-top-left' : 'right-0 origin-top-right';
$widthClasses = [
    '48' => 'w-48',
    '56' => 'w-56',
    '64' => 'w-64',
][$width] ?? 'w-48';
@endphp

<div x-data="{ open: false }" @click.away="open = false" class="relative inline-block text-left">
    <div @click="open = !open">
        {{ $trigger }}
    </div>

    <div
        x-show="open"
        x-transition:enter="transition ease-out duration-100"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-75"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        x-cloak
        class="absolute z-50 mt-2 {{ $widthClasses }} {{ $alignClasses }} rounded-md bg-white dark:bg-[#161925] border border-gray-200 dark:border-gray-800 shadow-sm py-1"
    >
        {{ $slot }}
    </div>
</div>
