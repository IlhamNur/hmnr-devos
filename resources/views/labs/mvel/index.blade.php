<x-app-layout>
    <div class="flex flex-col h-[calc(100vh-8rem)]">
        <div class="flex items-center justify-between mb-4 shrink-0">
            <div>
                <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100 tracking-tight">MVEL Playground</h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Evaluate and test MVEL expressions securely.</p>
            </div>
            
            <div x-data>
                <form method="GET" id="saveSnippetForm" action="{{ route('snippets.create') }}" class="hidden">
                    <input type="hidden" name="title" value="MVEL Expression">
                    <input type="hidden" name="language" value="mvel">
                    <input type="hidden" name="code" id="snippetCode">
                </form>
            </div>
        </div>

        <div class="flex-1 grid grid-cols-1 lg:grid-cols-2 gap-6 min-h-0" x-data="mvelLab()">
            
            {{-- Input Column --}}
            <div class="flex flex-col gap-6 h-full">
                
                {{-- Variables --}}
                <div class="flex-1 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 flex flex-col min-h-0">
                    <div class="p-3 border-b border-gray-200 dark:border-gray-700 shrink-0">
                        <h3 class="font-medium text-sm text-gray-900 dark:text-white">Variables (JSON)</h3>
                    </div>
                    <div class="flex-1 p-0 relative">
                        <textarea x-model="variables" class="absolute inset-0 w-full h-full bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 font-mono text-sm border-0 focus:ring-0 resize-none p-4 rounded-b-xl" placeholder="{&#10;  &#34;name&#34;: &#34;Ilham&#34;&#10;}"></textarea>
                    </div>
                </div>

                {{-- Expression --}}
                <div class="flex-1 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 flex flex-col min-h-0">
                    <div class="p-3 border-b border-gray-200 dark:border-gray-700 shrink-0 flex items-center justify-between">
                        <h3 class="font-medium text-sm text-gray-900 dark:text-white">Expression</h3>
                        <x-button type="button" @click="runExpression()" x-bind:disabled="loading" size="sm">
                            <span x-show="!loading">RUN</span>
                            <span x-show="loading">...</span>
                        </x-button>
                    </div>
                    <div class="flex-1 p-0 relative">
                        <textarea x-model="expression" class="absolute inset-0 w-full h-full bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 font-mono text-sm border-0 focus:ring-0 resize-none p-4 rounded-b-xl" placeholder="employee.name" @keydown.cmd.enter.prevent="runExpression()" @keydown.ctrl.enter.prevent="runExpression()"></textarea>
                    </div>
                </div>

            </div>

            {{-- Output Column --}}
            <div class="flex flex-col gap-6 h-full">
                
                {{-- Result --}}
                <div class="flex-1 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 flex flex-col min-h-0">
                    <div class="p-3 border-b border-gray-200 dark:border-gray-700 shrink-0 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <h3 class="font-medium text-sm text-gray-900 dark:text-white">Result</h3>
                            <template x-if="response && !response.error">
                                <span class="text-xs font-mono text-gray-500 bg-gray-100 dark:bg-gray-700 px-2 py-0.5 rounded" x-text="response.result_type"></span>
                            </template>
                            <template x-if="response">
                                <span class="text-xs text-gray-400" x-text="response.execution_time + 'ms'"></span>
                            </template>
                        </div>
                        <template x-if="response && !response.error">
                            <x-button type="button" variant="ghost" size="sm" @click="saveSnippet()">Save as Snippet</x-button>
                        </template>
                    </div>
                    <div class="flex-1 overflow-auto p-4 bg-gray-50 dark:bg-gray-900 rounded-b-xl relative font-mono text-sm">
                        <div x-show="!response && !loading" class="absolute inset-0 flex items-center justify-center text-gray-400">
                            Result will appear here
                        </div>
                        
                        <template x-if="response && response.error">
                            <div class="text-red-500 whitespace-pre-wrap" x-text="response.error"></div>
                        </template>

                        <template x-if="response && !response.error">
                            <div class="text-gray-900 dark:text-gray-100 whitespace-pre-wrap" x-text="response.result"></div>
                        </template>
                    </div>
                </div>

                {{-- History Sidebar --}}
                <div class="flex-1 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 flex flex-col min-h-0">
                    <div class="p-3 border-b border-gray-200 dark:border-gray-700 shrink-0">
                        <h3 class="font-medium text-sm text-gray-900 dark:text-white">Execution History</h3>
                    </div>
                    <div class="flex-1 overflow-y-auto p-2 space-y-1">
                        @if($history->isEmpty())
                            <div class="p-4 text-center text-sm text-gray-500 dark:text-gray-400">No history.</div>
                        @else
                            @foreach($history as $item)
                                <div class="group flex flex-col p-2 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700/50 cursor-pointer transition-colors"
                                     @click="loadHistory({{ json_encode(['expression' => $item->expression, 'variables' => is_array($item->variables) ? json_encode($item->variables, JSON_PRETTY_PRINT) : $item->variables]) }})">
                                    <div class="flex items-center justify-between">
                                        <div class="font-mono text-xs text-gray-700 dark:text-gray-300 truncate pr-4">{{ $item->expression }}</div>
                                        <form method="POST" action="{{ route('mvel-lab.destroy', $item) }}" class="opacity-0 group-hover:opacity-100 transition-opacity">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-gray-400 hover:text-red-500" @click.stop>
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        </form>
                                    </div>
                                    <div class="text-[10px] text-gray-400 mt-1">{{ $item->updated_at->diffForHumans() }} &middot; {{ $item->error ? 'Error' : 'Success' }}</div>
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>

            </div>
        </div>
    </div>

    <script>
        function mvelLab() {
            return {
                loading: false,
                variables: '{\n  \n}',
                expression: '',
                response: null,

                loadHistory(data) {
                    this.expression = data.expression;
                    this.variables = data.variables || '{\n  \n}';
                },

                async runExpression() {
                    if (!this.expression) return;
                    
                    this.loading = true;
                    this.response = null;
                    
                    try {
                        const res = await fetch('{{ route("mvel-lab.execute") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: JSON.stringify({
                                expression: this.expression,
                                variables: this.variables
                            })
                        });
                        
                        const data = await res.json();
                        
                        if (res.ok) {
                            this.response = data.result;
                        } else {
                            this.response = { error: data.error || 'Execution Error' };
                        }
                    } catch (e) {
                        this.response = { error: e.message };
                    } finally {
                        this.loading = false;
                    }
                },
                
                saveSnippet() {
                    document.getElementById('snippetCode').value = this.expression;
                    document.getElementById('saveSnippetForm').submit();
                }
            }
        }
    </script>
</x-app-layout>
