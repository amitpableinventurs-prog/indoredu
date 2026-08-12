<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ isset($title) ? $title.' — '.config('app.name') : config('app.name', 'IndorEdu').' — Find your perfect tutor' }}</title>

        <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32.png') }}">
        <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16.png') }}">
        <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('favicon-180.png') }}">

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @stack('scripts')
    </head>
    <body class="font-sans antialiased bg-gray-50 dark:bg-gray-900">
        <div class="min-h-screen flex flex-col">
            <nav x-data="{ open: false }" class="bg-white dark:bg-gray-800 border-b border-gray-100 dark:border-gray-700">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between h-16">
                        <div class="flex items-center gap-8">
                            <a href="{{ route('home') }}" class="flex items-center gap-2">
                                <x-application-logo class="h-9 w-9" />
                                <span class="font-bold text-lg text-gray-800 dark:text-gray-100">IndorEdu</span>
                            </a>
                            <div class="hidden sm:flex sm:space-x-6">
                                <x-nav-link :href="route('tutors.index')" :active="request()->routeIs('tutors.*')">{{ __('Find Tutors') }}</x-nav-link>
                                <x-nav-link :href="route('courses.index')" :active="request()->routeIs('courses.*')">{{ __('Courses') }}</x-nav-link>
                            </div>
                        </div>

                        <div class="hidden sm:flex sm:items-center gap-3">
                            @auth
                                <a href="{{ route('dashboard') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500 transition">
                                    {{ __('Dashboard') }}
                                </a>
                            @else
                                <a href="{{ route('login') }}" class="text-sm font-medium text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-gray-100">{{ __('Log in') }}</a>
                                <a href="{{ route('register', ['role' => 'tutor']) }}" class="text-sm font-medium text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-gray-100">{{ __('Become a tutor') }}</a>
                                <a href="{{ route('register') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500 transition">
                                    {{ __('Sign up') }}
                                </a>
                            @endauth
                        </div>

                        <div class="-me-2 flex items-center sm:hidden">
                            <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-900 focus:outline-none transition">
                                <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                    <path :class="{'hidden': open, 'inline-flex': ! open }" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                                    <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden border-t border-gray-100 dark:border-gray-700">
                    <div class="pt-2 pb-3 space-y-1">
                        <x-responsive-nav-link :href="route('tutors.index')">{{ __('Find Tutors') }}</x-responsive-nav-link>
                        <x-responsive-nav-link :href="route('courses.index')">{{ __('Courses') }}</x-responsive-nav-link>
                        @auth
                            <x-responsive-nav-link :href="route('dashboard')">{{ __('Dashboard') }}</x-responsive-nav-link>
                        @else
                            <x-responsive-nav-link :href="route('login')">{{ __('Log in') }}</x-responsive-nav-link>
                            <x-responsive-nav-link :href="route('register')">{{ __('Sign up') }}</x-responsive-nav-link>
                        @endauth
                    </div>
                </div>
            </nav>

            <x-flash-messages />

            <main class="flex-1">
                {{ $slot }}
            </main>

            <footer class="bg-white dark:bg-gray-800 border-t border-gray-100 dark:border-gray-700 mt-16">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 flex flex-col sm:flex-row justify-between items-center gap-4 text-sm text-gray-500 dark:text-gray-400">
                    <div class="flex items-center gap-2">
                        <x-application-logo class="h-6 w-6" />
                        <span>&copy; {{ date('Y') }} IndorEdu. All rights reserved.</span>
                    </div>
                    <div class="flex gap-6">
                        <a href="{{ route('tutors.index') }}" class="hover:text-gray-700 dark:hover:text-gray-200">Find Tutors</a>
                        <a href="{{ route('register', ['role' => 'tutor']) }}" class="hover:text-gray-700 dark:hover:text-gray-200">Become a Tutor</a>
                        <a href="{{ route('courses.index') }}" class="hover:text-gray-700 dark:hover:text-gray-200">Courses</a>
                    </div>
                </div>
            </footer>
        </div>
    </body>
</html>
