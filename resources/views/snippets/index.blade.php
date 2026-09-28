<x-app-layout>
    <div class="space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100 tracking-tight">Snippet Vault</h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Save and organize your reusable code snippets.</p>
            </div>
            <div>
                <a href="{{ route('snippets.create') }}">
                    <x-button variant="primary">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        New Snippet
                    </x-button>
                </a>
            </div>
        </div>

        <x-card :padding="false">
            <div class="p-4 border-b border-gray-200 dark:border-gray-800 bg-gray-50/50 dark:bg-[#13151f]">
                <form method="GET" action="{{ route('snippets.index') }}" class="flex flex-col lg:flex-row items-center gap-4">
                    <div class="w-full lg:flex-1">
                        <div class="relative">
                            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search snippets..." class="w-full pl-10 pr-4 py-2 text-sm rounded-md border-gray-300 dark:border-gray-700 bg-white dark:bg-[#161925] text-gray-900 dark:text-gray-100 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                        </div>
                    </div>
                    
                    <div class="w-full lg:w-auto flex flex-wrap sm:flex-nowrap items-center gap-2">
                        @php
                            $languages = ['php', 'javascript', 'typescript', 'java', 'python', 'sql', 'html', 'css', 'json', 'bash', 'mvel'];
                        @endphp
                        <select name="language" onchange="this.form.submit()" class="flex-1 sm:flex-none py-2 pl-3 pr-10 text-sm rounded-md border-gray-300 dark:border-gray-700 bg-white dark:bg-[#161925] text-gray-900 dark:text-gray-100 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                            <option value="all">All Languages</option>
                            @foreach($languages as $lang)
                                <option value="{{ $lang }}" {{ request('language') === $lang ? 'selected' : '' }}>{{ strtoupper($lang) }}</option>
                            @endforeach
                        </select>
                        
                        <label class="flex items-center gap-2 cursor-pointer border border-gray-300 dark:border-gray-700 px-3 py-2 rounded-md bg-white dark:bg-[#161925]">
                            <input type="checkbox" name="favorite" value="1" onchange="this.form.submit()" {{ request('favorite') ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Favorites</span>
                        </label>

                        <noscript>
                            <x-button type="submit" variant="secondary" size="sm">Filter</x-button>
                        </noscript>
                    </div>
                </form>
            </div>

            @if($snippets->isEmpty())
                <x-empty-state
                    title="No snippets found"
                    description="{{ request('search') || request('language') || request('favorite') ? 'Try adjusting your filters.' : 'Save code you find yourself typing over and over.' }}"
                >
                    @if(request('search') || request('language') || request('favorite'))
                        <a href="{{ route('snippets.index') }}" class="text-sm text-indigo-600 dark:text-indigo-400 hover:underline">Clear filters</a>
                    @else
                        <a href="{{ route('snippets.create') }}">
                            <x-button variant="primary" size="sm">Create your first snippet</x-button>
                        </a>
                    @endif
                </x-empty-state>
            @else
                <x-table :headers="['Title', 'Language', 'Purpose', 'Updated', 'Favorite']">
                    @foreach($snippets as $snippet)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors group">
                            <td class="px-5 py-3 whitespace-nowrap">
                                <a href="{{ route('snippets.show', $snippet) }}" class="font-medium text-gray-900 dark:text-gray-100 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors truncate max-w-[200px] sm:max-w-xs block">
                                    {{ $snippet->title }}
                                </a>
                            </td>
                            <td class="px-5 py-3 whitespace-nowrap">
                                <span class="bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400 text-xs font-mono px-2 py-0.5 rounded border border-gray-200 dark:border-gray-700">{{ strtoupper($snippet->language) }}</span>
                            </td>
                            <td class="px-5 py-3 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400 truncate max-w-[200px]">
                                {{ $snippet->purpose ?? '&mdash;' }}
                            </td>
                            <td class="px-5 py-3 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                {{ $snippet->updated_at->diffForHumans() }}
                            </td>
                            <td class="px-5 py-3 whitespace-nowrap">
                                <form method="POST" action="{{ route('snippets.favorite', $snippet) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="{{ $snippet->is_favorite ? 'text-yellow-400 hover:text-yellow-500' : 'text-gray-300 dark:text-gray-600 hover:text-yellow-400' }} transition-colors" title="Toggle Favorite">
                                        <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/></svg>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </x-table>
                <div class="p-4 border-t border-gray-200 dark:border-gray-800">
                    {{ $snippets->links('pagination::tailwind') }}
                </div>
            @endif
        </x-card>
    </div>
</x-app-layout>

