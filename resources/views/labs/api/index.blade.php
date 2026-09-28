<x-app-layout>
    <div class="flex flex-col h-[calc(100vh-8rem)]">
        <div class="flex items-center justify-between mb-4 shrink-0">
            <div>
                <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100 tracking-tight">API Lab</h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Test and debug HTTP requests.</p>
            </div>
            
            <div x-data>
                <form method="POST" id="saveToKnowledgeForm" action="{{ route('knowledge.create') }}" class="hidden">
                    @csrf
                    <input type="hidden" name="title" id="knowledgeTitle">
                    <input type="hidden" name="type" value="reference">
                    <input type="hidden" name="content" id="knowledgeContent">
                </form>
            </div>
        </div>

        <div class="flex-1 grid grid-cols-1 lg:grid-cols-4 gap-6 min-h-0">
            
            {{-- History Sidebar --}}
            <div class="hidden lg:flex flex-col bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden h-full">
                <div class="p-4 border-b border-gray-200 dark:border-gray-700 shrink-0">
                    <h3 class="font-medium text-gray-900 dark:text-white">History</h3>
                </div>
                <div class="flex-1 overflow-y-auto p-2 space-y-1">
                    @if($history->isEmpty())
                        <div class="p-4 text-center text-sm text-gray-500 dark:text-gray-400">No requests yet.</div>
                    @else
                        @foreach($history as $item)
                            <div class="group flex flex-col p-3 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700/50 cursor-pointer transition-colors"
                                 @click="$dispatch('load-request', {{ json_encode([
                                     'method' => $item->method,
                                     'url' => $item->url,
                                     'headers' => $item->headers ?? [],
                                     'query_parameters' => $item->query_parameters ?? [],
                                     'body_type' => $item->body_type,
                                     'body' => $item->body,
                                 ]) }})">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-2 truncate">
                                        @php
                                            $methodColors = [
                                                'GET' => 'text-blue-600 dark:text-blue-400',
                                                'POST' => 'text-green-600 dark:text-green-400',
                                                'PUT' => 'text-orange-600 dark:text-orange-400',
                                                'PATCH' => 'text-yellow-600 dark:text-yellow-400',
                                                'DELETE' => 'text-red-600 dark:text-red-400',
                                            ];
                                            $color = $methodColors[$item->method] ?? 'text-gray-600 dark:text-gray-400';
                                        @endphp
                                        <span class="text-xs font-bold {{ $color }}">{{ $item->method }}</span>
                                        <span class="text-sm text-gray-700 dark:text-gray-300 truncate">{{ $item->url }}</span>
                                    </div>
                                    <form method="POST" action="{{ route('api-lab.destroy', $item) }}" class="opacity-0 group-hover:opacity-100 transition-opacity">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-gray-400 hover:text-red-500" @click.stop>
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                </div>
                                <div class="flex items-center gap-2 mt-1">
                                    <span class="text-xs text-gray-500 {{ $item->last_status >= 400 ? 'text-red-500' : 'text-green-500' }}">{{ $item->last_status }}</span>
                                    <span class="text-[10px] text-gray-400">{{ $item->updated_at->diffForHumans() }}</span>
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>

            {{-- Main Workspace --}}
            <div class="col-span-1 lg:col-span-3 flex flex-col h-full space-y-4" x-data="apiLab()">
                
                {{-- Request Builder --}}
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4 shrink-0">
                    <div class="flex gap-2 mb-4">
                        <select x-model="request.method" class="w-24 rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            <option>GET</option>
                            <option>POST</option>
                            <option>PUT</option>
                            <option>PATCH</option>
                            <option>DELETE</option>
                        </select>
                        <input type="text" x-model="request.url" placeholder="https://api.example.com/v1/users" class="flex-1 rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-indigo-500 focus:border-indigo-500 font-mono sm:text-sm" @keydown.enter="sendRequest()">
                        <x-button type="button" @click="sendRequest()" x-bind:disabled="loading" class="w-24">
                            <span x-show="!loading">Send</span>
                            <svg x-show="loading" class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" x-cloak>
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </x-button>
                    </div>

                    {{-- Tabs --}}
                    <div class="border-b border-gray-200 dark:border-gray-700 mb-4">
                        <nav class="-mb-px flex space-x-6">
                            <button @click="tab = 'params'" :class="tab === 'params' ? 'border-indigo-500 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300'" class="whitespace-nowrap pb-3 border-b-2 font-medium text-sm">Query Params</button>
                            <button @click="tab = 'headers'" :class="tab === 'headers' ? 'border-indigo-500 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300'" class="whitespace-nowrap pb-3 border-b-2 font-medium text-sm">Headers</button>
                            <button @click="tab = 'body'" :class="tab === 'body' ? 'border-indigo-500 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300'" class="whitespace-nowrap pb-3 border-b-2 font-medium text-sm">Body</button>
                        </nav>
                    </div>

                    {{-- Params Tab --}}
                    <div x-show="tab === 'params'" class="space-y-2 max-h-48 overflow-y-auto">
                        <template x-for="(param, index) in request.query_parameters" :key="index">
                            <div class="flex gap-2">
                                <input type="text" x-model="param.key" placeholder="Key" class="flex-1 rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 sm:text-sm dark:text-white">
                                <input type="text" x-model="param.value" placeholder="Value" class="flex-1 rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 sm:text-sm dark:text-white">
                                <button @click="request.query_parameters.splice(index, 1)" class="p-2 text-gray-400 hover:text-red-500">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                </button>
                            </div>
                        </template>
                        <x-button type="button" variant="ghost" size="sm" @click="request.query_parameters.push({key: '', value: ''})">+ Add Parameter</x-button>
                    </div>

                    {{-- Headers Tab --}}
                    <div x-show="tab === 'headers'" class="space-y-2 max-h-48 overflow-y-auto" x-cloak>
                        <template x-for="(header, index) in request.headers" :key="index">
                            <div class="flex gap-2">
                                <input type="text" x-model="header.key" placeholder="Key (e.g. Authorization)" class="flex-1 rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 sm:text-sm dark:text-white">
                                <input type="text" x-model="header.value" placeholder="Value" class="flex-1 rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 sm:text-sm dark:text-white">
                                <button @click="request.headers.splice(index, 1)" class="p-2 text-gray-400 hover:text-red-500">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                </button>
                            </div>
                        </template>
                        <x-button type="button" variant="ghost" size="sm" @click="request.headers.push({key: '', value: ''})">+ Add Header</x-button>
                    </div>

                    {{-- Body Tab --}}
                    <div x-show="tab === 'body'" x-cloak>
                        <div class="flex gap-4 mb-2 text-sm">
                            <label class="flex items-center gap-2"><input type="radio" x-model="request.body_type" value="none" class="text-indigo-600"> None</label>
                            <label class="flex items-center gap-2"><input type="radio" x-model="request.body_type" value="json" class="text-indigo-600"> JSON</label>
                            <label class="flex items-center gap-2"><input type="radio" x-model="request.body_type" value="form" class="text-indigo-600"> Form Data</label>
                            <label class="flex items-center gap-2"><input type="radio" x-model="request.body_type" value="raw" class="text-indigo-600"> Raw</label>
                        </div>
                        <div x-show="request.body_type !== 'none'">
                            <textarea x-model="request.body" rows="4" class="w-full rounded-md border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 font-mono text-sm" placeholder="Enter request body here..."></textarea>
                        </div>
                    </div>
                </div>

                {{-- Response Area --}}
                <div class="flex-1 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 flex flex-col min-h-0">
                    <div class="p-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between shrink-0">
                        <div class="flex items-center gap-4">
                            <h3 class="font-medium text-gray-900 dark:text-white">Response</h3>
                            <template x-if="response">
                                <div class="flex items-center gap-3 text-sm">
                                    <span :class="response.status >= 400 ? 'text-red-600 dark:text-red-400' : 'text-green-600 dark:text-green-400'" class="font-semibold" x-text="response.status ? response.status + ' Status' : 'Error'"></span>
                                    <span class="text-gray-500 dark:text-gray-400 font-mono" x-text="response.duration + ' ms'"></span>
                                </div>
                            </template>
                        </div>
                        <div class="flex items-center gap-2">
                            <template x-if="response && !response.error">
                                <div class="flex items-center gap-2 bg-gray-100 dark:bg-gray-900 rounded-lg p-1">
                                    <button @click="responseView = 'pretty'" :class="responseView === 'pretty' ? 'bg-white dark:bg-gray-700 shadow text-gray-900 dark:text-white' : 'text-gray-500 hover:text-gray-700 dark:hover:text-gray-300'" class="px-3 py-1 rounded-md text-xs font-medium transition-all">Pretty</button>
                                    <button @click="responseView = 'raw'" :class="responseView === 'raw' ? 'bg-white dark:bg-gray-700 shadow text-gray-900 dark:text-white' : 'text-gray-500 hover:text-gray-700 dark:hover:text-gray-300'" class="px-3 py-1 rounded-md text-xs font-medium transition-all">Raw</button>
                                    <button @click="responseView = 'headers'" :class="responseView === 'headers' ? 'bg-white dark:bg-gray-700 shadow text-gray-900 dark:text-white' : 'text-gray-500 hover:text-gray-700 dark:hover:text-gray-300'" class="px-3 py-1 rounded-md text-xs font-medium transition-all">Headers</button>
                                </div>
                                <x-button variant="ghost" size="sm" @click="navigator.clipboard.writeText(response.body); $dispatch('toast', {message: 'Copied!'})">Copy</x-button>
                            </template>
                            <template x-if="response">
                                <x-button variant="secondary" size="sm" @click="saveToKnowledge()">Save to Knowledge</x-button>
                            </template>
                        </div>
                    </div>
                    
                    <div class="flex-1 overflow-auto p-4 bg-gray-50 dark:bg-gray-900 rounded-b-xl relative">
                        <div x-show="!response && !loading" class="absolute inset-0 flex items-center justify-center text-gray-400">
                            Enter URL and click Send to get a response
                        </div>
                        
                        <template x-if="response && response.error">
                            <div class="text-red-500 font-mono text-sm" x-text="response.error"></div>
                        </template>

                        <template x-if="response && !response.error">
                            <div class="h-full">
                                {{-- Pretty View --}}
                                <div x-show="responseView === 'pretty'" class="h-full">
                                    <pre><code class="language-json rounded-lg h-full overflow-auto text-sm" x-text="formatBody(response.body)"></code></pre>
                                </div>
                                
                                {{-- Raw View --}}
                                <div x-show="responseView === 'raw'" class="h-full">
                                    <textarea readonly class="w-full h-full bg-transparent border-0 font-mono text-sm text-gray-800 dark:text-gray-300 focus:ring-0 p-0 resize-none" x-text="response.body"></textarea>
                                </div>
                                
                                {{-- Headers View --}}
                                <div x-show="responseView === 'headers'" class="h-full">
                                    <pre><code class="language-json rounded-lg h-full overflow-auto text-sm" x-text="JSON.stringify(response.headers, null, 2)"></code></pre>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function apiLab() {
            return {
                loading: false,
                tab: 'params',
                responseView: 'pretty',
                request: {
                    method: 'GET',
                    url: '',
                    query_parameters: [],
                    headers: [
                        {key: 'Accept', value: 'application/json'}
                    ],
                    body_type: 'none',
                    body: ''
                },
                response: null,

                init() {
                    this.$el.addEventListener('load-request', (e) => {
                        this.request = e.detail;
                        // Ensure lists have at least one empty item for UI if empty
                        if (!this.request.query_parameters) this.request.query_parameters = [];
                        if (!this.request.headers) this.request.headers = [];
                    });
                },

                async sendRequest() {
                    if (!this.request.url) return;
                    
                    this.loading = true;
                    this.response = null;
                    
                    try {
                        const res = await fetch('{{ route("api-lab.execute") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: JSON.stringify(this.request)
                        });
                        
                        const data = await res.json();
                        
                        if (res.ok) {
                            this.response = data.result;
                            this.responseView = 'pretty';
                            
                            // Highlight JS
                            this.$nextTick(() => {
                                document.querySelectorAll('pre code').forEach((block) => {
                                    hljs.highlightElement(block);
                                });
                            });
                        } else {
                            this.response = { error: data.message || 'Validation or Server Error' };
                        }
                    } catch (e) {
                        this.response = { error: e.message };
                    } finally {
                        this.loading = false;
                        // Reload page or refresh history in a real app, but for now we'll just show it.
                        // Ideally we fetch history again, but to keep it simple, they can refresh.
                    }
                },
                
                formatBody(body) {
                    if (!body) return '';
                    try {
                        const obj = JSON.parse(body);
                        return JSON.stringify(obj, null, 2);
                    } catch (e) {
                        return body;
                    }
                },
                
                saveToKnowledge() {
                    const title = `${this.request.method} ${this.request.url}`;
                    let content = `### Request\n\n**Method:** ${this.request.method}\n**URL:** ${this.request.url}\n\n`;
                    
                    if (this.request.headers && this.request.headers.length > 0) {
                        content += `**Headers:**\n\`\`\`json\n${JSON.stringify(this.request.headers.filter(h => h.key), null, 2)}\n\`\`\`\n\n`;
                    }
                    
                    if (this.request.body_type !== 'none' && this.request.body) {
                        content += `**Body (${this.request.body_type}):**\n\`\`\`\n${this.request.body}\n\`\`\`\n\n`;
                    }
                    
                    content += `### Response\n\n**Status:** ${this.response.status}\n**Time:** ${this.response.duration}ms\n\n`;
                    content += `**Body:**\n\`\`\`json\n${this.formatBody(this.response.body)}\n\`\`\`\n`;
                    
                    document.getElementById('knowledgeTitle').value = title;
                    document.getElementById('knowledgeContent').value = content;
                    document.getElementById('saveToKnowledgeForm').submit();
                }
            }
        }
    </script>
</x-app-layout>
