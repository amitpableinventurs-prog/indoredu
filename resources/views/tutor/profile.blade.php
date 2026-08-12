<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Public Profile
        </h2>
    </x-slot>

    @php
        $commonLanguages = ['English', 'Spanish', 'French', 'Mandarin', 'German', 'Other'];
        $myLanguages = $profile->languages ?? [];
        $myPivots = $profile->subjects->keyBy('id');
    @endphp

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if ($profile->status !== 'approved')
                <div class="rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-4 flex items-center justify-between gap-3">
                    <p class="text-sm text-gray-600 dark:text-gray-300">Current application status:</p>
                    <x-status-badge :status="$profile->status" />
                </div>
            @endif

            <!-- Main profile form -->
            <x-card title="Profile details" subtitle="This information is shown to students on your public tutor page.">
                <form action="{{ route('tutor.profile.update') }}" method="POST" enctype="multipart/form-data" class="space-y-5" x-data="{ offersTrial: {{ $profile->offers_trial ? 'true' : 'false' }} }">
                    @csrf
                    @method('PUT')

                    <div>
                        <x-input-label for="headline" value="Headline" />
                        <x-text-input id="headline" name="headline" class="mt-1 block w-full" value="{{ old('headline', $profile->headline) }}" placeholder="e.g. Certified Math Tutor with 10+ years experience" />
                        <x-input-error :messages="$errors->get('headline')" class="mt-1" />
                    </div>

                    <div>
                        <x-input-label for="bio" value="Bio" />
                        <textarea id="bio" name="bio" rows="5" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">{{ old('bio', $profile->bio) }}</textarea>
                        <x-input-error :messages="$errors->get('bio')" class="mt-1" />
                    </div>

                    <div class="grid sm:grid-cols-2 gap-4">
                        <div>
                            <x-input-label for="hourly_rate" value="Hourly rate (₹)" />
                            <x-text-input id="hourly_rate" name="hourly_rate" type="number" step="0.01" min="1" max="1000" class="mt-1 block w-full" value="{{ old('hourly_rate', $profile->hourly_rate) }}" required />
                            <x-input-error :messages="$errors->get('hourly_rate')" class="mt-1" />
                        </div>
                        <div>
                            <x-input-label for="experience_years" value="Years of experience" />
                            <x-text-input id="experience_years" name="experience_years" type="number" min="0" max="60" class="mt-1 block w-full" value="{{ old('experience_years', $profile->experience_years) }}" required />
                            <x-input-error :messages="$errors->get('experience_years')" class="mt-1" />
                        </div>
                    </div>

                    <div>
                        <label class="inline-flex items-center gap-2 text-sm text-gray-600 dark:text-gray-300">
                            <input type="checkbox" name="offers_trial" value="1" x-model="offersTrial" @checked(old('offers_trial', $profile->offers_trial)) class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                            Offer a trial session
                        </label>
                        <div class="mt-2" x-show="offersTrial">
                            <x-input-label for="trial_price" value="Trial session price (₹)" />
                            <x-text-input id="trial_price" name="trial_price" type="number" step="0.01" min="0" max="1000" class="mt-1 block w-full sm:w-64" value="{{ old('trial_price', $profile->trial_price) }}" />
                            <x-input-error :messages="$errors->get('trial_price')" class="mt-1" />
                        </div>
                    </div>

                    <div>
                        <x-input-label for="education" value="Education & Qualifications" />
                        <textarea id="education" name="education" rows="3" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">{{ old('education', $profile->education) }}</textarea>
                        <x-input-error :messages="$errors->get('education')" class="mt-1" />
                    </div>

                    <div>
                        <x-input-label for="video_intro_url" value="Introduction video URL" />
                        <x-text-input id="video_intro_url" name="video_intro_url" type="url" class="mt-1 block w-full" value="{{ old('video_intro_url', $profile->video_intro_url) }}" placeholder="https://..." />
                        <x-input-error :messages="$errors->get('video_intro_url')" class="mt-1" />
                    </div>

                    <div>
                        <x-input-label value="Languages spoken" />
                        <div class="mt-1 flex flex-wrap gap-3">
                            @foreach ($commonLanguages as $language)
                                <label class="inline-flex items-center gap-2 text-sm text-gray-600 dark:text-gray-300">
                                    <input type="checkbox" name="languages[]" value="{{ $language }}" @checked(in_array($language, old('languages', $myLanguages))) class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                    {{ $language }}
                                </label>
                            @endforeach
                        </div>
                        <x-input-error :messages="$errors->get('languages')" class="mt-1" />
                    </div>

                    <div>
                        <x-input-label for="identity_document" value="Identity document" />
                        <input id="identity_document" name="identity_document" type="file" accept=".pdf,.jpg,.jpeg,.png" class="mt-1 block w-full text-sm text-gray-600 dark:text-gray-300 file:mr-3 file:py-2 file:px-3 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 dark:file:bg-indigo-900/40 file:text-indigo-700 dark:file:text-indigo-300 hover:file:bg-indigo-100">
                        <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">PDF, JPG or PNG, up to 5MB. Re-uploading will trigger a new review by our team.</p>
                        @if ($profile->identity_document)
                            <a href="{{ route('files.identity', $profile) }}" class="inline-block mt-1 text-xs text-indigo-600 dark:text-indigo-400 hover:underline">View current document</a>
                        @endif
                        <x-input-error :messages="$errors->get('identity_document')" class="mt-1" />
                    </div>

                    <div class="flex justify-end">
                        <x-primary-button type="submit">Save profile</x-primary-button>
                    </div>
                </form>
            </x-card>

            <!-- Subjects form -->
            <x-card title="Subjects you teach" subtitle="Select the subjects you offer and set your level and (optionally) a per-subject rate override.">
                <form action="{{ route('tutor.profile.subjects') }}" method="POST" class="space-y-6">
                    @csrf
                    @method('PUT')

                    @forelse ($subjects->groupBy('category.name') as $categoryName => $subjectsInCategory)
                        <div>
                            <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">{{ $categoryName ?: 'Other' }}</h4>
                            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-3">
                                @foreach ($subjectsInCategory as $subject)
                                    @php
                                        $pivot = $myPivots->get($subject->id)?->pivot;
                                    @endphp
                                    <div class="border border-gray-200 dark:border-gray-700 rounded-md p-3" x-data="{ checked: {{ in_array($subject->id, $mySubjectIds) ? 'true' : 'false' }} }">
                                        <label class="flex items-center gap-2">
                                            <input type="checkbox" x-model="checked" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                            <span class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $subject->name }}</span>
                                        </label>
                                        <input type="hidden" name="subjects[{{ $subject->id }}][id]" value="{{ $subject->id }}" :disabled="!checked">
                                        <div class="mt-2 space-y-2" x-show="checked">
                                            <select name="subjects[{{ $subject->id }}][level]" :disabled="!checked" class="block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-xs">
                                                @foreach (['beginner', 'intermediate', 'advanced', 'expert'] as $level)
                                                    <option value="{{ $level }}" @selected(($pivot->level ?? 'intermediate') === $level)>{{ ucfirst($level) }}</option>
                                                @endforeach
                                            </select>
                                            <input type="number" step="0.01" min="1" max="1000" name="subjects[{{ $subject->id }}][hourly_rate]" :disabled="!checked" value="{{ $pivot->hourly_rate ?? '' }}" placeholder="Rate override (₹/hr, optional)" class="block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-xs">
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500 dark:text-gray-400">No subjects are available yet.</p>
                    @endforelse

                    <x-input-error :messages="$errors->get('subjects')" />

                    <div class="flex justify-end">
                        <x-primary-button type="submit">Save subjects</x-primary-button>
                    </div>
                </form>
            </x-card>

            <!-- Certificates -->
            <x-card title="Certificates" subtitle="Upload certifications to boost trust with students. Each is reviewed before it's marked verified.">
                <form action="{{ route('tutor.profile.certificates.store') }}" method="POST" enctype="multipart/form-data" class="grid sm:grid-cols-3 gap-3 items-end mb-6">
                    @csrf
                    <div>
                        <x-input-label for="title" value="Title" />
                        <x-text-input id="title" name="title" class="mt-1 block w-full" placeholder="e.g. TEFL Certificate" required />
                    </div>
                    <div>
                        <x-input-label for="issuer" value="Issuer (optional)" />
                        <x-text-input id="issuer" name="issuer" class="mt-1 block w-full" placeholder="e.g. Cambridge" />
                    </div>
                    <div>
                        <x-input-label for="file" value="File" />
                        <input id="file" name="file" type="file" accept=".pdf,.jpg,.jpeg,.png" required class="mt-1 block w-full text-sm text-gray-600 dark:text-gray-300 file:mr-3 file:py-2 file:px-3 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 dark:file:bg-indigo-900/40 file:text-indigo-700 dark:file:text-indigo-300 hover:file:bg-indigo-100">
                    </div>
                    <div class="sm:col-span-3">
                        <x-input-error :messages="$errors->get('title')" class="mb-1" />
                        <x-input-error :messages="$errors->get('issuer')" class="mb-1" />
                        <x-input-error :messages="$errors->get('file')" class="mb-1" />
                        <x-primary-button type="submit">Upload certificate</x-primary-button>
                    </div>
                </form>

                @if ($certificates->isEmpty())
                    <x-empty-state title="No certificates uploaded yet" description="Add certificates to strengthen your profile." />
                @else
                    <div class="divide-y divide-gray-100 dark:divide-gray-700">
                        @foreach ($certificates as $certificate)
                            <div class="flex items-center justify-between gap-3 py-3 first:pt-0 last:pb-0">
                                <div class="min-w-0">
                                    <p class="font-medium text-gray-900 dark:text-gray-100 truncate">{{ $certificate->title }}</p>
                                    <p class="text-sm text-gray-500 dark:text-gray-400 truncate">{{ $certificate->issuer }}</p>
                                </div>
                                <div class="flex items-center gap-3 shrink-0">
                                    <x-status-badge :status="$certificate->status" />
                                    <a href="{{ route('files.certificate', $certificate) }}" class="text-sm text-indigo-600 dark:text-indigo-400 hover:underline">View</a>
                                    <form action="{{ route('tutor.profile.certificates.destroy', $certificate) }}" method="POST" onsubmit="return confirm('Delete this certificate?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-sm text-gray-400 hover:text-red-600 dark:hover:text-red-400">Delete</button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </x-card>

        </div>
    </div>
</x-app-layout>
