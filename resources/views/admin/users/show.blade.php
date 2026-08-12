<x-app-layout :title="$user->name">
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ $user->name }}
            </h2>
            <a href="{{ route('admin.users.index') }}" class="text-sm text-gray-500 hover:text-gray-700 dark:hover:text-gray-300">&larr; Back to users</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Profile summary -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <div class="flex items-start gap-5">
                    <div class="w-16 h-16 rounded-full bg-indigo-100 dark:bg-indigo-900/40 text-indigo-700 dark:text-indigo-300 flex items-center justify-center font-semibold text-xl shrink-0 overflow-hidden">
                        @if ($user->avatar)
                            <img src="{{ asset('storage/'.$user->avatar) }}" alt="{{ $user->name }}" class="w-full h-full object-cover">
                        @else
                            {{ $user->initials() }}
                        @endif
                    </div>
                    <div class="min-w-0 flex-1">
                        <div class="flex flex-wrap items-center gap-2">
                            <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100">{{ $user->name }}</h3>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium capitalize bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300">{{ $user->role }}</span>
                            <x-status-badge :status="$user->status" />
                        </div>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ $user->email }}</p>
                        <div class="mt-3 grid sm:grid-cols-2 gap-x-6 gap-y-1 text-sm text-gray-600 dark:text-gray-300">
                            <p><span class="text-gray-400 dark:text-gray-500">Phone:</span> {{ $user->phone ?: '—' }}</p>
                            <p><span class="text-gray-400 dark:text-gray-500">Location:</span> {{ collect([$user->city, $user->country])->filter()->join(', ') ?: '—' }}</p>
                            <p><span class="text-gray-400 dark:text-gray-500">Timezone:</span> {{ $user->timezone ?: '—' }}</p>
                            <p><span class="text-gray-400 dark:text-gray-500">Joined:</span> {{ $user->created_at->format('D, M j, Y') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            @if ($user->tutorProfile)
                <x-card title="Tutor profile" subtitle="Public tutoring profile summary">
                    <div class="grid sm:grid-cols-2 gap-4 text-sm">
                        <div class="sm:col-span-2">
                            <p class="text-gray-400 dark:text-gray-500">Headline</p>
                            <p class="text-gray-800 dark:text-gray-200">{{ $user->tutorProfile->headline ?: '—' }}</p>
                        </div>
                        <div>
                            <p class="text-gray-400 dark:text-gray-500">Hourly rate</p>
                            <p class="text-gray-800 dark:text-gray-200">₹{{ number_format($user->tutorProfile->hourly_rate, 2) }}</p>
                        </div>
                        <div>
                            <p class="text-gray-400 dark:text-gray-500">Rating</p>
                            <x-star-rating :rating="$user->tutorProfile->rating_avg" :count="$user->tutorProfile->rating_count" size="w-3.5 h-3.5" />
                        </div>
                        <div>
                            <p class="text-gray-400 dark:text-gray-500">Application status</p>
                            <x-status-badge :status="$user->tutorProfile->status" />
                        </div>
                        <div>
                            <p class="text-gray-400 dark:text-gray-500">Sessions taught</p>
                            <p class="text-gray-800 dark:text-gray-200">{{ $user->tutorProfile->total_sessions }}</p>
                        </div>
                        <div class="sm:col-span-2">
                            <p class="text-gray-400 dark:text-gray-500 mb-1">Subjects</p>
                            @if ($user->tutorProfile->subjects->isEmpty())
                                <p class="text-gray-500 dark:text-gray-400">No subjects added.</p>
                            @else
                                <div class="flex flex-wrap gap-2">
                                    @foreach ($user->tutorProfile->subjects as $subject)
                                        <span class="px-2.5 py-0.5 rounded-full text-xs bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300">{{ $subject->name }}</span>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="mt-4">
                        <a href="{{ route('admin.tutor-applications.show', $user->tutorProfile) }}" class="text-sm font-medium text-indigo-600 dark:text-indigo-400 hover:underline">View full application &rarr;</a>
                    </div>
                </x-card>
            @endif

            @if ($user->studentProfile)
                <x-card title="Student profile" subtitle="Learner profile summary">
                    <div class="grid sm:grid-cols-2 gap-4 text-sm">
                        <div>
                            <p class="text-gray-400 dark:text-gray-500">Grade / level</p>
                            <p class="text-gray-800 dark:text-gray-200">{{ $user->studentProfile->grade_level ?: '—' }}</p>
                        </div>
                        <div class="sm:col-span-2">
                            <p class="text-gray-400 dark:text-gray-500">Learning goals</p>
                            <p class="text-gray-800 dark:text-gray-200 whitespace-pre-line">{{ $user->studentProfile->learning_goals ?: '—' }}</p>
                        </div>
                    </div>
                </x-card>
            @endif

            <!-- Status change -->
            <x-card title="Account status">
                @if ($user->role !== 'admin')
                    <form action="{{ route('admin.users.status', $user) }}" method="POST" class="space-y-4"
                          onsubmit="return confirm('Change this user\'s account status?');">
                        @csrf
                        @method('PUT')

                        <div>
                            <x-input-label for="status" value="New status" />
                            <select id="status" name="status" required class="mt-1 block w-full sm:w-64 rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                <option value="active" @selected($user->status === 'active')>Active</option>
                                <option value="suspended" @selected($user->status === 'suspended')>Suspended</option>
                                <option value="banned" @selected($user->status === 'banned')>Banned</option>
                            </select>
                            <x-input-error class="mt-2" :messages="$errors->get('status')" />
                        </div>

                        <div>
                            <x-input-label for="reason" value="Reason (optional)" />
                            <textarea id="reason" name="reason" rows="3" maxlength="500"
                                      class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                                      placeholder="Note for the audit log"></textarea>
                            <x-input-error class="mt-2" :messages="$errors->get('reason')" />
                        </div>

                        <x-danger-button type="submit">Update status</x-danger-button>
                    </form>
                @else
                    <p class="text-sm text-gray-500 dark:text-gray-400">Admin accounts cannot be modified here.</p>
                @endif
            </x-card>
        </div>
    </div>
</x-app-layout>
