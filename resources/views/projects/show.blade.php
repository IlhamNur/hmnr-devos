<x-app-layout>
    <div class="space-y-6" x-data="{ tab: 'overview' }">
        {{-- Workspace Header --}}
        <div>
            <div class="flex items-center gap-3 text-sm text-gray-500 dark:text-gray-400 mb-2">
                <a href="{{ route('projects.index') }}" class="hover:text-gray-900 dark:hover:text-gray-200 transition-colors">Projects</a>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                <span class="font-medium text-gray-900 dark:text-gray-100">{{ $project->name }}</span>
            </div>

            <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
                <div>
                    <div class="flex items-center gap-3">
                        <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100 tracking-tight">{{ $project->name }}</h1>
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
                    </div>
                    
                    @if($project->tags->isNotEmpty())
                        <div class="flex items-center flex-wrap gap-2 mt-3 font-mono text-xs text-gray-500 dark:text-gray-400">
                            @foreach($project->tags as $tag)
                                <span>{{ $tag->name }}</span>
                                @if(!$loop->last) <span class="text-gray-300 dark:text-gray-700">&middot;</span> @endif
                            @endforeach
                        </div>
                    @endif
                </div>

                <div class="flex items-center gap-2">
                    <a href="{{ route('projects.edit', $project) }}">
                        <x-button variant="secondary" size="sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            Settings
                        </x-button>
                    </a>
                </div>
            </div>
        </div>

        {{-- Statistics --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="bg-white dark:bg-[#161925] rounded-md border border-gray-200 dark:border-gray-800 p-4">
                <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Problems</p>
                <p class="mt-1 text-2xl font-mono font-semibold text-gray-900 dark:text-gray-100">{{ $project->problems_count }}</p>
            </div>
            <div class="bg-white dark:bg-[#161925] rounded-md border border-gray-200 dark:border-gray-800 p-4">
                <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Solved</p>
                <p class="mt-1 text-2xl font-mono font-semibold text-gray-900 dark:text-gray-100">{{ $project->solved_problems_count }}</p>
            </div>
            <div class="bg-white dark:bg-[#161925] rounded-md border border-gray-200 dark:border-gray-800 p-4">
                <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Snippets</p>
                <p class="mt-1 text-2xl font-mono font-semibold text-gray-900 dark:text-gray-100">{{ $project->snippets_count }}</p>
            </div>
            <div class="bg-white dark:bg-[#161925] rounded-md border border-gray-200 dark:border-gray-800 p-4">
                <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Knowledge</p>
                <p class="mt-1 text-2xl font-mono font-semibold text-gray-900 dark:text-gray-100">{{ $project->knowledge_items_count }}</p>
            </div>
        </div>

        {{-- Tabs --}}
        <div class="border-b border-gray-200 dark:border-gray-800">
            <nav class="-mb-px flex space-x-6">
                <button @click="tab = 'overview'" :class="tab === 'overview' ? 'border-indigo-500 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 hover:border-gray-300 dark:hover:text-gray-300 dark:hover:border-gray-700'" class="whitespace-nowrap pb-4 px-1 border-b-2 font-medium text-sm transition-colors">Overview</button>
                <button @click="tab = 'problems'" :class="tab === 'problems' ? 'border-indigo-500 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 hover:border-gray-300 dark:hover:text-gray-300 dark:hover:border-gray-700'" class="whitespace-nowrap pb-4 px-1 border-b-2 font-medium text-sm transition-colors">Problems</button>
                <button @click="tab = 'snippets'" :class="tab === 'snippets' ? 'border-indigo-500 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 hover:border-gray-300 dark:hover:text-gray-300 dark:hover:border-gray-700'" class="whitespace-nowrap pb-4 px-1 border-b-2 font-medium text-sm transition-colors">Snippets</button>
                <button @click="tab = 'knowledge'" :class="tab === 'knowledge' ? 'border-indigo-500 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 hover:border-gray-300 dark:hover:text-gray-300 dark:hover:border-gray-700'" class="whitespace-nowrap pb-4 px-1 border-b-2 font-medium text-sm transition-colors">Knowledge</button>
            </nav>
        </div>

        {{-- Tab Content --}}
        <div class="py-2">
            
            {{-- Overview Tab --}}
            <div x-show="tab === 'overview'" x-cloak class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="md:col-span-2 space-y-6">
                        @if($project->description)
                            <x-card title="Description">
                                <div class="prose dark:prose-invert prose-sm max-w-none">
                                    {{ $project->description }}
                                </div>
                            </x-card>
                        @endif

                        <x-card title="Recent Problems">
                            <div class="p-4 flex flex-col gap-3">
                                @forelse($project->problems()->latest()->take(3)->get() as $problem)
                                    <div class="flex items-center justify-between p-3 border border-gray-100 dark:border-gray-800 rounded-md hover:bg-gray-50 dark:hover:bg-[#13151f] transition-colors">
                                        <div>
                                            <a href="#" class="text-sm font-medium text-indigo-600 dark:text-indigo-400 hover:underline">{{ $problem->title }}</a>
                                            <div class="text-xs text-gray-500 dark:text-gray-500 mt-1">Logged {{ $problem->created_at->diffForHumans() }}</div>
                                        </div>
                                        <x-badge :variant="$problem->status === 'solved' ? 'success' : 'warning'">{{ ucfirst($problem->status) }}</x-badge>
                                    </div>
                                @empty
                                    <p class="text-sm text-gray-500 dark:text-gray-400">No problems logged yet.</p>
                                @endforelse
                            </div>
                        </x-card>
                    </div>

                    <div class="space-y-6">
                        <x-card title="Details">
                            <div class="space-y-4 text-sm">
                                <div>
                                    <span class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Environment</span>
                                    <span class="text-gray-900 dark:text-gray-100">{{ $project->environment ?? 'Not specified' }}</span>
                                </div>
                                @if($project->repository_url)
                                <div>
                                    <span class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Repository</span>
                                    <a href="{{ $project->repository_url }}" target="_blank" class="text-indigo-600 dark:text-indigo-400 hover:underline flex items-center gap-1">
                                        {{ str_replace(['https://', 'http://'], '', $project->repository_url) }}
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                    </a>
                                </div>
                                @endif
                                <div>
                                    <span class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Timeline</span>
                                    <span class="text-gray-900 dark:text-gray-100">
                                        {{ $project->started_at ? $project->started_at->format('M Y') : 'Unknown' }}
                                        &rarr;
                                        {{ $project->ended_at ? $project->ended_at->format('M Y') : 'Present' }}
                                    </span>
                                </div>
                            </div>
                        </x-card>
                    </div>
                </div>
            </div>

            {{-- Problems Tab --}}
            <div x-show="tab === 'problems'" x-cloak>
                <div class="mb-4 flex justify-end">
                    <x-button variant="primary" size="sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        New Problem
                    </x-button>
                </div>
                <x-card :padding="false">
                    @if($project->problems->isEmpty())
                        <x-empty-state title="No problems recorded" description="Track bugs, errors, and challenges for this project here." />
                    @else
                        <x-table :headers="['Problem', 'Severity', 'Status', 'Logged']">
                            @foreach($project->problems as $problem)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50">
                                    <td class="px-5 py-3">
                                        <a href="#" class="font-medium text-gray-900 dark:text-gray-100 hover:text-indigo-600 dark:hover:text-indigo-400">{{ $problem->title }}</a>
                                        <div class="text-xs font-mono text-gray-500 dark:text-gray-500 mt-1">{{ $problem->error_type ?? 'Issue' }}</div>
                                    </td>
                                    <td class="px-5 py-3">
                                        <x-badge :variant="$problem->severity === 'critical' ? 'danger' : ($problem->severity === 'high' ? 'warning' : 'default')">
                                            {{ ucfirst($problem->severity) }}
                                        </x-badge>
                                    </td>
                                    <td class="px-5 py-3">
                                        <x-badge :variant="$problem->status === 'solved' ? 'success' : 'warning'">
                                            {{ ucfirst($problem->status) }}
                                        </x-badge>
                                    </td>
                                    <td class="px-5 py-3 text-sm text-gray-500 dark:text-gray-400">
                                        {{ $problem->created_at->format('M j, Y') }}
                                    </td>
                                </tr>
                            @endforeach
                        </x-table>
                    @endif
                </x-card>
            </div>

            {{-- Snippets Tab --}}
            <div x-show="tab === 'snippets'" x-cloak>
                <x-card>
                    <x-empty-state title="No snippets yet" description="Save reusable code for this project here." />
                </x-card>
            </div>

            {{-- Knowledge Tab --}}
            <div x-show="tab === 'knowledge'" x-cloak>
                <x-card>
                    <x-empty-state title="No knowledge entries" description="Document your architectural decisions here." />
                </x-card>
            </div>
            
        </div>
    </div>
</x-app-layout>

