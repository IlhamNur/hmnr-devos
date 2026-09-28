<x-app-layout>
    <div class="max-w-4xl mx-auto space-y-8">
        {{-- Header Section --}}
        <div>
            <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4">
                <div class="flex-1 space-y-4">
                    <div class="flex items-center gap-3 text-sm text-gray-500 dark:text-gray-400">
                        <a href="{{ route('knowledge.index') }}" class="hover:text-gray-900 dark:hover:text-gray-200 transition-colors">Knowledge Base</a>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        <span class="font-medium text-gray-900 dark:text-gray-100 truncate max-w-[200px] sm:max-w-xs">{{ $knowledge->title }}</span>
                    </div>

                    <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100 tracking-tight leading-snug">{{ $knowledge->title }}</h1>

                    <div class="flex flex-wrap items-center gap-4 text-sm text-gray-500 dark:text-gray-400">
                        <div class="flex items-center gap-2">
                            <span class="font-medium">Type:</span>
                            <span class="bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400 text-xs font-mono px-2 py-0.5 rounded border border-gray-200 dark:border-gray-700">{{ ucfirst($knowledge->type) }}</span>
                        </div>
                        
                        @if($knowledge->project)
                            <div class="flex items-center gap-2">
                                <span class="font-medium">Project:</span>
                                <a href="{{ route('projects.show', $knowledge->project) }}" class="font-medium text-indigo-600 dark:text-indigo-400 hover:underline">
                                    {{ $knowledge->project->name }}
                                </a>
                            </div>
                        @endif

                        @if($knowledge->source)
                            <div class="flex items-center gap-2">
                                <span class="font-medium">Source:</span>
                                <a href="{{ $knowledge->source }}" target="_blank" class="text-indigo-600 dark:text-indigo-400 hover:underline flex items-center gap-1">
                                    <span class="truncate max-w-[200px]">{{ str_replace(['https://', 'http://'], '', $knowledge->source) }}</span>
                                    <svg class="w-3 h-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                </a>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="shrink-0">
                    <a href="{{ route('knowledge.edit', $knowledge) }}">
                        <x-button variant="secondary" size="sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                            Edit
                        </x-button>
                    </a>
                </div>
            </div>
            
            @if($knowledge->tags->isNotEmpty())
                <div class="flex items-center flex-wrap gap-2 mt-6 font-mono text-xs text-gray-500 dark:text-gray-400">
                    @foreach($knowledge->tags as $tag)
                        <span class="bg-gray-100 dark:bg-[#13151f] px-2 py-1 rounded border border-gray-200 dark:border-gray-800">{{ $tag->name }}</span>
                    @endforeach
                </div>
            @endif
        </div>

        <div class="border-t border-gray-200 dark:border-gray-800"></div>

        {{-- CONTENT --}}
        <section class="bg-white dark:bg-[#161925] rounded-xl border border-gray-200 dark:border-gray-800 p-6 md:p-8">
            <article class="prose dark:prose-invert max-w-none prose-indigo prose-a:text-indigo-600 dark:prose-a:text-indigo-400 hover:prose-a:text-indigo-500 prose-pre:bg-gray-900 prose-pre:border prose-pre:border-gray-800">
                {!! $renderedContent !!}
            </article>
        </section>
        
        {{-- Initialize Highlight.js for Markdown Code Blocks --}}
        <script>
            document.addEventListener('DOMContentLoaded', (event) => {
                document.querySelectorAll('article.prose pre code').forEach((el) => {
                    hljs.highlightElement(el);
                });
            });
        </script>
    </div>
</x-app-layout>

