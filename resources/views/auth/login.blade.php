<x-guest-layout>
    <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 tracking-tight mb-6 text-center">Sign in to your account</h2>

    @if (session('status'))
        <x-alert type="success" class="mb-4">{{ session('status') }}</x-alert>
    @endif

    <form method="POST" action="{{ route('login') }}" class="space-y-4" x-data="{ submitting: false }" @submit="submitting = true">
        @csrf

        <x-input
            label="Email address"
            type="email"
            name="email"
            :value="old('email')"
            required
            autofocus
            autocomplete="username"
            x-bind:readonly="submitting"
        />

        <x-input
            label="Password"
            type="password"
            name="password"
            required
            autocomplete="current-password"
            x-bind:readonly="submitting"
        />

        <div class="flex items-center justify-between mt-2">
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" name="remember" class="rounded border-gray-300 dark:border-gray-700 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:bg-gray-800 dark:focus:ring-indigo-500">
                <span class="text-sm text-gray-600 dark:text-gray-400">Remember me</span>
            </label>
            <a href="{{ route('password.request') }}" class="text-sm text-indigo-600 dark:text-indigo-400 hover:text-indigo-500 transition-colors">Forgot password?</a>
        </div>

        <div class="pt-2">
            <x-button type="submit" variant="primary" class="w-full" x-bind:disabled="submitting">
                <span x-show="!submitting">Sign in</span>
                <span x-show="submitting" x-cloak class="flex items-center gap-2">
                    <svg class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                    Signing in...
                </span>
            </x-button>
        </div>
    </form>

    <p class="mt-6 text-center text-sm text-gray-600 dark:text-gray-400">
        Don't have an account?
        <a href="{{ route('register') }}" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-500 transition-colors font-medium">Create one</a>
    </p>
</x-guest-layout>
