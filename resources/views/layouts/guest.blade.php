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
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="min-h-screen bg-gray-50 dark:bg-[#0f111a] flex flex-col items-center justify-center p-4 font-sans text-gray-900 dark:text-gray-200 antialiased">
    <div class="w-full max-w-sm">
        <div class="text-center mb-8">
            <a href="/" class="inline-flex items-center gap-2">
                <span class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white">HMNR <span class="font-light text-indigo-500">DEVOS</span></span>
            </a>
            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Your code. Your problems. Your solutions.</p>
        </div>
        
        <x-card :padding="false" class="border-gray-200 dark:border-gray-800 shadow-none">
            <div class="p-6">
                {{ $slot }}
            </div>
        </x-card>
    </div>
    
    <x-toast />
    @livewireScripts
</body>
</html>
