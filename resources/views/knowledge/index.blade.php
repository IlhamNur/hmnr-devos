<x-app-layout>
    <div class="space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100 tracking-tight">Knowledge Base</h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Document architectures, notes, concepts, and references.</p>
            </div>
            <div>
                <a href="{{ route('knowledge.create') }}">
                    <x-button variant="primary">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        New Entry
                    </x-button>
                </a>
            </div>
        </div>

        <x-card :padding="false">
            <div class="p-4 border-b border-gray-200 dark:border-gray-800 bg-gray-50/50 dark:bg-[#13151f]">
                <form method="GET" action="{{ route('knowledge.index') }}" class="flex flex-col lg:flex-row items-center gap-4">
                    <div class="w-full lg:flex-1">
                        <div class="relative">
                            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search knowledge base..." class="w-full pl-10 pr-4 py-2 text-sm rounded-md border-gray-300 dark:border-gray-700 bg-white dark:bg-[#161925] text-gray-900 dark:text-gray-100 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                        </div>
                    </div>
                    
                    <div class="w-full lg:w-auto flex flex-wrap sm:flex-nowrap items-center gap-2">
                        @php
                            $types = ['note' => 'Note', 'documentation' => 'Documentation', 'reference' => 'Reference', 'concept' => 'Concept', 'tutorial' => 'Tutorial'];
                        @endphp
                        <select name="type" onchange="this.form.submit()" class="flex-1 sm:flex-none py-2 pl-3 pr-10 text-sm rounded-md border-gray-300 dark:border-gray-700 bg-white dark:bg-[#161925] text-gray-900 dark:text-gray-100 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                            <option value="all">All Types</option>
                            @foreach($types as $val => $label)
                                <option value="{{ $val }}" {{ request('type') === $val ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        
                        <select name="project_id" onchange="this.form.submit()" class="flex-1 sm:flex-none py-2 pl-3 pr-10 text-sm rounded-md border-gray-300 dark:border-gray-700 bg-white dark:bg-[#161925] text-gray-900 dark:text-gray-100 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                            <option value="">All Projects</option>
                            @foreach($projects as $project)
                                <option value="{{ $project->id }}" {{ request('project_id') == $project->id ? 'selected' : '' }}>{{ $project->name }}</option>
                            @endforeach
                        </select>

                        <noscript>
                            <x-button type="submit" variant="secondary" size="sm">Filter</x-button>
                        </noscript>
                    </div>
                </form>
            </div>

            @if($knowledgeItems->isEmpty())
                <x-empty-state
                    title="No knowledge entries found"
                    description="{{ request('search') || request('type') || request('project_id') ? 'Try adjusting your filters.' : 'Document your ideas, architectural decisions, and learnings.' }}"
                >
                    @if(request('search') || request('type') || request('project_id'))
                        <a href="{{ route('knowledge.index') }}" class="text-sm text-indigo-600 dark:text-indigo-400 hover:underline">Clear filters</a>
                    @else
                        <a href="{{ route('knowledge.create') }}">
                            <x-button variant="primary" size="sm">Create your first entry</x-button>
                        </a>
                    @endif
                </x-empty-state>
            @else
                <x-table :headers="['Title', 'Project', 'Type', 'Updated']">
                    @foreach($knowledgeItems as $item)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors group">
                            <td class="px-5 py-4 whitespace-nowrap">
                                <a href="{{ route('knowledge.show', $item) }}" class="font-medium text-gray-900 dark:text-gray-100 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors truncate max-w-[250px] sm:max-w-md block">
                                    {{ $item->title }}
                                </a>
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap">
                                @if($item->project)
                                    <a href="{{ route('projects.show', $item->project) }}" class="text-sm text-gray-600 dark:text-gray-300 hover:underline">{{ $item->project->name }}</a>
                                @else
                                    <span class="text-sm text-gray-400 dark:text-gray-500">&mdash;</span>
                                @endif
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap">
                                <span class="bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400 text-xs font-mono px-2 py-0.5 rounded border border-gray-200 dark:border-gray-700">{{ ucfirst($item->type) }}</span>
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                {{ $item->updated_at->diffForHumans() }}
                            </td>
                        </tr>
                    @endforeach
                </x-table>
                <div class="p-4 border-t border-gray-200 dark:border-gray-800">
                    {{ $knowledgeItems->links('pagination::tailwind') }}
                </div>
            @endif
        </x-card>
    </div>
</x-app-layout>

