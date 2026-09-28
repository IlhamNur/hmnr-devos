<x-app-layout>
    <div class="space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100 tracking-tight">Problems</h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Track and solve programming problems. Don't solve the same problem twice.</p>
            </div>
            <div>
                <a href="{{ route('problems.create') }}">
                    <x-button variant="primary">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        New Problem
                    </x-button>
                </a>
            </div>
        </div>

        <x-card :padding="false">
            <div class="p-4 border-b border-gray-200 dark:border-gray-800 bg-gray-50/50 dark:bg-[#13151f]">
                <form method="GET" action="{{ route('problems.index') }}" class="flex flex-col lg:flex-row items-center gap-4">
                    <div class="w-full lg:flex-1">
                        <div class="relative">
                            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search problems or errors..." class="w-full pl-10 pr-4 py-2 text-sm rounded-md border-gray-300 dark:border-gray-700 bg-white dark:bg-[#161925] text-gray-900 dark:text-gray-100 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                        </div>
                    </div>
                    
                    <div class="w-full lg:w-auto flex flex-wrap sm:flex-nowrap items-center gap-2">
                        <select name="status" onchange="this.form.submit()" class="flex-1 sm:flex-none py-2 pl-3 pr-10 text-sm rounded-md border-gray-300 dark:border-gray-700 bg-white dark:bg-[#161925] text-gray-900 dark:text-gray-100 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                            <option value="all" {{ request('status') === 'all' ? 'selected' : '' }}>All Status</option>
                            <option value="open" {{ request('status') === 'open' ? 'selected' : '' }}>Open</option>
                            <option value="investigating" {{ request('status') === 'investigating' ? 'selected' : '' }}>Investigating</option>
                            <option value="solved" {{ request('status') === 'solved' ? 'selected' : '' }}>Solved</option>
                            <option value="archived" {{ request('status') === 'archived' ? 'selected' : '' }}>Archived</option>
                        </select>
                        
                        <select name="severity" onchange="this.form.submit()" class="flex-1 sm:flex-none py-2 pl-3 pr-10 text-sm rounded-md border-gray-300 dark:border-gray-700 bg-white dark:bg-[#161925] text-gray-900 dark:text-gray-100 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                            <option value="all" {{ request('severity') === 'all' ? 'selected' : '' }}>All Severity</option>
                            <option value="low" {{ request('severity') === 'low' ? 'selected' : '' }}>Low</option>
                            <option value="medium" {{ request('severity') === 'medium' ? 'selected' : '' }}>Medium</option>
                            <option value="high" {{ request('severity') === 'high' ? 'selected' : '' }}>High</option>
                            <option value="critical" {{ request('severity') === 'critical' ? 'selected' : '' }}>Critical</option>
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

            @if($problems->isEmpty())
                <x-empty-state
                    title="No problems found"
                    description="{{ request('search') || request('status') || request('severity') || request('project_id') ? 'Try adjusting your filters.' : 'Every bug you solve can become future knowledge.' }}"
                >
                    @if(request('search') || request('status') || request('severity') || request('project_id'))
                        <a href="{{ route('problems.index') }}" class="text-sm text-indigo-600 dark:text-indigo-400 hover:underline">Clear filters</a>
                    @else
                        <a href="{{ route('problems.create') }}">
                            <x-button variant="primary" size="sm">Log your first problem</x-button>
                        </a>
                    @endif
                </x-empty-state>
            @else
                <x-table :headers="['Title', 'Project', 'Severity', 'Status', 'Updated']">
                    @foreach($problems as $problem)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors group">
                            <td class="px-5 py-3 whitespace-nowrap">
                                <a href="{{ route('problems.show', $problem) }}" class="flex flex-col group-hover:text-indigo-600 dark:group-hover:text-indigo-400">
                                    <span class="font-medium text-gray-900 dark:text-gray-100 transition-colors truncate max-w-xs sm:max-w-md">{{ $problem->title }}</span>
                                    <span class="text-xs font-mono text-gray-500 dark:text-gray-400 mt-0.5 truncate max-w-xs sm:max-w-md">{{ $problem->error_type ?? 'Issue' }}</span>
                                </a>
                            </td>
                            <td class="px-5 py-3 whitespace-nowrap">
                                @if($problem->project)
                                    <a href="{{ route('projects.show', $problem->project) }}" class="text-sm text-gray-600 dark:text-gray-300 hover:underline">{{ $problem->project->name }}</a>
                                @else
                                    <span class="text-sm text-gray-400 dark:text-gray-500">&mdash;</span>
                                @endif
                            </td>
                            <td class="px-5 py-3 whitespace-nowrap">
                                @php
                                    $severityColors = [
                                        'critical' => 'danger',
                                        'high' => 'warning',
                                        'medium' => 'primary',
                                        'low' => 'default',
                                    ];
                                @endphp
                                <x-badge :variant="$severityColors[$problem->severity] ?? 'default'">
                                    {{ ucfirst($problem->severity) }}
                                </x-badge>
                            </td>
                            <td class="px-5 py-3 whitespace-nowrap">
                                @php
                                    $statusColors = [
                                        'open' => 'danger',
                                        'investigating' => 'warning',
                                        'solved' => 'success',
                                        'archived' => 'default',
                                    ];
                                @endphp
                                <x-badge :variant="$statusColors[$problem->status] ?? 'default'">
                                    {{ ucfirst($problem->status) }}
                                </x-badge>
                            </td>
                            <td class="px-5 py-3 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                {{ $problem->updated_at->diffForHumans() }}
                            </td>
                        </tr>
                    @endforeach
                </x-table>
                <div class="p-4 border-t border-gray-200 dark:border-gray-800">
                    {{ $problems->links('pagination::tailwind') }}
                </div>
            @endif
        </x-card>
    </div>
</x-app-layout>

