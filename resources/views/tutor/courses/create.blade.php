<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Create Course
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <x-card subtitle="New courses are reviewed by our team before they appear publicly.">
                <form action="{{ route('tutor.courses.store') }}" method="POST" enctype="multipart/form-data" class="space-y-5" x-data="{ isGroup: {{ old('is_group') ? 'true' : 'false' }} }">
                    @csrf

                    <div>
                        <x-input-label for="subject_id" value="Subject" />
                        <select id="subject_id" name="subject_id" required class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                            <option value="">Select a subject</option>
                            @foreach ($categories as $category)
                                <optgroup label="{{ $category->name }}">
                                    @foreach ($category->subjects as $subject)
                                        <option value="{{ $subject->id }}" @selected(old('subject_id') == $subject->id)>{{ $subject->name }}</option>
                                    @endforeach
                                </optgroup>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('subject_id')" class="mt-1" />
                    </div>

                    <div>
                        <x-input-label for="title" value="Title" />
                        <x-text-input id="title" name="title" class="mt-1 block w-full" value="{{ old('title') }}" required />
                        <x-input-error :messages="$errors->get('title')" class="mt-1" />
                    </div>

                    <div>
                        <x-input-label for="description" value="Description" />
                        <textarea id="description" name="description" rows="4" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">{{ old('description') }}</textarea>
                        <x-input-error :messages="$errors->get('description')" class="mt-1" />
                    </div>

                    <div class="grid sm:grid-cols-2 gap-4">
                        <div>
                            <x-input-label for="level" value="Level" />
                            <select id="level" name="level" required class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                @foreach (\App\Models\Course::LEVELS as $value => $label)
                                    <option value="{{ $value }}" @selected(old('level') === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                            <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Choose "Crash Course" for a fast-paced, exam-focused track.</p>
                            <x-input-error :messages="$errors->get('level')" class="mt-1" />
                        </div>
                        <div>
                            <x-input-label for="grade" value="Grade / class" />
                            <select id="grade" name="grade" required class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                @foreach (\App\Models\Course::GRADES as $value => $label)
                                    <option value="{{ $value }}" @selected(old('grade', 'all_grades') === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                            <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Helps students on the same board/class find this course.</p>
                            <x-input-error :messages="$errors->get('grade')" class="mt-1" />
                        </div>
                    </div>

                    <div>
                        <x-input-label for="duration_minutes" value="Session duration" />
                        <select id="duration_minutes" name="duration_minutes" required class="mt-1 block w-full sm:w-64 rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                            @foreach ([30, 60, 90, 120] as $minutes)
                                <option value="{{ $minutes }}" @selected(old('duration_minutes', 60) == $minutes)>{{ $minutes }} minutes</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('duration_minutes')" class="mt-1" />
                    </div>

                    <div class="grid sm:grid-cols-2 gap-4">
                        <div>
                            <x-input-label for="price" value="Price (₹)" />
                            <x-text-input id="price" name="price" type="number" step="0.01" min="0" max="10000" class="mt-1 block w-full" value="{{ old('price') }}" required />
                            <x-input-error :messages="$errors->get('price')" class="mt-1" />
                        </div>
                        <div>
                            <x-input-label for="total_sessions" value="Total sessions" />
                            <x-text-input id="total_sessions" name="total_sessions" type="number" min="1" max="100" class="mt-1 block w-full" value="{{ old('total_sessions') }}" required />
                            <x-input-error :messages="$errors->get('total_sessions')" class="mt-1" />
                        </div>
                    </div>

                    <div>
                        <label class="inline-flex items-center gap-2 text-sm text-gray-600 dark:text-gray-300">
                            <input type="checkbox" name="is_group" value="1" x-model="isGroup" @checked(old('is_group')) class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                            This is a group course
                        </label>
                        <div class="mt-2" x-show="isGroup">
                            <x-input-label for="max_students" value="Max students" />
                            <x-text-input id="max_students" name="max_students" type="number" min="1" max="100" class="mt-1 block w-full sm:w-64" value="{{ old('max_students', 1) }}" />
                            <x-input-error :messages="$errors->get('max_students')" class="mt-1" />
                        </div>
                    </div>

                    <div>
                        <x-input-label for="cover_image" value="Cover image (optional)" />
                        <input id="cover_image" name="cover_image" type="file" accept="image/*" class="mt-1 block w-full text-sm text-gray-600 dark:text-gray-300 file:mr-3 file:py-2 file:px-3 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 dark:file:bg-indigo-900/40 file:text-indigo-700 dark:file:text-indigo-300 hover:file:bg-indigo-100">
                        <x-input-error :messages="$errors->get('cover_image')" class="mt-1" />
                    </div>

                    <div class="flex justify-end gap-3">
                        <a href="{{ route('tutor.courses.index') }}"><x-secondary-button type="button">Cancel</x-secondary-button></a>
                        <x-primary-button type="submit">Submit for review</x-primary-button>
                    </div>
                </form>
            </x-card>
        </div>
    </div>
</x-app-layout>
