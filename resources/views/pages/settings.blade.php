<x-app-layout>
    <div class="space-y-6">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100 tracking-tight">Settings</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Manage your workspace preferences.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="md:col-span-1">
                <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Profile</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Your personal account information.</p>
            </div>
            <div class="md:col-span-2">
                <x-card :padding="false">
                    <div class="p-6 space-y-4">
                        <div class="flex items-center gap-4">
                            <div class="w-16 h-16 rounded-lg bg-indigo-50 dark:bg-indigo-500/10 flex items-center justify-center text-indigo-700 dark:text-indigo-400 text-xl font-medium border border-indigo-100 dark:border-indigo-500/20 uppercase">
                                {{ substr(auth()->user()->name, 0, 1) }}
                            </div>
                            <div>
                                <h3 class="text-lg font-medium text-gray-900 dark:text-white">{{ auth()->user()->name }}</h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400">{{ auth()->user()->email }}</p>
                            </div>
                        </div>
                    </div>
                </x-card>
            </div>
        </div>

        <div class="hidden sm:block">
            <div class="border-t border-gray-200 dark:border-gray-800"></div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="md:col-span-1">
                <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Appearance</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Customize the look and feel of your workspace.</p>
            </div>
            <div class="md:col-span-2">
                <x-card :padding="false">
                    <div class="p-6 flex items-center justify-between" x-data>
                        <div>
                            <h4 class="text-sm font-medium text-gray-900 dark:text-gray-100">Dark Mode</h4>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Toggle between dark and light themes.</p>
                        </div>
                        <button
                            @click="darkMode = !darkMode"
                            :class="darkMode ? 'bg-indigo-600' : 'bg-gray-200 dark:bg-gray-800'"
                            class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-[#161925]"
                        >
                            <span :class="darkMode ? 'translate-x-6' : 'translate-x-1'" class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform"></span>
                        </button>
                    </div>
                </x-card>
            </div>
        </div>
    </div>
</x-app-layout>
