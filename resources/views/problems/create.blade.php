<x-app-layout>
    <div class="max-w-3xl mx-auto space-y-6">
        <div class="flex items-center gap-4">
            <a href="{{ route('problems.index') }}" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>
            <div>
                <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100 tracking-tight">Log Problem</h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Document a bug, error, or challenge for future reference.</p>
            </div>
        </div>

        <x-card>
            <form method="POST" action="{{ route('problems.store') }}" class="space-y-6" x-data="{ submitting: false }" @submit="submitting = true">
                @csrf

                <div class="space-y-6">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <x-input label="Title" name="title" :value="old('title')" placeholder="e.g. CSRF Token Mismatch on Login" required autofocus />
                        
                        <x-select label="Project" name="project_id" :options="$projects->pluck('name', 'id')->toArray()" :selected="old('project_id', request('project_id'))" placeholder="None (Global)" />
                    </div>
                    
                    <x-textarea label="Description" name="description" :value="old('description')" rows="4" placeholder="Describe what you were trying to do and what happened instead..." required />
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <x-input label="Error Type" name="error_type" :value="old('error_type')" placeholder="e.g. TokenMismatchException" class="font-mono text-sm" />
                        
                        <x-input label="Environment" name="environment" :value="old('environment')" placeholder="e.g. PHP 8.3, Laravel 11, Production" />
                    </div>

                    <x-textarea label="Error Message / Stack Trace" name="error_message" :value="old('error_message')" rows="4" class="font-mono text-sm" placeholder="Paste the raw error message here..." />

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <x-select label="Severity" name="severity" :options="['low' => 'Low', 'medium' => 'Medium', 'high' => 'High', 'critical' => 'Critical']" :selected="old('severity', 'medium')" />
                        
                        <x-select label="Status" name="status" :options="['open' => 'Open', 'investigating' => 'Investigating', 'solved' => 'Solved', 'archived' => 'Archived']" :selected="old('status', 'open')" />
                    </div>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <x-input label="Occurred At" type="datetime-local" name="occurred_at" :value="old('occurred_at', now()->format('Y-m-d\TH:i'))" />
                        
                        <x-input label="Tags (comma separated)" name="tags" :value="old('tags')" placeholder="e.g. frontend, auth, config" />
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-200 dark:border-gray-800">
                    <a href="{{ route('problems.index') }}">
                        <x-button variant="ghost">Cancel</x-button>
                    </a>
                    <x-button type="submit" variant="primary" x-bind:disabled="submitting">
                        <span x-show="!submitting">Log Problem</span>
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

