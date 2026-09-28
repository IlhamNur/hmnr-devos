@props([
    'title' => null,
    'padding' => true,
])

<div {{ $attributes->merge(['class' => 'bg-white dark:bg-[#161925] rounded-lg border border-gray-200 dark:border-gray-800 overflow-hidden']) }}>
    @if($title)
        <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-800 bg-gray-50/50 dark:bg-[#13151f]">
            <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100 tracking-wide">{{ $title }}</h3>
        </div>
    @endif
    <div class="{{ $padding ? 'p-5' : '' }}">
        {{ $slot }}
    </div>
</div>
