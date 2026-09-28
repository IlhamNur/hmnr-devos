<x-app-layout>
    <div class="max-w-2xl mx-auto space-y-6">
        <div class="flex items-center gap-4">
            <a href="{{ route('projects.index') }}" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>
            <div>
                <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100 tracking-tight">Create Project</h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Initialize a new project in your workspace.</p>
            </div>
        </div>

        <x-card>
            <form method="POST" action="{{ route('projects.store') }}" class="space-y-6" x-data="{ submitting: false }" @submit="submitting = true">
                @csrf

                <div class="space-y-4">
                    <x-input label="Name" name="name" :value="old('name')" required autofocus />
                    
                    <x-textarea label="Description" name="description" :value="old('description')" rows="3" />
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <x-select label="Status" name="status" :options="['active' => 'Active', 'paused' => 'Paused', 'completed' => 'Completed', 'archived' => 'Archived']" :selected="old('status', 'active')" />
                        
                        <x-input label="Environment" name="environment" :value="old('environment')" placeholder="e.g. production, staging, local" />
                    </div>

                    <x-input label="Repository URL" type="url" name="repository_url" :value="old('repository_url')" placeholder="https://github.com/..." />
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <x-input label="Started At" type="date" name="started_at" :value="old('started_at')" />
                        <x-input label="Ended At" type="date" name="ended_at" :value="old('ended_at')" />
                    </div>

                    <x-input label="Tags (comma separated)" name="tags" :value="old('tags')" placeholder="e.g. php, laravel, vue" />
                </div>

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-200 dark:border-gray-800">
                    <a href="{{ route('projects.index') }}">
                        <x-button variant="ghost">Cancel</x-button>
                    </a>
                    <x-button type="submit" variant="primary" x-bind:disabled="submitting">
                        <span x-show="!submitting">Create Project</span>
                        <span x-show="submitting" x-cloak class="flex items-center gap-2">
                            <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                            Creating...
                        </span>
                    </x-button>
                </div>
            </form>
        </x-card>
    </div>
</x-app-layout>

