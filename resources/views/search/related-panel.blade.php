{{-- Related content component for Problem, Snippet, and Knowledge detail pages --}}
{{-- Usage: @include('search.related-panel', ['entityType' => 'problem', 'entityId' => $problem->id]) --}}

<section class="space-y-4" x-data="relatedContent('{{ $entityType }}', {{ $entityId }})" x-init="load()">
    <div class="flex items-center justify-between">
        <h3 class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-widest">Related</h3>
        <button @click="load()" class="text-xs text-indigo-600 dark:text-indigo-400 hover:underline" x-show="!loading" x-cloak>Refresh</button>
        <span class="text-xs text-gray-400" x-show="loading">Loading...</span>
    </div>

    <template x-if="results.length > 0">
        <div class="space-y-2">
            <template x-for="item in results" :key="item.type + '-' + item.id">
                <a :href="item.url" class="block bg-white dark:bg-[#161925] rounded-lg border border-gray-200 dark:border-gray-800 p-4 hover:border-indigo-300 dark:hover:border-indigo-500/50 transition-colors group">
                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0 flex-1">
                            <h4 class="text-sm font-medium text-gray-900 dark:text-gray-100 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 truncate transition-colors" x-text="item.title"></h4>
                            <div class="flex items-center gap-2 mt-1.5">
                                <span class="text-[10px] font-mono uppercase bg-gray-100 dark:bg-gray-800 text-gray-500 dark:text-gray-400 px-1.5 py-0.5 rounded" x-text="item.type"></span>
                                <template x-if="item.context">
                                    <span class="text-xs text-gray-500 dark:text-gray-400 truncate" x-text="item.context"></span>
                                </template>
                                <template x-if="item.status === 'solved'">
                                    <span class="text-[10px] bg-green-100 dark:bg-green-500/20 text-green-700 dark:text-green-400 px-1.5 py-0.5 rounded-full font-medium">Solved</span>
                                </template>
                                <template x-if="item.status === 'verified'">
                                    <span class="text-[10px] bg-green-100 dark:bg-green-500/20 text-green-700 dark:text-green-400 px-1.5 py-0.5 rounded-full font-medium flex items-center gap-0.5">
                                        <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                        Verified
                                    </span>
                                </template>
                            </div>
                        </div>
                        <span class="text-[10px] font-medium shrink-0 mt-0.5 px-2 py-0.5 rounded-full"
                              :class="{
                                  'bg-indigo-100 dark:bg-indigo-500/20 text-indigo-700 dark:text-indigo-400': item.relevance_label === 'Highly related',
                                  'bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400': item.relevance_label === 'Related',
                                  'bg-gray-50 dark:bg-gray-900 text-gray-500 dark:text-gray-500': item.relevance_label === 'Possible match'
                              }"
                              x-text="item.relevance_label"></span>
                    </div>
                </a>
            </template>
        </div>
    </template>

    <template x-if="!loading && results.length === 0">
        <div class="bg-gray-50 dark:bg-[#13151f] rounded-lg p-6 border border-gray-200 dark:border-gray-800 text-center">
            <p class="text-sm text-gray-500 dark:text-gray-400">No related content found yet.</p>
        </div>
    </template>
</section>

<script>
function relatedContent(entityType, entityId) {
    return {
        results: [],
        loading: false,
        async load() {
            this.loading = true;
            try {
                const response = await fetch(`/search/related?type=${entityType}&id=${entityId}`, {
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                });
                const data = await response.json();
                this.results = data.results || [];
            } catch (e) {
                this.results = [];
            }
            this.loading = false;
        }
    }
}
</script>

