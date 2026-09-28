<x-app-layout>
    <div class="max-w-4xl mx-auto space-y-6">
        <div class="flex items-center gap-4">
            <a href="{{ route('knowledge.index') }}" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>
            <div>
                <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100 tracking-tight">Create Knowledge Entry</h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Write documentation, concepts, or references. Markdown supported.</p>
            </div>
        </div>

        <x-card>
            <form method="POST" action="{{ route('knowledge.store') }}" class="space-y-6" x-data="{ submitting: false }" @submit="submitting = true">
                @csrf

                <div class="space-y-6">
                    <x-input label="Title" name="title" :value="old('title')" placeholder="e.g. System Architecture v2" required autofocus />
                    <x-input label="Title" name="title" :value="old('title', request('title'))" placeholder="e.g. System Architecture v2" required autofocus />
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        @php
                            $types = ['note' => 'Note', 'documentation' => 'Documentation', 'reference' => 'Reference', 'concept' => 'Concept', 'tutorial' => 'Tutorial'];
                        @endphp
                        <x-select label="Type" name="type" :options="$types" :selected="old('type', 'note')" required />
                        <x-select label="Type" name="type" :options="$types" :selected="old('type', request('type', 'note'))" required />
                        
                        <x-select label="Project" name="project_id" :options="$projects->pluck('name', 'id')->toArray()" :selected="old('project_id', request('project_id'))" placeholder="None (Global)" />
                    </div>

                    <x-textarea label="Content (Markdown)" name="content" :value="old('content')" rows="15" class="font-mono text-sm leading-relaxed" placeholder="# Introduction..." required />
                    <x-textarea label="Content (Markdown)" name="content" :value="old('content', request('content'))" rows="15" class="font-mono text-sm leading-relaxed" placeholder="# Introduction..." required />
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <x-input label="Source URL (Optional)" type="url" name="source" :value="old('source')" placeholder="https://..." />
                        
                        <x-input label="Tags (comma separated)" name="tags" :value="old('tags')" placeholder="e.g. architecture, design, backend" />
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-200 dark:border-gray-800">
                    <a href="{{ route('knowledge.index') }}">
                        <x-button variant="ghost">Cancel</x-button>
                    </a>
                    <x-button type="submit" variant="primary" x-bind:disabled="submitting">
                        <span x-show="!submitting">Save Entry</span>
                        <span x-show="submitting" x-cloak class="flex items-center gap-2">
                            <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                            Saving...
                        </span>
                    </x-button>
                </div>
            </form>
        </x-card>
    </div>
</x-app-layout>

