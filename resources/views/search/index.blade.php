<x-app-layout>
    <div class="space-y-6">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100 tracking-tight">Search Results</h1>
            @if(filled($query))
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    {{ $totalCount }} {{ Str::plural('result', $totalCount) }} for "<span class="font-medium text-gray-900 dark:text-gray-100">{{ $query }}</span>"
                </p>
            @endif
        </div>

        {{-- Search bar --}}
        <form method="GET" action="{{ route('search') }}" class="max-w-2xl">
            <div class="relative">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input type="text" name="q" value="{{ $query }}" autofocus placeholder="Search everything..." class="block w-full pl-11 pr-4 py-3 text-sm rounded-lg border-gray-300 dark:border-gray-700 bg-white dark:bg-[#161925] text-gray-900 dark:text-gray-100 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
            </div>
        </form>

        @if(blank($query))
            <x-empty-state
                title="Search everything"
                description="Search across your projects, problems, solutions, snippets, and knowledge base."
            />
        @elseif($totalCount === 0)
            <x-empty-state
                title="No results found"
                description="Try different keywords or check your spelling."
            />
        @else
            <div class="space-y-8">
                @php
                    $sections = [
                        'problems' => ['label' => 'Problems', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>'],
                        'solutions' => ['label' => 'Solutions', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>'],
                        'projects' => ['label' => 'Projects', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/>'],
                        'snippets' => ['label' => 'Snippets', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/>'],
                        'knowledge' => ['label' => 'Knowledge', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>'],
                    ];
                @endphp

                @foreach($sections as $key => $section)
                    @if(isset($results[$key]) && $results[$key]->isNotEmpty())
                        <section>
                            <div class="flex items-center gap-2 mb-3">
                                <svg class="w-5 h-5 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">{!! $section['icon'] !!}</svg>
                                <h2 class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-widest">{{ $section['label'] }}</h2>
                                <span class="text-xs text-gray-400 dark:text-gray-500">({{ $results[$key]->count() }})</span>
                            </div>

                            <div class="space-y-2">
                                @foreach($results[$key] as $result)
                                    @php
                                        $url = match($key) {
                                            'projects' => route('projects.show', $result),
                                            'problems' => route('problems.show', $result),
                                            'solutions' => $result->problem_id ? route('problems.show', $result->problem_id) : '#',
                                            'snippets' => route('snippets.show', $result),
                                            'knowledge' => route('knowledge.show', $result),
                                        };
                                    @endphp
                                    <a href="{{ $url }}" class="block bg-white dark:bg-[#161925] rounded-lg border border-gray-200 dark:border-gray-800 p-4 hover:border-indigo-300 dark:hover:border-indigo-500/50 transition-colors group">
                                        <div class="flex items-start justify-between gap-4">
                                            <div class="min-w-0">
                                                <h3 class="font-medium text-gray-900 dark:text-gray-100 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 truncate transition-colors">
                                                    {{ $result->title ?? $result->name ?? 'Untitled' }}
                                                </h3>
                                                <div class="flex items-center gap-3 mt-1 text-xs text-gray-500 dark:text-gray-400">
                                                    @if($key === 'problems')
                                                        @if($result->project)
                                                            <span>{{ $result->project->name }}</span>
                                                            <span class="text-gray-300 dark:text-gray-700">&middot;</span>
                                                        @endif
                                                        <x-badge :variant="$result->status === 'solved' ? 'success' : 'warning'" class="!text-[10px]">{{ ucfirst($result->status) }}</x-badge>
                                                    @elseif($key === 'solutions')
                                                        @if($result->problem)
                                                            <span class="truncate max-w-xs">→ {{ $result->problem->title }}</span>
                                                        @endif
                                                        <span class="font-mono">{{ ucfirst($result->solution_type) }}</span>
                                                    @elseif($key === 'projects')
                                                        <x-badge :variant="$result->status === 'active' ? 'success' : 'default'" class="!text-[10px]">{{ ucfirst($result->status) }}</x-badge>
                                                    @elseif($key === 'snippets')
                                                        <span class="font-mono bg-gray-100 dark:bg-gray-800 px-1.5 py-0.5 rounded">{{ strtoupper($result->language) }}</span>
                                                        @if($result->purpose)
                                                            <span class="truncate max-w-xs">{{ $result->purpose }}</span>
                                                        @endif
                                                    @elseif($key === 'knowledge')
                                                        <span class="font-mono bg-gray-100 dark:bg-gray-800 px-1.5 py-0.5 rounded">{{ ucfirst($result->type) }}</span>
                                                    @endif
                                                </div>
                                            </div>
                                            <svg class="w-4 h-4 text-gray-400 dark:text-gray-600 group-hover:text-indigo-500 dark:group-hover:text-indigo-400 shrink-0 mt-1 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        </section>
                    @endif
                @endforeach
            </div>
        @endif
    </div>
</x-app-layout>

