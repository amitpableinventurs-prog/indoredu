<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Schedule
        </h2>
    </x-slot>

    @php
        $dayNames = [0 => 'Sunday', 1 => 'Monday', 2 => 'Tuesday', 3 => 'Wednesday', 4 => 'Thursday', 5 => 'Friday', 6 => 'Saturday'];
    @endphp

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <x-card title="Weekly availability" subtitle="Set the recurring windows when students can book you.">
                <div class="space-y-4">
                    @foreach ($dayNames as $dayNum => $dayName)
                        <div class="flex flex-col sm:flex-row sm:items-center gap-2 py-3 border-b border-gray-100 dark:border-gray-700 last:border-0">
                            <p class="w-28 shrink-0 font-medium text-sm text-gray-700 dark:text-gray-300">{{ $dayName }}</p>
                            <div class="flex flex-wrap gap-2">
                                @forelse ($availabilities[$dayNum] ?? [] as $slot)
                                    <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs bg-indigo-50 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-300">
                                        {{ substr($slot->start_time, 0, 5) }} &ndash; {{ substr($slot->end_time, 0, 5) }}
                                        <form action="{{ route('tutor.availability.destroy', $slot) }}" method="POST" onsubmit="return confirm('Remove this availability slot?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-indigo-400 hover:text-red-600 dark:hover:text-red-400" aria-label="Remove">&times;</button>
                                        </form>
                                    </span>
                                @empty
                                    <span class="text-xs text-gray-400 dark:text-gray-500">No availability set</span>
                                @endforelse
                            </div>
                        </div>
                    @endforeach
                </div>

                <form action="{{ route('tutor.availability.store') }}" method="POST" class="mt-6 grid grid-cols-2 sm:grid-cols-4 gap-3 items-end">
                    @csrf
                    <div>
                        <x-input-label for="day_of_week" value="Day" />
                        <select id="day_of_week" name="day_of_week" required class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                            @foreach ($dayNames as $dayNum => $dayName)
                                <option value="{{ $dayNum }}">{{ $dayName }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <x-input-label for="start_time" value="Start time" />
                        <x-text-input id="start_time" name="start_time" type="time" class="mt-1 block w-full" required />
                    </div>
                    <div>
                        <x-input-label for="end_time" value="End time" />
                        <x-text-input id="end_time" name="end_time" type="time" class="mt-1 block w-full" required />
                    </div>
                    <div>
                        <x-primary-button type="submit" class="w-full justify-center">Add</x-primary-button>
                    </div>
                    <div class="col-span-2 sm:col-span-4">
                        <x-input-error :messages="$errors->get('day_of_week')" />
                        <x-input-error :messages="$errors->get('start_time')" />
                        <x-input-error :messages="$errors->get('end_time')" />
                    </div>
                </form>
            </x-card>

            <x-card title="Time off / vacation" subtitle="Block out specific dates when you won't be available, even during your usual hours.">
                @if ($timeOffs->isEmpty())
                    <x-empty-state title="No upcoming time off" description="Add a date below if you'll be unavailable." />
                @else
                    <div class="divide-y divide-gray-100 dark:divide-gray-700 mb-6">
                        @foreach ($timeOffs as $timeOff)
                            <div class="flex items-center justify-between gap-3 py-3 first:pt-0">
                                <div class="min-w-0">
                                    <p class="font-medium text-gray-900 dark:text-gray-100">{{ $timeOff->date->format('D, M j, Y') }}</p>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">
                                        {{ $timeOff->start_time ? substr($timeOff->start_time, 0, 5).' – '.substr($timeOff->end_time, 0, 5) : 'All day' }}
                                        @if ($timeOff->reason)
                                            &middot; {{ $timeOff->reason }}
                                        @endif
                                    </p>
                                </div>
                                <form action="{{ route('tutor.time-off.destroy', $timeOff) }}" method="POST" onsubmit="return confirm('Remove this time off?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-sm text-gray-400 hover:text-red-600 dark:hover:text-red-400">Remove</button>
                                </form>
                            </div>
                        @endforeach
                    </div>
                @endif

                <form action="{{ route('tutor.time-off.store') }}" method="POST" class="grid grid-cols-2 sm:grid-cols-4 gap-3 items-end">
                    @csrf
                    <div>
                        <x-input-label for="date" value="Date" />
                        <x-text-input id="date" name="date" type="date" min="{{ now()->toDateString() }}" class="mt-1 block w-full" required />
                    </div>
                    <div>
                        <x-input-label for="to_start_time" value="Start time (optional)" />
                        <x-text-input id="to_start_time" name="start_time" type="time" class="mt-1 block w-full" />
                    </div>
                    <div>
                        <x-input-label for="to_end_time" value="End time (optional)" />
                        <x-text-input id="to_end_time" name="end_time" type="time" class="mt-1 block w-full" />
                    </div>
                    <div>
                        <x-input-label for="reason" value="Reason (optional)" />
                        <x-text-input id="reason" name="reason" class="mt-1 block w-full" />
                    </div>
                    <div class="col-span-2 sm:col-span-4 flex items-center justify-between">
                        <p class="text-xs text-gray-400 dark:text-gray-500">Leave start/end time blank to block the entire day.</p>
                        <x-primary-button type="submit">Add time off</x-primary-button>
                    </div>
                    <div class="col-span-2 sm:col-span-4">
                        <x-input-error :messages="$errors->get('date')" />
                    </div>
                </form>
            </x-card>

        </div>
    </div>
</x-app-layout>
