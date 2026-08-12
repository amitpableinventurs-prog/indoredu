<x-guest-layout>
    <form method="POST" action="{{ route('register') }}" x-data="{ role: '{{ old('role', $role ?? 'student') }}' }">
        @csrf

        <!-- Role selection -->
        <div>
            <x-input-label :value="__('I am registering as a...')" />
            <div class="grid grid-cols-2 gap-3 mt-1">
                <label class="relative flex cursor-pointer rounded-lg border p-3 text-sm focus:outline-none"
                       :class="role === 'student' ? 'border-indigo-600 ring-1 ring-indigo-600 bg-indigo-50 dark:bg-indigo-950/40' : 'border-gray-300 dark:border-gray-700'">
                    <input type="radio" name="role" value="student" x-model="role" class="sr-only">
                    <span class="flex flex-1 flex-col">
                        <span class="font-medium text-gray-900 dark:text-gray-100">{{ __('Student') }}</span>
                        <span class="text-gray-500 dark:text-gray-400 text-xs">{{ __('Find tutors & book sessions') }}</span>
                    </span>
                </label>
                <label class="relative flex cursor-pointer rounded-lg border p-3 text-sm focus:outline-none"
                       :class="role === 'tutor' ? 'border-indigo-600 ring-1 ring-indigo-600 bg-indigo-50 dark:bg-indigo-950/40' : 'border-gray-300 dark:border-gray-700'">
                    <input type="radio" name="role" value="tutor" x-model="role" class="sr-only">
                    <span class="flex flex-1 flex-col">
                        <span class="font-medium text-gray-900 dark:text-gray-100">{{ __('Tutor') }}</span>
                        <span class="text-gray-500 dark:text-gray-400 text-xs">{{ __('Teach & earn on your schedule') }}</span>
                    </span>
                </label>
            </div>
            <x-input-error :messages="$errors->get('role')" class="mt-2" />
        </div>

        <!-- Name -->
        <div class="mt-4">
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />

            <x-text-input id="password_confirmation" class="block mt-1 w-full"
                            type="password"
                            name="password_confirmation" required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <a class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800" href="{{ route('login') }}">
                {{ __('Already registered?') }}
            </a>

            <x-primary-button class="ms-4">
                {{ __('Register') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
