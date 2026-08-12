<nav x-data="{ open: false }" class="bg-white dark:bg-gray-800 border-b border-gray-100 dark:border-gray-700">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <div class="shrink-0 flex items-center gap-2">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-2">
                        <x-application-logo class="h-9 w-9" />
                        <span class="font-bold text-lg text-gray-800 dark:text-gray-100">IndorEdu</span>
                    </a>
                </div>

                <div class="hidden space-x-6 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">{{ __('Dashboard') }}</x-nav-link>

                    @auth
                        @if (auth()->user()->isStudent())
                            <x-nav-link :href="route('tutors.index')" :active="request()->routeIs('tutors.*')">{{ __('Find Tutors') }}</x-nav-link>
                            <x-nav-link :href="route('courses.index')" :active="request()->routeIs('courses.index')">{{ __('Courses') }}</x-nav-link>
                            <x-nav-link :href="route('student.bookings.index')" :active="request()->routeIs('student.bookings.*')">{{ __('My Bookings') }}</x-nav-link>
                            <x-nav-link :href="route('student.reports.index')" :active="request()->routeIs('student.reports.*')">{{ __('Progress') }}</x-nav-link>
                        @elseif (auth()->user()->isTutor())
                            <x-nav-link :href="route('tutor.bookings.index')" :active="request()->routeIs('tutor.bookings.*')">{{ __('Bookings') }}</x-nav-link>
                            <x-nav-link :href="route('tutor.courses.index')" :active="request()->routeIs('tutor.courses.*')">{{ __('Courses') }}</x-nav-link>
                            <x-nav-link :href="route('tutor.availability.index')" :active="request()->routeIs('tutor.availability.*')">{{ __('Schedule') }}</x-nav-link>
                            <x-nav-link :href="route('tutor.students.index')" :active="request()->routeIs('tutor.students.*')">{{ __('Students') }}</x-nav-link>
                            <x-nav-link :href="route('tutor.payouts.index')" :active="request()->routeIs('tutor.payouts.*')">{{ __('Earnings') }}</x-nav-link>
                        @elseif (auth()->user()->isAdmin())
                            <x-nav-link :href="route('admin.users.index')" :active="request()->routeIs('admin.users.*')">{{ __('Users') }}</x-nav-link>
                            <x-nav-link :href="route('admin.tutor-applications.index')" :active="request()->routeIs('admin.tutor-applications.*')">{{ __('Tutor Applications') }}</x-nav-link>
                            <x-nav-link :href="route('admin.courses.index')" :active="request()->routeIs('admin.courses.*')">{{ __('Courses') }}</x-nav-link>
                            <x-nav-link :href="route('admin.reports.index')" :active="request()->routeIs('admin.reports.*') || request()->routeIs('admin.reviews.*')">{{ __('Moderation') }}</x-nav-link>
                            <x-nav-link :href="route('admin.payments.index')" :active="request()->routeIs('admin.payments.*') || request()->routeIs('admin.payouts.*')">{{ __('Payments') }}</x-nav-link>
                        @endif
                    @endauth
                </div>
            </div>

            <div class="hidden sm:flex sm:items-center sm:ms-6 gap-1">
                @auth
                    <a href="{{ route('messages.index') }}" class="relative p-2 text-gray-500 dark:text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400 rounded-md" title="Messages">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8-1.06 0-2.076-.163-3.017-.463L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                        @php $unreadMsgs = auth()->user()->unreadMessagesCount(); @endphp
                        @if ($unreadMsgs > 0)
                            <span class="absolute -top-0.5 -right-0.5 flex h-4 w-4 items-center justify-center rounded-full bg-red-500 text-[10px] text-white">{{ $unreadMsgs > 9 ? '9+' : $unreadMsgs }}</span>
                        @endif
                    </a>
                    <a href="{{ route('notifications.index') }}" class="relative p-2 text-gray-500 dark:text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400 rounded-md" title="Notifications">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                        @php $unreadNotifs = auth()->user()->unreadNotifications()->count(); @endphp
                        @if ($unreadNotifs > 0)
                            <span class="absolute -top-0.5 -right-0.5 flex h-4 w-4 items-center justify-center rounded-full bg-red-500 text-[10px] text-white">{{ $unreadNotifs > 9 ? '9+' : $unreadNotifs }}</span>
                        @endif
                    </a>
                @endauth

                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none transition ease-in-out duration-150">
                            <div>{{ Auth::user()->name }}</div>
                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">{{ __('Account Settings') }}</x-dropdown-link>
                        @auth
                            @if (auth()->user()->isTutor())
                                <x-dropdown-link :href="route('tutor.profile.edit')">{{ __('Public Profile') }}</x-dropdown-link>
                            @elseif (auth()->user()->isStudent())
                                <x-dropdown-link :href="route('student.profile.edit')">{{ __('Learner Profile') }}</x-dropdown-link>
                            @endif
                        @endauth
                        <x-dropdown-link :href="route('calendar.index')">{{ __('Calendar') }}</x-dropdown-link>
                        <x-dropdown-link :href="route('settings.notifications.edit')">{{ __('Notification Settings') }}</x-dropdown-link>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 dark:text-gray-500 hover:text-gray-500 dark:hover:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-900 focus:outline-none focus:bg-gray-100 dark:focus:bg-gray-900 focus:text-gray-500 dark:focus:text-gray-400 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">{{ __('Dashboard') }}</x-responsive-nav-link>
            @auth
                @if (auth()->user()->isStudent())
                    <x-responsive-nav-link :href="route('tutors.index')">{{ __('Find Tutors') }}</x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('courses.index')">{{ __('Courses') }}</x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('student.bookings.index')">{{ __('My Bookings') }}</x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('student.reports.index')">{{ __('Progress') }}</x-responsive-nav-link>
                @elseif (auth()->user()->isTutor())
                    <x-responsive-nav-link :href="route('tutor.bookings.index')">{{ __('Bookings') }}</x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('tutor.courses.index')">{{ __('Courses') }}</x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('tutor.availability.index')">{{ __('Schedule') }}</x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('tutor.students.index')">{{ __('Students') }}</x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('tutor.payouts.index')">{{ __('Earnings') }}</x-responsive-nav-link>
                @elseif (auth()->user()->isAdmin())
                    <x-responsive-nav-link :href="route('admin.users.index')">{{ __('Users') }}</x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('admin.tutor-applications.index')">{{ __('Tutor Applications') }}</x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('admin.courses.index')">{{ __('Courses') }}</x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('admin.reports.index')">{{ __('Moderation') }}</x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('admin.payments.index')">{{ __('Payments') }}</x-responsive-nav-link>
                @endif
                <x-responsive-nav-link :href="route('messages.index')">{{ __('Messages') }}</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('notifications.index')">{{ __('Notifications') }}</x-responsive-nav-link>
            @endauth
        </div>

        <div class="pt-4 pb-1 border-t border-gray-200 dark:border-gray-600">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800 dark:text-gray-200">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">{{ __('Account Settings') }}</x-responsive-nav-link>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
