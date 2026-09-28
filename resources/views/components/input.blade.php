@props([
    'label' => null,
    'name' => null,
    'type' => 'text',
    'value' => null,
])

<div class="space-y-1">
    @if($label)
        <label for="{{ $name }}" class="block text-xs font-semibold text-gray-700 dark:text-gray-300 tracking-wide">{{ $label }}</label>
    @endif
    <input
        type="{{ $type }}"
        name="{{ $name }}"
        id="{{ $name }}"
        value="{{ old($name, $value) }}"
        {{ $attributes->merge(['class' => 'block w-full rounded-md border-gray-300 dark:border-gray-700 bg-white dark:bg-[#161925] text-gray-900 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 dark:focus:border-indigo-500 dark:focus:ring-indigo-500 sm:text-sm px-3 py-2 transition-colors disabled:opacity-50 disabled:bg-gray-50 dark:disabled:bg-[#11131a]']) }}
    >
    @error($name)
        <p class="text-xs font-medium text-red-600 dark:text-red-400 mt-1">{{ $message }}</p>
    @enderror
</div>
