<x-app-layout>
    <div class="space-y-8">
        {{-- Header --}}
        <div>
            <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100 tracking-tight">Workspace Overview</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Welcome back, {{ auth()->user()->name }}. What are you working on today?</p>
        </div>

        {{-- Stats Grid --}}
        <div class="grid grid-cols-2 md:grid-cols-5 gap-4 lg:gap-6">
            <x-card :padding="false" class="flex flex-col">
                <div class="p-4 flex flex-col gap-2">
                    <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Active Projects</p>
                    <p class="text-2xl font-mono font-medium text-indigo-600 dark:text-indigo-400">{{ $stats['active_projects'] }}</p>
                </div>
            </x-card>

            <x-card :padding="false" class="flex flex-col">
                <div class="p-4 flex flex-col gap-2">
                    <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Open Problems</p>
                    <p class="text-2xl font-mono font-medium text-red-600 dark:text-red-400">{{ $stats['open_problems'] }}</p>
                </div>
            </x-card>

            <x-card :padding="false" class="flex flex-col">
                <div class="p-4 flex flex-col gap-2">
                    <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Solved Problems</p>
                    <p class="text-2xl font-mono font-medium text-green-600 dark:text-green-400">{{ $stats['solved_problems'] }}</p>
                </div>
            </x-card>

            <x-card :padding="false" class="flex flex-col">
                <div class="p-4 flex flex-col gap-2">
                    <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Snippets</p>
                    <p class="text-2xl font-mono font-medium text-gray-900 dark:text-gray-100">{{ $stats['snippets'] }}</p>
                </div>
            </x-card>

            <x-card :padding="false" class="flex flex-col">
                <div class="p-4 flex flex-col gap-2">
                    <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Knowledge</p>
                    <p class="text-2xl font-mono font-medium text-gray-900 dark:text-gray-100">{{ $stats['knowledge_items'] }}</p>
                </div>
            </x-card>
        </div>

        {{-- Main split content --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Left column --}}
            <div class="lg:col-span-2 space-y-6">
                
                {{-- Active Projects --}}
                @if($activeProjects->isNotEmpty())
                    <x-card title="Active Projects" :padding="false">
                        <div class="divide-y divide-gray-200 dark:divide-gray-800">
                            @foreach($activeProjects as $project)
                                <a href="{{ route('projects.show', $project) }}" class="block p-4 hover:bg-gray-50 dark:hover:bg-[#13151f] transition-colors group">
                                    <div class="flex items-center justify-between">
                                        <div class="min-w-0">
                                            <p class="text-sm font-medium text-gray-900 dark:text-gray-100 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 truncate">{{ $project->name }}</p>
                                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                                {{ $project->problems_count }} Problems &middot; {{ $project->solved_problems_count }} Solved &middot; Updated {{ $project->updated_at->diffForHumans() }}
                                            </p>
                                        </div>
                                        <x-badge variant="success" class="ml-2">Active</x-badge>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </x-card>
                @endif

                {{-- Recent Problems --}}
                <x-card title="Recent Problems" :padding="false">
                    @if($recentProblems->isEmpty())
                        <div class="p-8 text-center text-sm text-gray-500 dark:text-gray-400">
                            No problems recorded yet.
                        </div>
                    @else
                        <div class="divide-y divide-gray-200 dark:divide-gray-800">
                            @foreach($recentProblems as $problem)
                                <a href="{{ route('problems.show', $problem) }}" class="block p-4 hover:bg-gray-50 dark:hover:bg-[#13151f] transition-colors group">
                                    <div class="flex items-center justify-between">
                                        <div class="min-w-0 flex-1 pr-4">
                                            <p class="text-sm font-medium text-gray-900 dark:text-gray-100 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 truncate">{{ $problem->title }}</p>
                                            <div class="flex items-center gap-2 mt-1">
                                                @php
                                                    $severityColors = [
                                                        'critical' => 'text-red-700 bg-red-100 dark:bg-red-900/50 dark:text-red-300',
                                                        'high' => 'text-orange-700 bg-orange-100 dark:bg-orange-900/50 dark:text-orange-300',
                                                        'medium' => 'text-yellow-700 bg-yellow-100 dark:bg-yellow-900/50 dark:text-yellow-300',
                                                        'low' => 'text-gray-700 bg-gray-100 dark:bg-gray-800 dark:text-gray-300',
                                                    ];
                                                @endphp
                                                <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-medium {{ $severityColors[$problem->severity] }}">{{ ucfirst($problem->severity) }}</span>
                                                <span class="text-xs text-gray-500 dark:text-gray-400 truncate">{{ $problem->project?->name ?? 'No Project' }}</span>
                                            </div>
                                        </div>
                                        <x-badge :variant="$problem->status === 'solved' ? 'success' : ($problem->status === 'open' ? 'danger' : 'warning')" class="ml-2 !text-[10px]">
                                            {{ ucfirst($problem->status) }}
                                        </x-badge>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    @endif
                </x-card>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Recent Snippets --}}
                    <x-card title="Recent Snippets" :padding="false">
                        @if($recentSnippets->isEmpty())
                            <div class="p-6 text-center text-sm text-gray-500 dark:text-gray-400">No snippets yet.</div>
                        @else
                            <div class="divide-y divide-gray-200 dark:divide-gray-800">
                                @foreach($recentSnippets as $snippet)
                                    <a href="{{ route('snippets.show', $snippet) }}" class="block p-3 hover:bg-gray-50 dark:hover:bg-[#13151f] transition-colors group">
                                        <p class="text-sm font-medium text-gray-900 dark:text-gray-100 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 truncate">{{ $snippet->title }}</p>
                                        <p class="mt-1 text-xs font-mono text-gray-500 dark:text-gray-400">{{ strtoupper($snippet->language) }}</p>
                                    </a>
                                @endforeach
                            </div>
                        @endif
                    </x-card>

                    {{-- Recent Knowledge --}}
                    <x-card title="Recent Knowledge" :padding="false">
                        @if($recentKnowledge->isEmpty())
                            <div class="p-6 text-center text-sm text-gray-500 dark:text-gray-400">No knowledge yet.</div>
                        @else
                            <div class="divide-y divide-gray-200 dark:divide-gray-800">
                                @foreach($recentKnowledge as $knowledge)
                                    <a href="{{ route('knowledge.show', $knowledge) }}" class="block p-3 hover:bg-gray-50 dark:hover:bg-[#13151f] transition-colors group">
                                        <p class="text-sm font-medium text-gray-900 dark:text-gray-100 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 truncate">{{ $knowledge->title }}</p>
                                        <p class="mt-1 text-xs font-mono text-gray-500 dark:text-gray-400">{{ ucfirst($knowledge->type) }}</p>
                                    </a>
                                @endforeach
                            </div>
                        @endif
                    </x-card>
                </div>
            </div>
            
            {{-- Right column --}}
            <div class="space-y-6">
                {{-- Quick Actions --}}
                <x-card title="Quick Actions" :padding="false">
                    <div class="p-2 flex flex-col gap-1 text-sm">
                        <a href="{{ route('problems.create') }}" class="flex items-center gap-3 p-2 rounded-md hover:bg-gray-100 dark:hover:bg-[#13151f] transition-colors group">
                            <div class="text-red-500 dark:text-red-400 group-hover:text-red-600 dark:group-hover:text-red-300 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                            </div>
                            <span class="font-medium text-gray-700 dark:text-gray-300">New Problem</span>
                        </a>
                        <a href="{{ route('snippets.create') }}" class="flex items-center gap-3 p-2 rounded-md hover:bg-gray-100 dark:hover:bg-[#13151f] transition-colors group">
                            <div class="text-yellow-500 dark:text-yellow-400 group-hover:text-yellow-600 dark:group-hover:text-yellow-300 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/></svg>
                            </div>
                            <span class="font-medium text-gray-700 dark:text-gray-300">New Snippet</span>
                        </a>
                        <a href="{{ route('knowledge.create') }}" class="flex items-center gap-3 p-2 rounded-md hover:bg-gray-100 dark:hover:bg-[#13151f] transition-colors group">
                            <div class="text-green-500 dark:text-green-400 group-hover:text-green-600 dark:group-hover:text-green-300 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                            </div>
                            <span class="font-medium text-gray-700 dark:text-gray-300">New Knowledge</span>
                        </a>
                        <a href="{{ route('projects.create') }}" class="flex items-center gap-3 p-2 rounded-md hover:bg-gray-100 dark:hover:bg-[#13151f] transition-colors group">
                            <div class="text-indigo-500 dark:text-indigo-400 group-hover:text-indigo-600 dark:group-hover:text-indigo-300 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/></svg>
                            </div>
                            <span class="font-medium text-gray-700 dark:text-gray-300">New Project</span>
                        </a>
                    </div>
                </x-card>

                {{-- Developer Activity Feed --}}
                <x-card title="Activity" :padding="false">
                    @if($activities->isEmpty())
                        <div class="p-6 text-center text-sm text-gray-500 dark:text-gray-400">
                            No recent activity.
                        </div>
                    @else
                        <div class="p-4 space-y-4">
                            @foreach($activities as $activity)
                                <div class="flex items-start gap-3">
                                    <div class="shrink-0 mt-1">
                                        @if($activity->type === 'solved_problem')
                                            <div class="w-2 h-2 rounded-full bg-green-500"></div>
                                        @elseif(str_starts_with($activity->type, 'created_'))
                                            <div class="w-2 h-2 rounded-full bg-indigo-500"></div>
                                        @elseif(str_starts_with($activity->type, 'updated_'))
                                            <div class="w-2 h-2 rounded-full bg-gray-400"></div>
                                        @else
                                            <div class="w-2 h-2 rounded-full bg-gray-300"></div>
                                        @endif
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-sm text-gray-900 dark:text-gray-100">
                                            {{ $activity->description }}
                                        </p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                                            {{ $activity->created_at->diffForHumans() }}
                                        </p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </x-card>
            </div>
        </div>
    </div>
</x-app-layout>
