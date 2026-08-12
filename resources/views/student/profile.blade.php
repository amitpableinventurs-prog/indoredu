<x-app-layout :title="'Learner Profile'">
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Learner Profile
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <form method="POST" action="{{ route('student.profile.update') }}" class="space-y-6">
                @csrf
                @method('PUT')

                <x-card title="About you" subtitle="Help tutors understand how to best support your learning">
                    <div class="space-y-4">
                        <div>
                            <x-input-label for="grade_level" value="Grade / level" />
                            <x-text-input id="grade_level" name="grade_level" type="text" class="mt-1 block w-full"
                                          :value="old('grade_level', $profile->grade_level)" placeholder="e.g. Grade 9, University Freshman" />
                            <x-input-error class="mt-2" :messages="$errors->get('grade_level')" />
                        </div>

                        <div>
                            <x-input-label for="bio" value="About me" />
                            <textarea id="bio" name="bio" rows="4"
                                      class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                                      placeholder="A little about yourself, your interests, and how you learn best">{{ old('bio', $profile->bio) }}</textarea>
                            <x-input-error class="mt-2" :messages="$errors->get('bio')" />
                        </div>

                        <div>
                            <x-input-label for="learning_goals" value="Learning goals" />
                            <textarea id="learning_goals" name="learning_goals" rows="4"
                                      class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                                      placeholder="What do you want to achieve? Exam prep, homework help, fluency...">{{ old('learning_goals', $profile->learning_goals) }}</textarea>
                            <x-input-error class="mt-2" :messages="$errors->get('learning_goals')" />
                        </div>
                    </div>
                </x-card>

                <x-card title="Guardian info" subtitle="Optional, for younger students">
                    <div class="grid sm:grid-cols-2 gap-4">
                        <div class="sm:col-span-2">
                            <x-input-label for="guardian_name" value="Guardian name" />
                            <x-text-input id="guardian_name" name="guardian_name" type="text" class="mt-1 block w-full"
                                          :value="old('guardian_name', $profile->guardian_name)" />
                            <x-input-error class="mt-2" :messages="$errors->get('guardian_name')" />
                        </div>

                        <div>
                            <x-input-label for="guardian_email" value="Guardian email" />
                            <x-text-input id="guardian_email" name="guardian_email" type="email" class="mt-1 block w-full"
                                          :value="old('guardian_email', $profile->guardian_email)" />
                            <x-input-error class="mt-2" :messages="$errors->get('guardian_email')" />
                        </div>

                        <div>
                            <x-input-label for="guardian_phone" value="Guardian phone" />
                            <x-text-input id="guardian_phone" name="guardian_phone" type="tel" class="mt-1 block w-full"
                                          :value="old('guardian_phone', $profile->guardian_phone)" />
                            <x-input-error class="mt-2" :messages="$errors->get('guardian_phone')" />
                        </div>
                    </div>
                </x-card>

                <x-card title="Subjects I want to learn" subtitle="Select all that apply — this helps us recommend tutors">
                    @if ($subjects->isEmpty())
                        <p class="text-sm text-gray-500 dark:text-gray-400">No subjects are available yet.</p>
                    @else
                        <div class="space-y-5">
                            @foreach ($subjects->groupBy('category.name') as $categoryName => $categorySubjects)
                                <div>
                                    <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">{{ $categoryName }}</h4>
                                    <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-2">
                                        @foreach ($categorySubjects as $subject)
                                            <label class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-300">
                                                <input type="checkbox" name="subjects[]" value="{{ $subject->id }}"
                                                       @checked(in_array($subject->id, $mySubjectIds))
                                                       class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                                {{ $subject->name }}
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                    <x-input-error class="mt-2" :messages="$errors->get('subjects')" />
                </x-card>

                <div class="flex justify-end">
                    <x-primary-button type="submit">Save profile</x-primary-button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
