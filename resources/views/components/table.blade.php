@props([
    'headers' => [],
])

<div class="overflow-x-auto w-full">
    <table class="min-w-full text-left text-sm whitespace-nowrap">
        @if(count($headers) > 0)
            <thead class="bg-gray-50/80 dark:bg-[#13151f] border-b border-gray-200 dark:border-gray-800 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                <tr>
                    @foreach($headers as $header)
                        <th scope="col" class="px-5 py-3">{{ $header }}</th>
                    @endforeach
                </tr>
            </thead>
        @endif
        <tbody class="divide-y divide-gray-200 dark:divide-gray-800 bg-white dark:bg-[#161925]">
            {{ $slot }}
        </tbody>
    </table>
</div>
