@props([
    'title' => 'No data',
    'description' => null,
    'icon' => null,
])

<div {{ $attributes->merge(['class' => 'flex flex-col items-center justify-center text-center py-12 px-4 rounded-lg border border-dashed border-gray-300 dark:border-gray-800 bg-gray-50/50 dark:bg-[#13151f]/50']) }}>
    @if($icon)
        <div class="mb-4 text-gray-400 dark:text-gray-500">
            {!! $icon !!}
        </div>
    @else
        <div class="mb-4 text-gray-400 dark:text-gray-600">
            <svg class="w-10 h-10 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
            </svg>
        </div>
    @endif
    
    <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-200">{{ $title }}</h3>
    
    @if($description)
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400 max-w-sm mx-auto">{{ $description }}</p>
    @endif
    
    @if($slot->isNotEmpty())
        <div class="mt-6">
            {{ $slot }}
        </div>
    @endif
</div>
