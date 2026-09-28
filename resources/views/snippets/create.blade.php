<x-app-layout>
    <div class="max-w-4xl mx-auto space-y-6">
        <div class="flex items-center gap-4">
            <a href="{{ route('snippets.index') }}" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>
            <div>
                <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100 tracking-tight">Create Snippet</h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Save a reusable block of code.</p>
            </div>
        </div>

        <x-card>
            <form method="POST" action="{{ route('snippets.store') }}" class="space-y-6" x-data="{ submitting: false }" @submit="submitting = true">
                @csrf

                <div class="space-y-6">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <x-input label="Title" name="title" :value="old('title')" placeholder="e.g. Postgres DB Connection" required autofocus />
                        <x-input label="Title" name="title" :value="old('title', request('title'))" placeholder="e.g. Postgres DB Connection" required autofocus />
                        
                        @php
                            $languages = ['php' => 'PHP', 'javascript' => 'JavaScript', 'typescript' => 'TypeScript', 'java' => 'Java', 'python' => 'Python', 'sql' => 'SQL', 'html' => 'HTML', 'css' => 'CSS', 'json' => 'JSON', 'bash' => 'Bash', 'mvel' => 'MVEL'];
                        @endphp
                        <x-select label="Language" name="language" :options="$languages" :selected="old('language', 'php')" required />
                        <x-select label="Language" name="language" :options="$languages" :selected="old('language', request('language', 'php'))" required />
                    </div>

                    <x-textarea label="Code" name="code" :value="old('code')" rows="10" class="font-mono text-sm leading-relaxed" placeholder="Paste your code here..." required />
                    <x-textarea label="Code" name="code" :value="old('code', request('code'))" rows="10" class="font-mono text-sm leading-relaxed" placeholder="Paste your code here..." required />
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <x-input label="Purpose" name="purpose" :value="old('purpose')" placeholder="e.g. Standard DB connection configuration" />
                        
                        <x-select label="Project" name="project_id" :options="$projects->pluck('name', 'id')->toArray()" :selected="old('project_id', request('project_id'))" placeholder="None (Global)" />
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <x-input label="Environment" name="environment" :value="old('environment')" placeholder="e.g. Laravel 11, Node 20" />
                        
                        <x-input label="Version" name="version" :value="old('version')" placeholder="e.g. v1.0.0" />
                    </div>
                    
                    <x-textarea label="Description (Optional)" name="description" :value="old('description')" rows="3" placeholder="Additional context about this snippet..." />
                    
                    <x-input label="Tags (comma separated)" name="tags" :value="old('tags')" placeholder="e.g. database, config, boilerplate" />
                </div>

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-200 dark:border-gray-800">
                    <a href="{{ route('snippets.index') }}">
                        <x-button variant="ghost">Cancel</x-button>
                    </a>
                    <x-button type="submit" variant="primary" x-bind:disabled="submitting">
                        <span x-show="!submitting">Save Snippet</span>
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

