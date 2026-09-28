<x-guest-layout>
    <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 tracking-tight mb-2 text-center">Reset your password</h2>
    <p class="text-sm text-center text-gray-500 dark:text-gray-400 mb-6">Enter your email address and we'll send you a link to reset your password.</p>

    @if (session('status'))
        <x-alert type="success" class="mb-4">{{ session('status') }}</x-alert>
    @endif

    <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
        @csrf
        <x-input label="Email address" type="email" name="email" :value="old('email')" required autofocus />
        
        <div class="pt-2">
            <x-button type="submit" variant="primary" class="w-full">Send reset link</x-button>
        </div>
    </form>

    <p class="mt-6 text-center text-sm text-gray-600 dark:text-gray-400">
        <a href="{{ route('login') }}" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-500 transition-colors font-medium">Back to sign in</a>
    </p>
</x-guest-layout>
