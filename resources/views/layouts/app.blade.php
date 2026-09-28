<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark" x-data="{ darkMode: localStorage.getItem('darkMode') !== 'false' }" x-init="$watch('darkMode', val => { localStorage.setItem('darkMode', val); document.documentElement.classList.toggle('dark', val) }); document.documentElement.classList.toggle('dark', darkMode)" :class="{ 'dark': darkMode }">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'HMNR DevOS') }}</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500;700&display=swap" rel="stylesheet">
    
    <!-- Syntax Highlighting -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/styles/atom-one-dark.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/highlight.min.js"></script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="min-h-screen bg-gray-50 dark:bg-[#0f111a] text-gray-900 dark:text-gray-200 font-sans antialiased" x-data="{ sidebarOpen: false }">
    
    {{-- Mobile sidebar backdrop --}}
    <div x-show="sidebarOpen" x-transition.opacity class="fixed inset-0 z-40 bg-gray-900/80 lg:hidden" @click="sidebarOpen = false"></div>

    {{-- Sidebar --}}
    <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'" class="fixed inset-y-0 left-0 z-50 w-64 bg-white dark:bg-[#161925] border-r border-gray-200 dark:border-gray-800 transition-transform duration-200 lg:translate-x-0 flex flex-col">
        {{-- Logo --}}
        <div class="h-16 flex items-center px-6 border-b border-gray-200 dark:border-gray-800 shrink-0">
            <a href="/" class="flex items-center gap-2">
                <span class="text-xl font-bold tracking-tight text-gray-900 dark:text-white">HMNR <span class="font-light text-indigo-500">DEVOS</span></span>
            </a>
        </div>

        {{-- Navigation --}}
        <nav class="flex-1 overflow-y-auto p-4 space-y-6">
            
            {{-- Main Navigation --}}
            <div>
                <a href="{{ route('dashboard') }}"
                   class="flex items-center gap-3 px-3 py-2 rounded-md text-sm font-medium transition-colors
                          {{ request()->routeIs('dashboard') 
                              ? 'bg-indigo-50/50 dark:bg-indigo-500/10 text-indigo-700 dark:text-indigo-400' 
                              : 'text-gray-700 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800/50 hover:text-gray-900 dark:hover:text-gray-200' }}"
                   @click="sidebarOpen = false"
                >
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                    Dashboard
                </a>
            </div>

            {{-- Workspace --}}
            <div>
                <h3 class="px-3 mb-2 text-xs font-semibold text-gray-500 uppercase tracking-wider">Workspace</h3>
                <div class="space-y-1">
                    @php
                        $workspaceItems = [
                            ['route' => 'projects.index', 'label' => 'Projects', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/>'],
                            ['route' => 'problems.index', 'label' => 'Problems', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>'],
                            ['route' => 'snippets.index', 'label' => 'Snippets', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/>'],
                            ['route' => 'knowledge.index', 'label' => 'Knowledge', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>'],
                        ];
                    @endphp
                    @foreach($workspaceItems as $item)
                        <a href="{{ route($item['route']) }}"
                           class="flex items-center gap-3 px-3 py-2 rounded-md text-sm font-medium transition-colors
                                  {{ request()->routeIs($item['route']) || request()->routeIs(str_replace('.index', '.*', $item['route']))
                                      ? 'bg-indigo-50/50 dark:bg-indigo-500/10 text-indigo-700 dark:text-indigo-400' 
                                      : 'text-gray-700 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800/50 hover:text-gray-900 dark:hover:text-gray-200' }}"
                           @click="sidebarOpen = false"
                        >
                            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">{!! $item['icon'] !!}</svg>
                            {{ $item['label'] }}
                        </a>
                    @endforeach
                </div>
            </div>

            {{-- Labs --}}
            <div>
                <h3 class="px-3 mb-2 text-xs font-semibold text-gray-500 uppercase tracking-wider">Labs</h3>
                <div class="space-y-1">
                    <a href="{{ route('api-lab.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-md text-sm font-medium transition-colors {{ request()->routeIs('api-lab.*') ? 'bg-indigo-50/50 dark:bg-indigo-500/10 text-indigo-700 dark:text-indigo-400' : 'text-gray-700 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800/50 hover:text-gray-900 dark:hover:text-gray-200' }}">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>
                        API Lab
                    </a>
                    <a href="{{ route('mvel-lab.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-md text-sm font-medium transition-colors {{ request()->routeIs('mvel-lab.*') ? 'bg-indigo-50/50 dark:bg-indigo-500/10 text-indigo-700 dark:text-indigo-400' : 'text-gray-700 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800/50 hover:text-gray-900 dark:hover:text-gray-200' }}">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/></svg>
                        MVEL Lab
                    </a>
                </div>
            </div>
        </nav>

        {{-- Bottom section --}}
        <div class="p-4 border-t border-gray-200 dark:border-gray-800 space-y-1 shrink-0">
            <a href="{{ route('settings.index') }}"
               class="flex items-center gap-3 px-3 py-2 rounded-md text-sm font-medium transition-colors
                      {{ request()->routeIs('settings.*') || request()->routeIs('settings.index')
                          ? 'bg-indigo-50/50 dark:bg-indigo-500/10 text-indigo-700 dark:text-indigo-400' 
                          : 'text-gray-700 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800/50 hover:text-gray-900 dark:hover:text-gray-200' }}"
               @click="sidebarOpen = false"
            >
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                Settings
            </a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="flex items-center gap-3 px-3 py-2 rounded-md text-sm font-medium text-gray-700 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800/50 hover:text-gray-900 dark:hover:text-gray-200 transition-colors w-full">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    Logout
                </button>
            </form>
        </div>
    </aside>

    {{-- Main content --}}
    <div class="lg:pl-64 flex flex-col min-h-screen">
        {{-- Top bar --}}
        <header class="sticky top-0 z-30 h-16 bg-white/80 dark:bg-[#161925]/80 backdrop-blur border-b border-gray-200 dark:border-gray-800 flex items-center justify-between px-4 lg:px-8">
            {{-- Mobile menu button --}}
            <div class="flex items-center gap-4">
                <button @click="sidebarOpen = true" class="lg:hidden p-2 -ml-2 rounded-md text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
            </div>

            {{-- Global Search --}}
            <div class="flex-1 max-w-xl mx-4 lg:mx-0 lg:flex lg:justify-center">
                <div class="relative w-full group cursor-text" @click="$dispatch('open-command-palette')">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <svg class="w-4 h-4 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>
                    <div class="block w-full pl-10 pr-12 py-2 text-sm bg-gray-100/50 dark:bg-[#0f111a] border-transparent rounded-md text-gray-500 dark:text-gray-400 transition-colors">
                        Search or jump to...
                    </div>
                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                        <span class="text-xs font-mono text-gray-400 dark:text-gray-500" x-text="navigator.platform.includes('Mac') ? '⌘K' : 'Ctrl K'"></span>
                    </div>
                </div>
            </div>

            {{-- Right section --}}
            <div class="flex items-center gap-3">
                {{-- Dark mode toggle --}}
                <button @click="darkMode = !darkMode" class="p-2 rounded-md text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                    <svg x-show="darkMode" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    <svg x-show="!darkMode" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
                </button>

                {{-- User dropdown --}}
                <x-dropdown align="right" width="48">
                    <x-slot:trigger>
                        <button class="flex items-center gap-2 p-1.5 rounded-md hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                            <div class="w-7 h-7 rounded-md bg-indigo-500/10 dark:bg-indigo-500/20 text-indigo-700 dark:text-indigo-400 flex items-center justify-center text-xs font-semibold uppercase border border-indigo-100 dark:border-indigo-500/30">
                                {{ substr(auth()->user()->name, 0, 1) }}
                            </div>
                        </button>
                    </x-slot:trigger>

                    <div class="px-4 py-2 border-b border-gray-100 dark:border-gray-800">
                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ auth()->user()->name }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 truncate">{{ auth()->user()->email }}</p>
                    </div>
                    <a href="{{ route('settings.index') }}" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800">Settings</a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-b-md">Logout</button>
                    </form>
                </x-dropdown>
            </div>
        </header>

        {{-- Page content --}}
        <main class="flex-1 p-4 lg:p-8">
            <div class="max-w-6xl mx-auto">
                {{ $slot ?? '' }}
                @yield('content')
            </div>
        </main>
    </div>

    {{-- Command Palette --}}
    <x-command-palette />

    {{-- Toast container --}}
    <x-toast />

    @livewireScripts
</body>
</html>