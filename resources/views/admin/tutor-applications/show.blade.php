<x-app-layout :title="$tutorProfile->user->name.' — Application'">
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Tutor Application &middot; {{ $tutorProfile->user->name }}
            </h2>
            <a href="{{ route('admin.tutor-applications.index') }}" class="text-sm text-gray-500 hover:text-gray-700 dark:hover:text-gray-300">&larr; Back to applications</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <div class="flex flex-wrap items-start justify-between gap-3">
                    <div>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100">{{ $tutorProfile->user->name }}</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ $tutorProfile->user->email }}</p>
                        <p class="text-sm text-gray-600 dark:text-gray-300 mt-1">{{ $tutorProfile->headline ?: 'No headline provided' }}</p>
                    </div>
                    <x-status-badge :status="$tutorProfile->status" />
                </div>

                @if ($tutorProfile->status === 'rejected' && $tutorProfile->rejection_reason)
                    <div class="mt-4 p-3 rounded-md bg-red-50 dark:bg-red-900/20 text-sm text-red-700 dark:text-red-300">
                        <span class="font-medium">Rejection reason:</span> {{ $tutorProfile->rejection_reason }}
                    </div>
                @endif

                <div class="mt-4 grid sm:grid-cols-3 gap-4 text-sm">
                    <div>
                        <p class="text-gray-400 dark:text-gray-500">Hourly rate</p>
                        <p class="text-gray-800 dark:text-gray-200">₹{{ number_format($tutorProfile->hourly_rate, 2) }}</p>
                    </div>
                    <div>
                        <p class="text-gray-400 dark:text-gray-500">Experience</p>
                        <p class="text-gray-800 dark:text-gray-200">{{ $tutorProfile->experience_years }} years</p>
                    </div>
                    <div>
                        <p class="text-gray-400 dark:text-gray-500">Languages</p>
                        <p class="text-gray-800 dark:text-gray-200">{{ $tutorProfile->languages ? implode(', ', $tutorProfile->languages) : '—' }}</p>
                    </div>
                </div>
            </div>

            <x-card title="Bio & education">
                <div class="space-y-4 text-sm">
                    <div>
                        <p class="text-gray-400 dark:text-gray-500 mb-1">Bio</p>
                        <p class="text-gray-700 dark:text-gray-300 whitespace-pre-line">{{ $tutorProfile->bio ?: '—' }}</p>
                    </div>
                    <div>
                        <p class="text-gray-400 dark:text-gray-500 mb-1">Education</p>
                        <p class="text-gray-700 dark:text-gray-300 whitespace-pre-line">{{ $tutorProfile->education ?: '—' }}</p>
                    </div>
                </div>
            </x-card>

            <x-card title="Subjects">
                @if ($tutorProfile->subjects->isEmpty())
                    <p class="text-sm text-gray-500 dark:text-gray-400">No subjects added.</p>
                @else
                    <div class="flex flex-wrap gap-2">
                        @foreach ($tutorProfile->subjects as $subject)
                            <span class="px-2.5 py-0.5 rounded-full text-xs bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300">
                                {{ $subject->name }} <span class="text-gray-400 dark:text-gray-500">&middot; {{ ucfirst($subject->pivot->level) }}</span>
                            </span>
                        @endforeach
                    </div>
                @endif
            </x-card>

            <x-card title="Identity verification">
                @if ($tutorProfile->identity_document)
                    <a href="{{ route('files.identity', $tutorProfile) }}" target="_blank" rel="noopener" class="text-sm font-medium text-indigo-600 dark:text-indigo-400 hover:underline">View identity document &rarr;</a>
                @else
                    <p class="text-sm text-gray-500 dark:text-gray-400">No identity document uploaded.</p>
                @endif
            </x-card>

            <x-card title="Certificates" :subtitle="$tutorProfile->certificates->count().' submitted'">
                @if ($tutorProfile->certificates->isEmpty())
                    <p class="text-sm text-gray-500 dark:text-gray-400">No certificates submitted.</p>
                @else
                    <div class="space-y-3">
                        @foreach ($tutorProfile->certificates as $certificate)
                            <div class="flex flex-wrap items-center justify-between gap-3 py-3 {{ !$loop->last ? 'border-b border-gray-100 dark:border-gray-700' : '' }}">
                                <div class="min-w-0">
                                    <p class="font-medium text-gray-900 dark:text-gray-100">{{ $certificate->title }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $certificate->issuer }}</p>
                                    <a href="{{ route('files.certificate', $certificate) }}" target="_blank" rel="noopener" class="text-xs text-indigo-600 dark:text-indigo-400 hover:underline">View file</a>
                                </div>
                                <div class="flex items-center gap-3">
                                    <x-status-badge :status="$certificate->status" />
                                    <form action="{{ route('admin.certificates.verify', $certificate) }}" method="POST" class="flex items-center gap-2">
                                        @csrf
                                        @method('PUT')
                                        <select name="status" class="rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-xs py-1">
                                            <option value="verified" @selected($certificate->status === 'verified')>Verified</option>
                                            <option value="rejected" @selected($certificate->status === 'rejected')>Rejected</option>
                                        </select>
                                        <button type="submit" class="text-xs font-medium text-indigo-600 dark:text-indigo-400 hover:underline">Save</button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </x-card>

            <x-card title="Application decision">
                <div class="grid sm:grid-cols-2 gap-6">
                    <form action="{{ route('admin.tutor-applications.approve', $tutorProfile) }}" method="POST"
                          onsubmit="return confirm('Approve {{ $tutorProfile->user->name }} as a tutor?');">
                        @csrf
                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-3">Approve this application and grant tutor access.</p>
                        <x-primary-button type="submit">Approve application</x-primary-button>
                    </form>

                    <form action="{{ route('admin.tutor-applications.reject', $tutorProfile) }}" method="POST"
                          onsubmit="return confirm('Reject this tutor application?');">
                        @csrf
                        <x-input-label for="rejection_reason" value="Rejection reason" />
                        <textarea id="rejection_reason" name="rejection_reason" rows="2" required maxlength="500"
                                  class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                                  placeholder="Explain why this application is being rejected"></textarea>
                        <x-input-error class="mt-2" :messages="$errors->get('rejection_reason')" />
                        <x-danger-button type="submit" class="mt-3">Reject application</x-danger-button>
                    </form>
                </div>
            </x-card>
        </div>
    </div>
</x-app-layout>
