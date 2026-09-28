<x-guest-layout>
    <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 tracking-tight mb-6 text-center">Set new password</h2>

    <form method="POST" action="{{ route('password.update') }}" class="space-y-4">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">

        <x-input label="Email address" type="email" name="email" :value="old('email', $email)" required autocomplete="username" />
        <x-input label="New Password" type="password" name="password" required autocomplete="new-password" />
        <x-input label="Confirm Password" type="password" name="password_confirmation" required autocomplete="new-password" />

        <div class="pt-2">
            <x-button type="submit" variant="primary" class="w-full">Reset password</x-button>
        </div>
    </form>
</x-guest-layout>
