<x-app-layout>
    <div class="max-w-4xl mx-auto space-y-8">
        {{-- Header Section --}}
        <div>
            <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4">
                <div class="flex-1 space-y-4">
                    <div class="flex items-center gap-3 text-sm text-gray-500 dark:text-gray-400">
                        <a href="{{ route('problems.index') }}" class="hover:text-gray-900 dark:hover:text-gray-200 transition-colors">Problems</a>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        <span class="font-medium text-gray-900 dark:text-gray-100 truncate max-w-[200px] sm:max-w-xs">{{ $problem->title }}</span>
                    </div>

                    <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100 tracking-tight leading-snug">{{ $problem->title }}</h1>

                    <div class="flex flex-wrap items-center gap-4">
                        @php
                            $statusColors = [
                                'open' => 'danger',
                                'investigating' => 'warning',
                                'solved' => 'success',
                                'archived' => 'default',
                            ];
                            $severityColors = [
                                'critical' => 'danger',
                                'high' => 'warning',
                                'medium' => 'primary',
                                'low' => 'default',
                            ];
                        @endphp
                        
                        <div class="flex items-center gap-2">
                            <span class="text-sm text-gray-500 dark:text-gray-400">Status:</span>
                            <x-badge :variant="$statusColors[$problem->status] ?? 'default'">{{ ucfirst($problem->status) }}</x-badge>
                        </div>
                        
                        <div class="flex items-center gap-2">
                            <span class="text-sm text-gray-500 dark:text-gray-400">Severity:</span>
                            <x-badge :variant="$severityColors[$problem->severity] ?? 'default'">{{ ucfirst($problem->severity) }}</x-badge>
                        </div>

                        @if($problem->project)
                            <div class="flex items-center gap-2">
                                <span class="text-sm text-gray-500 dark:text-gray-400">Project:</span>
                                <a href="{{ route('projects.show', $problem->project) }}" class="text-sm font-medium text-indigo-600 dark:text-indigo-400 hover:underline">
                                    {{ $problem->project->name }}
                                </a>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="flex sm:flex-col items-center sm:items-end gap-2 shrink-0">
                    <a href="{{ route('problems.edit', $problem) }}" class="w-full">
                        <x-button variant="secondary" size="sm" class="w-full justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                            Edit
                        </x-button>
                    </a>
                    
                    {{-- Signature Feature --}}
                    @if($problem->status !== 'solved')
                        <a href="{{ route('search', ['q' => $problem->title]) }}" class="w-full">
                            <x-button variant="primary" size="sm" class="w-full justify-center">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                How did I solve this?
                            </x-button>
                        </a>
                    @endif
                </div>
            </div>

            @if($problem->tags->isNotEmpty())
                <div class="flex items-center flex-wrap gap-2 mt-6 font-mono text-xs text-gray-500 dark:text-gray-400">
                    @foreach($problem->tags as $tag)
                        <span class="bg-gray-100 dark:bg-[#13151f] px-2 py-1 rounded border border-gray-200 dark:border-gray-800">{{ $tag->name }}</span>
                    @endforeach
                </div>
            @endif
        </div>

        <div class="border-t border-gray-200 dark:border-gray-800"></div>

        {{-- PROBLEM --}}
        <section class="space-y-4">
            <h3 class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-widest">Problem</h3>
            <div class="prose dark:prose-invert max-w-none text-gray-800 dark:text-gray-200 text-sm leading-relaxed whitespace-pre-wrap">{{ $problem->description }}</div>
        </section>

        @if($problem->error_message || $problem->error_type)
            <div class="border-t border-gray-200 dark:border-gray-800"></div>
            
            {{-- ERROR --}}
            <section class="space-y-4">
                <div class="flex items-center gap-3">
                    <h3 class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-widest">Error</h3>
                    @if($problem->error_type)
                        <span class="font-mono text-xs text-red-600 dark:text-red-400 bg-red-50 dark:bg-red-500/10 px-2 py-0.5 rounded border border-red-100 dark:border-red-500/20">{{ $problem->error_type }}</span>
                    @endif
                </div>
                
                @if($problem->error_message)
                    <div class="bg-gray-900 dark:bg-[#0a0c10] rounded-lg p-4 overflow-x-auto border border-gray-800">
                        <pre class="font-mono text-sm text-gray-300 leading-relaxed">{{ $problem->error_message }}</pre>
                    </div>
                @endif
            </section>
        @endif

        @if($problem->environment || $problem->occurred_at)
            <div class="border-t border-gray-200 dark:border-gray-800"></div>
            
            {{-- ENVIRONMENT --}}
            <section class="space-y-4">
                <h3 class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-widest">Environment & Timeline</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    @if($problem->environment)
                        <div class="bg-gray-50 dark:bg-[#13151f] rounded-lg p-4 border border-gray-200 dark:border-gray-800">
                            <span class="block text-xs text-gray-500 dark:text-gray-400 mb-2">Context</span>
                            <div class="font-mono text-sm text-gray-700 dark:text-gray-300">{{ $problem->environment }}</div>
                        </div>
                    @endif
                    
                    <div class="bg-gray-50 dark:bg-[#13151f] rounded-lg p-4 border border-gray-200 dark:border-gray-800">
                        <span class="block text-xs text-gray-500 dark:text-gray-400 mb-2">Timeline</span>
                        <div class="space-y-2 text-sm text-gray-700 dark:text-gray-300">
                            <div class="flex items-center justify-between">
                                <span>Occurred:</span>
                                <span class="font-mono text-xs">{{ $problem->occurred_at ? $problem->occurred_at->format('Y-m-d H:i') : 'Unknown' }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span>Solved:</span>
                                <span class="font-mono text-xs {{ $problem->solved_at ? 'text-green-600 dark:text-green-400' : 'text-gray-400' }}">{{ $problem->solved_at ? $problem->solved_at->format('Y-m-d H:i') : 'Pending' }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        @endif

        <div class="border-t border-gray-200 dark:border-gray-800"></div>

        {{-- SOLUTIONS --}}
        <section class="space-y-6">
            <div class="flex items-center justify-between">
                <h3 class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-widest">Solutions</h3>
                <x-button variant="secondary" size="sm" x-data @click="$dispatch('open-modal', 'add-solution')">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Add Solution
                </x-button>
            </div>

            <div class="space-y-4">
                @forelse($problem->solutions as $solution)
                    <div class="bg-white dark:bg-[#161925] rounded-lg border {{ $solution->is_verified ? 'border-green-300 dark:border-green-500/50 shadow-sm shadow-green-500/10' : 'border-gray-200 dark:border-gray-800' }} overflow-hidden relative group">
                        
                        @if($solution->is_verified)
                            <div class="absolute top-0 right-0 bg-green-100 dark:bg-green-500/20 text-green-700 dark:text-green-400 text-xs font-semibold px-3 py-1 rounded-bl-lg border-b border-l border-green-200 dark:border-green-500/30 flex items-center gap-1">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                Verified
                            </div>
                        @endif

                        <div class="p-5">
                            <div class="flex items-center gap-3 mb-4 pr-24">
                                <span class="bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400 text-xs font-mono px-2 py-0.5 rounded">{{ ucfirst($solution->solution_type) }}</span>
                                <h4 class="font-semibold text-gray-900 dark:text-gray-100">{{ $solution->title }}</h4>
                            </div>

                            <div class="prose dark:prose-invert max-w-none text-sm text-gray-800 dark:text-gray-200 whitespace-pre-wrap {{ $solution->solution_type === 'code' || $solution->solution_type === 'configuration' ? 'font-mono bg-gray-50 dark:bg-[#0a0c10] p-4 rounded-md border border-gray-100 dark:border-gray-800' : '' }}">{{ $solution->content }}</div>
                        </div>
                        
                        <div class="bg-gray-50 dark:bg-[#13151f] px-5 py-3 border-t border-gray-100 dark:border-gray-800 flex items-center justify-between opacity-0 group-hover:opacity-100 transition-opacity">
                            <div class="text-xs text-gray-500 dark:text-gray-400">
                                Added {{ $solution->created_at->diffForHumans() }}
                            </div>
                            <div class="flex items-center gap-2">
                                <form method="POST" action="{{ route('solutions.verify', [$problem, $solution]) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="text-xs font-medium {{ $solution->is_verified ? 'text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200' : 'text-green-600 hover:text-green-700 dark:text-green-400 dark:hover:text-green-300' }} transition-colors">
                                        {{ $solution->is_verified ? 'Unverify' : 'Mark as Verified' }}
                                    </button>
                                </form>
                                <span class="text-gray-300 dark:text-gray-700">&middot;</span>
                                <form method="POST" action="{{ route('solutions.destroy', [$problem, $solution]) }}" onsubmit="return confirm('Delete this solution?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-xs font-medium text-red-600 dark:text-red-400 hover:text-red-700 dark:hover:text-red-300 transition-colors">Delete</button>
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <x-empty-state title="No solutions yet" description="Document how you resolved this problem to help your future self." icon='<svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>' />
                @endforelse
            </div>
        </section>

        <div class="border-t border-gray-200 dark:border-gray-800"></div>

        {{-- RELATED --}}
        @include('search.related-panel', ['entityType' => 'problem', 'entityId' => $problem->id])
    </div>

    {{-- Add Solution Modal --}}
    <x-modal name="add-solution" title="Add Solution" maxWidth="2xl">
        <form method="POST" action="{{ route('solutions.store', $problem) }}" class="space-y-6" x-data="{ submitting: false }" @submit="submitting = true">
            @csrf
            
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="sm:col-span-2">
                    <x-input label="Title" name="title" required placeholder="e.g. Updated the CSRF middleware whitelist" />
                </div>
                <div>
                    <x-select label="Type" name="solution_type" :options="['explanation' => 'Explanation', 'code' => 'Code', 'configuration' => 'Configuration', 'workaround' => 'Workaround', 'documentation' => 'Documentation', 'other' => 'Other']" required />
                </div>
            </div>

            <x-textarea label="Content" name="content" rows="8" required placeholder="Describe the solution, paste code, or explain the workaround..." class="font-mono text-sm" />

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-200 dark:border-gray-700">
                <x-button type="button" variant="ghost" @click="$dispatch('close-modal', 'add-solution')">Cancel</x-button>
                <x-button type="submit" variant="primary" x-bind:disabled="submitting">Save Solution</x-button>
            </div>
        </form>
    </x-modal>
</x-app-layout>

