<x-app-layout>
    <div class="max-w-4xl mx-auto space-y-8">
        {{-- Header Section --}}
        <div>
            <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4">
                <div class="flex-1 space-y-4">
                    <div class="flex items-center gap-3 text-sm text-gray-500 dark:text-gray-400">
                        <a href="{{ route('snippets.index') }}" class="hover:text-gray-900 dark:hover:text-gray-200 transition-colors">Snippets</a>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        <span class="font-medium text-gray-900 dark:text-gray-100 truncate max-w-[200px] sm:max-w-xs">{{ $snippet->title }}</span>
                    </div>

                    <div class="flex items-center gap-3">
                        <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100 tracking-tight leading-snug">{{ $snippet->title }}</h1>
                        <form method="POST" action="{{ route('snippets.favorite', $snippet) }}">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="{{ $snippet->is_favorite ? 'text-yellow-400 hover:text-yellow-500' : 'text-gray-300 dark:text-gray-600 hover:text-yellow-400' }} transition-colors" title="Toggle Favorite">
                                <svg class="w-7 h-7 fill-current" viewBox="0 0 24 24"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/></svg>
                            </button>
                        </form>
                    </div>

                    <div class="flex flex-wrap items-center gap-4 text-sm text-gray-500 dark:text-gray-400">
                        <div class="flex items-center gap-2">
                            <span class="font-medium">Language:</span>
                            <span class="bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400 text-xs font-mono px-2 py-0.5 rounded border border-gray-200 dark:border-gray-700">{{ strtoupper($snippet->language) }}</span>
                        </div>
                        
                        @if($snippet->purpose)
                            <div class="flex items-center gap-2">
                                <span class="font-medium">Purpose:</span>
                                <span class="text-gray-700 dark:text-gray-300">{{ $snippet->purpose }}</span>
                            </div>
                        @endif

                        @if($snippet->project)
                            <div class="flex items-center gap-2">
                                <span class="font-medium">Project:</span>
                                <a href="{{ route('projects.show', $snippet->project) }}" class="font-medium text-indigo-600 dark:text-indigo-400 hover:underline">
                                    {{ $snippet->project->name }}
                                </a>
                            </div>
                        @endif
                        
                        @if($snippet->environment)
                            <div class="flex items-center gap-2">
                                <span class="font-medium">Env:</span>
                                <span class="text-gray-700 dark:text-gray-300">{{ $snippet->environment }}</span>
                            </div>
                        @endif
                        
                        @if($snippet->version)
                            <div class="flex items-center gap-2">
                                <span class="font-medium">Version:</span>
                                <span class="font-mono text-gray-700 dark:text-gray-300">{{ $snippet->version }}</span>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="shrink-0">
                    <a href="{{ route('snippets.edit', $snippet) }}">
                        <x-button variant="secondary" size="sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                            Edit
                        </x-button>
                    </a>
                </div>
            </div>
        </div>

        <div class="border-t border-gray-200 dark:border-gray-800"></div>

        {{-- CODE --}}
        <section class="space-y-4">
            <div class="flex items-center justify-between">
                <h3 class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-widest">Code</h3>
                
                <div x-data="{
                        copied: false,
                        copyCode() {
                            navigator.clipboard.writeText($refs.codeBlock.innerText).then(() => {
                                this.copied = true;
                                setTimeout(() => this.copied = false, 2000);
                            });
                        }
                    }">
                    <x-button variant="ghost" size="sm" @click="copyCode()" class="!text-xs">
                        <span x-show="!copied" class="flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                            Copy
                        </span>
                        <span x-show="copied" x-cloak class="flex items-center gap-2 text-green-600 dark:text-green-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            Copied!
                        </span>
                    </x-button>
                </div>
            </div>
            
            <div class="rounded-lg overflow-hidden border border-gray-200 dark:border-gray-700 bg-[#282c34] text-sm">
                <pre><code x-ref="codeBlock" class="language-{{ strtolower($snippet->language) }} !bg-transparent !p-4" style="font-family: 'JetBrains Mono', monospace;">{{ $snippet->code }}</code></pre>
            </div>
        </section>

        @if($snippet->description || $snippet->tags->isNotEmpty())
            <div class="border-t border-gray-200 dark:border-gray-800"></div>
            
            {{-- DESCRIPTION & TAGS --}}
            <section class="space-y-4">
                @if($snippet->description)
                    <div>
                        <h3 class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-widest mb-3">Description</h3>
                        <div class="prose dark:prose-invert max-w-none text-gray-800 dark:text-gray-200 text-sm leading-relaxed whitespace-pre-wrap">{{ $snippet->description }}</div>
                    </div>
                @endif
                
                @if($snippet->tags->isNotEmpty())
                    <div class="{{ $snippet->description ? 'mt-6' : '' }}">
                        <h3 class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-widest mb-3">Tags</h3>
                        <div class="flex items-center flex-wrap gap-2 font-mono text-xs text-gray-500 dark:text-gray-400">
                            @foreach($snippet->tags as $tag)
                                <span class="bg-gray-100 dark:bg-[#13151f] px-2 py-1 rounded border border-gray-200 dark:border-gray-800">{{ $tag->name }}</span>
                            @endforeach
                        </div>
                    </div>
                @endif
            </section>
        @endif
    </div>

    {{-- Initialize Highlight.js --}}
    <script>
        document.addEventListener('DOMContentLoaded', (event) => {
            document.querySelectorAll('pre code').forEach((el) => {
                hljs.highlightElement(el);
            });
        });
    </script>
</x-app-layout>

