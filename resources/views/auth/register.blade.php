<x-guest-layout>
    <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 tracking-tight mb-6 text-center">Create your account</h2>

    <form method="POST" action="{{ route('register') }}" class="space-y-4">
        @csrf

        <x-input label="Name" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
        <x-input label="Email address" type="email" name="email" :value="old('email')" required autocomplete="username" />
        <x-input label="Password" type="password" name="password" required autocomplete="new-password" />
        <x-input label="Confirm Password" type="password" name="password_confirmation" required autocomplete="new-password" />

        <div class="pt-2">
            <x-button type="submit" variant="primary" class="w-full">Create account</x-button>
        </div>
    </form>

    <p class="mt-6 text-center text-sm text-gray-600 dark:text-gray-400">
        Already have an account?
        <a href="{{ route('login') }}" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-500 transition-colors font-medium">Sign in</a>
    </p>
</x-guest-layout>
