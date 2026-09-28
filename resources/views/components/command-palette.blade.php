<div
    x-data="commandPalette()"
    x-on:keydown.window.prevent.cmd.k="open = true"
    x-on:keydown.window.prevent.ctrl.k="open = true"
    x-on:open-command-palette.window="open = true"
    x-show="open"
    x-cloak
    class="fixed inset-0 z-50 overflow-y-auto p-4 sm:p-6 md:p-20"
    role="dialog"
    aria-modal="true"
>
    <!-- Backdrop -->
    <div 
        x-show="open" 
        x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm transition-opacity" 
        @click="open = false"
    ></div>

    <!-- Palette -->
    <div 
        x-show="open"
        x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        class="relative mx-auto max-w-xl transform divide-y divide-gray-100 dark:divide-gray-800 overflow-hidden rounded-xl bg-white dark:bg-[#161925] shadow-2xl ring-1 ring-gray-200 dark:ring-gray-800 transition-all"
        @click.away="open = false"
    >
        <div class="relative">
            <svg class="pointer-events-none absolute left-4 top-3.5 h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
            </svg>
            <input 
                x-ref="searchInput"
                x-model="search"
                type="text"
                class="h-12 w-full border-0 bg-transparent pl-11 pr-4 text-gray-900 dark:text-gray-100 placeholder:text-gray-400 focus:ring-0 sm:text-sm"
                placeholder="Search commands..."
                @keydown.down.prevent="nextCommand()"
                @keydown.up.prevent="prevCommand()"
                @keydown.enter.prevent="executeCommand()"
                @keydown.escape.prevent="open = false"
            >
        </div>

        <ul class="max-h-80 scroll-py-2 overflow-y-auto p-2" x-show="filteredCommands.length > 0">
            <template x-for="(command, index) in filteredCommands" :key="command.id">
                <li
                    class="group flex cursor-pointer select-none items-center rounded-md px-3 py-2"
                    :class="{ 'bg-indigo-600 text-white': selectedIndex === index, 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-[#13151f]': selectedIndex !== index }"
                    @click="selectedIndex = index; executeCommand()"
                    @mouseenter="selectedIndex = index"
                >
                    <div class="flex items-center gap-3 w-full">
                        <div x-html="command.icon" class="w-5 h-5 flex-shrink-0" :class="{ 'text-indigo-200': selectedIndex === index, 'text-gray-400 dark:text-gray-500 group-hover:text-indigo-500': selectedIndex !== index }"></div>
                        <span class="flex-auto truncate font-medium" x-text="command.name"></span>
                        <template x-if="command.badge">
                            <span class="ml-3 flex-none text-xs rounded-full px-2 py-0.5" 
                                :class="{ 'bg-indigo-500 text-white': selectedIndex === index, 'bg-gray-100 dark:bg-gray-800 text-gray-500 dark:text-gray-400': selectedIndex !== index }"
                                x-text="command.badge"></span>
                        </template>
                    </div>
                </li>
            </template>
        </ul>

        <div x-show="search !== '' && filteredCommands.length === 0" class="px-6 py-14 text-center text-sm sm:px-14">
            <svg class="mx-auto h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
            <p class="mt-4 font-semibold text-gray-900 dark:text-gray-100">No results found</p>
            <p class="mt-2 text-gray-500 dark:text-gray-400">We couldn't find anything with that term. Please try again.</p>
        </div>
    </div>
</div>

<script>
function commandPalette() {
    return {
        open: false,
        search: '',
        selectedIndex: 0,
        commands: [
            { id: 'search', name: 'Search Everything', action: () => window.location.href = '{{ route("search") }}', icon: '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>' },
            { id: 'new-project', name: 'New Project', action: () => window.location.href = '{{ route("projects.create") }}', icon: '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/></svg>' },
            { id: 'new-problem', name: 'New Problem', action: () => window.location.href = '{{ route("problems.create") }}', icon: '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>' },
            { id: 'new-snippet', name: 'New Snippet', action: () => window.location.href = '{{ route("snippets.create") }}', icon: '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/></svg>' },
            { id: 'new-knowledge', name: 'New Knowledge', action: () => window.location.href = '{{ route("knowledge.create") }}', icon: '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>' },
            { id: 'go-dashboard', name: 'Go Dashboard', action: () => window.location.href = '{{ route("dashboard") }}', icon: '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>' },
            { id: 'go-projects', name: 'Go Projects', action: () => window.location.href = '{{ route("projects.index") }}', icon: '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/></svg>' },
            { id: 'go-problems', name: 'Go Problems', action: () => window.location.href = '{{ route("problems.index") }}', icon: '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>' },
            { id: 'go-snippets', name: 'Go Snippets', action: () => window.location.href = '{{ route("snippets.index") }}', icon: '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/></svg>' },
            { id: 'go-knowledge', name: 'Go Knowledge', action: () => window.location.href = '{{ route("knowledge.index") }}', icon: '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>' },
            { id: 'lab-api', name: 'Open API Lab', badge: 'Coming soon', action: () => alert('API Lab is coming soon'), icon: '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>' },
            { id: 'lab-mvel', name: 'Open MVEL Lab', badge: 'Coming soon', action: () => alert('MVEL Lab is coming soon'), icon: '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/></svg>' },
            { id: 'lab-api', name: 'Open API Lab', action: () => window.location.href = '{{ route("api-lab.index") }}', icon: '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>' },
            { id: 'lab-mvel', name: 'Open MVEL Lab', action: () => window.location.href = '{{ route("mvel-lab.index") }}', icon: '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/></svg>' },
        ],
        get filteredCommands() {
            if (this.search === '') return this.commands;
            const term = this.search.toLowerCase();
            return this.commands.filter(c => c.name.toLowerCase().includes(term));
        },
        nextCommand() {
            if (this.selectedIndex < this.filteredCommands.length - 1) {
                this.selectedIndex++;
                this.scrollToSelected();
            }
        },
        prevCommand() {
            if (this.selectedIndex > 0) {
                this.selectedIndex--;
                this.scrollToSelected();
            }
        },
        executeCommand() {
            const command = this.filteredCommands[this.selectedIndex];
            if (command) {
                this.open = false;
                command.action();
            }
        },
        scrollToSelected() {
            // Very simple auto-scroll implementation
            this.$nextTick(() => {
                const el = this.$el.querySelector('ul');
                if (el) {
                    const active = el.children[this.selectedIndex];
                    if (active) {
                        const top = active.offsetTop;
                        const bottom = top + active.offsetHeight;
                        if (bottom > el.scrollTop + el.offsetHeight) {
                            el.scrollTop = bottom - el.offsetHeight;
                        } else if (top < el.scrollTop) {
                            el.scrollTop = top;
                        }
                    }
                }
            });
        },
        init() {
            this.$watch('open', value => {
                if (value) {
                    this.search = '';
                    this.selectedIndex = 0;
                    this.$nextTick(() => this.$refs.searchInput.focus());
                }
            });
            this.$watch('search', () => {
                this.selectedIndex = 0;
            });
        }
    }
}
</script>
