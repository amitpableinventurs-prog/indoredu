<x-app-layout :title="'Subjects'">
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Subjects &amp; Categories
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div class="grid lg:grid-cols-2 gap-6 items-start">
                <!-- Categories -->
                <x-card title="Categories" :subtitle="$categories->count().' categories'">
                    @if ($categories->isEmpty())
                        <x-empty-state title="No categories yet" description="Add your first subject category below." />
                    @else
                        <div class="divide-y divide-gray-100 dark:divide-gray-700 mb-4 max-h-96 overflow-y-auto">
                            @foreach ($categories as $category)
                                <div class="flex items-center justify-between py-2.5 gap-2">
                                    <div class="min-w-0">
                                        <span class="text-sm text-gray-800 dark:text-gray-200">{{ $category->name }}</span>
                                        <div class="flex flex-wrap gap-1 mt-1">
                                            @if ($category->education_level)
                                                <span class="text-[10px] uppercase tracking-wide px-1.5 py-0.5 rounded bg-indigo-50 dark:bg-indigo-900/40 text-indigo-600 dark:text-indigo-300">{{ str_replace('_', ' ', $category->education_level) }}</span>
                                            @endif
                                            @if ($category->board)
                                                <span class="text-[10px] px-1.5 py-0.5 rounded bg-amber-50 dark:bg-amber-900/40 text-amber-600 dark:text-amber-300">{{ $category->board }}</span>
                                            @endif
                                            @if ($category->university)
                                                <span class="text-[10px] px-1.5 py-0.5 rounded bg-emerald-50 dark:bg-emerald-900/40 text-emerald-600 dark:text-emerald-300">{{ $category->university }}</span>
                                            @endif
                                        </div>
                                    </div>
                                    <span class="text-xs text-gray-400 dark:text-gray-500 whitespace-nowrap">{{ $category->subjects_count }} subjects</span>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    <form action="{{ route('admin.subject-categories.store') }}" method="POST" class="space-y-3 pt-2 border-t border-gray-100 dark:border-gray-700">
                        @csrf
                        <div>
                            <x-input-label for="category_name" value="New category" />
                            <x-text-input id="category_name" name="name" class="mt-1 block w-full" placeholder="e.g. Class 10 (CBSE) or B.E. Computer Science (RGPV)" required />
                            <x-input-error class="mt-2" :messages="$errors->get('name')" />
                        </div>
                        <div class="grid grid-cols-3 gap-2">
                            <div>
                                <x-input-label for="education_level" value="Level" />
                                <select id="education_level" name="education_level" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                    <option value="">—</option>
                                    <option value="school">School</option>
                                    <option value="undergraduate">Undergraduate</option>
                                    <option value="postgraduate">Postgraduate</option>
                                </select>
                            </div>
                            <div>
                                <x-input-label for="board" value="Board" />
                                <x-text-input id="board" name="board" class="mt-1 block w-full" placeholder="CBSE / MP Board" />
                            </div>
                            <div>
                                <x-input-label for="university" value="University" />
                                <x-text-input id="university" name="university" class="mt-1 block w-full" placeholder="RGPV / AKTU" />
                            </div>
                        </div>
                        <x-primary-button type="submit">Add</x-primary-button>
                    </form>
                </x-card>

                <!-- Add subject -->
                <x-card title="Add subject">
                    @if ($categories->isEmpty())
                        <p class="text-sm text-gray-500 dark:text-gray-400">Add a category first before creating subjects.</p>
                    @else
                        <form action="{{ route('admin.subjects.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                            @csrf
                            <div>
                                <x-input-label for="subject_category_id" value="Category" />
                                <select id="subject_category_id" name="subject_category_id" required class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                                <x-input-error class="mt-2" :messages="$errors->get('subject_category_id')" />
                            </div>
                            <div>
                                <x-input-label for="subject_name" value="Subject name" />
                                <x-text-input id="subject_name" name="name" class="mt-1 block w-full" placeholder="e.g. Spanish" required />
                                <x-input-error class="mt-2" :messages="$errors->get('name')" />
                            </div>
                            <div>
                                <x-input-label for="syllabus" value="Syllabus summary (optional)" />
                                <textarea id="syllabus" name="syllabus" rows="3" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm" placeholder="Unit 1: ...; Unit 2: ..."></textarea>
                                <x-input-error class="mt-2" :messages="$errors->get('syllabus')" />
                            </div>
                            <div>
                                <x-input-label for="syllabus_pdf" value="Syllabus PDF (optional)" />
                                <input id="syllabus_pdf" name="syllabus_pdf" type="file" accept="application/pdf" class="mt-1 block w-full text-sm text-gray-600 dark:text-gray-300 file:mr-3 file:py-2 file:px-3 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 dark:file:bg-indigo-900/40 file:text-indigo-700 dark:file:text-indigo-300 hover:file:bg-indigo-100">
                                <x-input-error class="mt-2" :messages="$errors->get('syllabus_pdf')" />
                            </div>
                            <x-primary-button type="submit">Add subject</x-primary-button>
                        </form>
                    @endif
                </x-card>
            </div>

            <!-- Subjects -->
            <x-card title="Subjects" :subtitle="$subjects->count().' subjects'">
                @if ($subjects->isEmpty())
                    <x-empty-state title="No subjects yet" description="Add a subject to get started." />
                @else
                    <div class="overflow-x-auto -mx-4 sm:-mx-6">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead>
                                <tr class="text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    <th class="px-4 sm:px-6 py-3">Name</th>
                                    <th class="px-4 sm:px-6 py-3">Category</th>
                                    <th class="px-4 sm:px-6 py-3">Syllabus</th>
                                    <th class="px-4 sm:px-6 py-3">Tutors</th>
                                    <th class="px-4 sm:px-6 py-3">Status</th>
                                    <th class="px-4 sm:px-6 py-3"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                @foreach ($subjects as $subject)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30">
                                        <td class="px-4 sm:px-6 py-3 text-sm font-medium text-gray-900 dark:text-gray-100 whitespace-nowrap">{{ $subject->name }}</td>
                                        <td class="px-4 sm:px-6 py-3 text-sm text-gray-600 dark:text-gray-300 whitespace-nowrap">{{ $subject->category->name ?? '—' }}</td>
                                        <td class="px-4 sm:px-6 py-3 text-sm text-gray-600 dark:text-gray-300 max-w-xs space-y-1">
                                            @if ($subject->syllabus)
                                                <details>
                                                    <summary class="cursor-pointer text-indigo-600 dark:text-indigo-400">Text outline</summary>
                                                    <p class="mt-1 whitespace-pre-line text-xs text-gray-500 dark:text-gray-400">{{ $subject->syllabus }}</p>
                                                </details>
                                            @endif

                                            @if ($subject->syllabus_pdf)
                                                <div class="flex items-center gap-2 whitespace-nowrap">
                                                    <a href="{{ asset('storage/'.$subject->syllabus_pdf) }}" target="_blank" rel="noopener" class="text-indigo-600 dark:text-indigo-400 hover:underline">View PDF</a>
                                                    <form action="{{ route('admin.subjects.syllabus.destroy', $subject) }}" method="POST" onsubmit="return confirm('Remove syllabus PDF?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="text-red-600 dark:text-red-400 hover:underline">Remove</button>
                                                    </form>
                                                </div>
                                            @else
                                                <form action="{{ route('admin.subjects.syllabus.upload', $subject) }}" method="POST" enctype="multipart/form-data" class="flex items-center gap-1">
                                                    @csrf
                                                    <input type="file" name="syllabus_pdf" accept="application/pdf" required class="text-xs w-28">
                                                    <button type="submit" class="text-indigo-600 dark:text-indigo-400 hover:underline text-xs whitespace-nowrap">Upload PDF</button>
                                                </form>
                                            @endif

                                            @if (! $subject->syllabus && ! $subject->syllabus_pdf)
                                                <span class="text-xs">—</span>
                                            @endif
                                        </td>
                                        <td class="px-4 sm:px-6 py-3 text-sm text-gray-600 dark:text-gray-300">{{ $subject->tutor_profiles_count }}</td>
                                        <td class="px-4 sm:px-6 py-3"><x-status-badge :status="$subject->is_active ? 'active' : 'draft'" /></td>
                                        <td class="px-4 sm:px-6 py-3 text-right whitespace-nowrap">
                                            <form action="{{ route('admin.subjects.toggle', $subject) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" class="text-sm font-medium text-indigo-600 dark:text-indigo-400 hover:underline mr-3">
                                                    {{ $subject->is_active ? 'Deactivate' : 'Activate' }}
                                                </button>
                                            </form>
                                            <form action="{{ route('admin.subjects.destroy', $subject) }}" method="POST" class="inline"
                                                  onsubmit="return confirm('Delete this subject? This cannot be undone.');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-sm font-medium text-red-600 dark:text-red-400 hover:underline">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </x-card>
        </div>
    </div>
</x-app-layout>
