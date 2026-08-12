<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            My Students
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <x-card>
                @if ($students->isEmpty())
                    <x-empty-state title="No students yet" description="Once a student books a session with you, they'll appear here." />
                @else
                    <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach ($students as $student)
                            <a href="{{ route('tutor.students.show', $student) }}" class="block border border-gray-200 dark:border-gray-700 rounded-md p-4 hover:border-indigo-300 dark:hover:border-indigo-700 hover:shadow-sm transition">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full bg-indigo-100 dark:bg-indigo-900/40 text-indigo-700 dark:text-indigo-300 flex items-center justify-center font-semibold text-sm shrink-0 overflow-hidden">
                                        @if ($student->avatar)
                                            <img src="{{ asset('storage/'.$student->avatar) }}" alt="{{ $student->name }}" class="w-full h-full object-cover">
                                        @else
                                            {{ $student->initials() }}
                                        @endif
                                    </div>
                                    <div class="min-w-0">
                                        <p class="font-medium text-gray-900 dark:text-gray-100 truncate">{{ $student->name }}</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 truncate">{{ $student->studentProfile?->grade_level ?? 'Grade level not set' }}</p>
                                    </div>
                                </div>
                                <p class="text-sm text-gray-500 dark:text-gray-400 mt-3">{{ $student->sessions_with_me }} {{ \Illuminate\Support\Str::plural('session', $student->sessions_with_me) }} together</p>
                            </a>
                        @endforeach
                    </div>
                @endif
            </x-card>
        </div>
    </div>
</x-app-layout>
