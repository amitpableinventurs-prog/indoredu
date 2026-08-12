<x-app-layout :title="'Activity Log'">
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Admin Activity Log
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if ($logs->isEmpty())
                <x-empty-state title="No activity recorded yet" description="Admin actions will be logged here for auditing." />
            @else
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead>
                                <tr class="text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    <th class="px-4 py-3">When</th>
                                    <th class="px-4 py-3">Admin</th>
                                    <th class="px-4 py-3">Action</th>
                                    <th class="px-4 py-3">Subject</th>
                                    <th class="px-4 py-3">Details</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                @foreach ($logs as $log)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 align-top">
                                        <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400 whitespace-nowrap" title="{{ $log->created_at->format('D, M j, Y g:ia') }}">
                                            {{ $log->created_at->diffForHumans() }}
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-800 dark:text-gray-200 whitespace-nowrap">{{ $log->admin->name ?? 'Unknown admin' }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300 capitalize whitespace-nowrap">{{ str_replace(['.', '_'], ' ', $log->action) }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400 whitespace-nowrap">
                                            {{ $log->subject_type ? class_basename($log->subject_type).' #'.$log->subject_id : '—' }}
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">
                                            @if (empty($log->details))
                                                —
                                            @else
                                                <ul class="space-y-0.5">
                                                    @foreach ($log->details as $key => $value)
                                                        @if (! is_null($value) && $value !== '')
                                                            <li><span class="text-gray-400 dark:text-gray-500">{{ str_replace('_', ' ', $key) }}:</span> {{ is_array($value) ? json_encode($value) : $value }}</li>
                                                        @endif
                                                    @endforeach
                                                </ul>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div>{{ $logs->links() }}</div>
            @endif
        </div>
    </div>
</x-app-layout>
