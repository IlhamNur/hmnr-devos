<x-app-layout>
    <div class="space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100 tracking-tight">Projects</h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Manage your development projects and their environments.</p>
            </div>
            <div>
                <a href="{{ route('projects.create') }}">
                    <x-button variant="primary">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        New Project
                    </x-button>
                </a>
            </div>
        </div>

        <x-card :padding="false">
            <div class="p-4 border-b border-gray-200 dark:border-gray-800 bg-gray-50/50 dark:bg-[#13151f]">
                <form method="GET" action="{{ route('projects.index') }}" class="flex flex-col sm:flex-row items-center gap-4">
                    <div class="flex-1 w-full">
                        <div class="relative">
                            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search projects..." class="w-full pl-10 pr-4 py-2 text-sm rounded-md border-gray-300 dark:border-gray-700 bg-white dark:bg-[#161925] text-gray-900 dark:text-gray-100 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                        </div>
                    </div>
                    <div class="w-full sm:w-auto flex items-center gap-2">
                        <select name="status" onchange="this.form.submit()" class="w-full sm:w-auto py-2 pl-3 pr-10 text-sm rounded-md border-gray-300 dark:border-gray-700 bg-white dark:bg-[#161925] text-gray-900 dark:text-gray-100 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                            <option value="all" {{ request('status') === 'all' ? 'selected' : '' }}>All Status</option>
                            <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                            <option value="paused" {{ request('status') === 'paused' ? 'selected' : '' }}>Paused</option>
                            <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                            <option value="archived" {{ request('status') === 'archived' ? 'selected' : '' }}>Archived</option>
                        </select>
                        <noscript>
                            <x-button type="submit" variant="secondary" size="sm">Filter</x-button>
                        </noscript>
                    </div>
                </form>
            </div>

            @if($projects->isEmpty())
                <x-empty-state
                    title="No projects found"
                    description="{{ request('search') || request('status') ? 'Try adjusting your filters.' : 'Organize your workspace by tracking your first project.' }}"
                >
                    @if(request('search') || request('status'))
                        <a href="{{ route('projects.index') }}" class="text-sm text-indigo-600 dark:text-indigo-400 hover:underline">Clear filters</a>
                    @else
                        <a href="{{ route('projects.create') }}">
                            <x-button variant="primary" size="sm">Create your first project</x-button>
                        </a>
                    @endif
                </x-empty-state>
            @else
                <x-table :headers="['Name', 'Status', 'Problems', 'Last Updated', '']">
                    @foreach($projects as $project)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors group">
                            <td class="px-5 py-3 whitespace-nowrap">
                                <a href="{{ route('projects.show', $project) }}" class="flex items-center gap-3 group-hover:text-indigo-600 dark:group-hover:text-indigo-400">
                                    <div class="font-medium text-gray-900 dark:text-gray-100 transition-colors">{{ $project->name }}</div>
                                </a>
                                <div class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ $project->environment ?? 'No environment' }}</div>
                            </td>
                            <td class="px-5 py-3 whitespace-nowrap">
                                @php
                                    $statusColors = [
                                        'active' => 'success',
                                        'paused' => 'warning',
                                        'completed' => 'primary',
                                        'archived' => 'default',
                                    ];
                                @endphp
                                <x-badge :variant="$statusColors[$project->status] ?? 'default'">
                                    {{ ucfirst($project->status) }}
                                </x-badge>
                            </td>
                            <td class="px-5 py-3 whitespace-nowrap">
                                <span class="font-mono text-sm {{ $project->problems_count > 0 ? 'text-red-600 dark:text-red-400' : 'text-gray-500 dark:text-gray-400' }}">
                                    {{ $project->problems_count }}
                                </span>
                            </td>
                            <td class="px-5 py-3 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                {{ $project->updated_at->diffForHumans() }}
                            </td>
                            <td class="px-5 py-3 whitespace-nowrap text-right text-sm font-medium">
                                <a href="{{ route('projects.show', $project) }}" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300">View</a>
                            </td>
                        </tr>
                    @endforeach
                </x-table>
                <div class="p-4 border-t border-gray-200 dark:border-gray-800">
                    {{ $projects->links('pagination::tailwind') }}
                </div>
            @endif
        </x-card>
    </div>
</x-app-layout>

