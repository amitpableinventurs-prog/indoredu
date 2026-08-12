<x-app-layout :title="'Tutor Applications'">
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Tutor Applications
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @php $tabs = ['pending' => 'Pending', 'approved' => 'Approved', 'rejected' => 'Rejected', 'all' => 'All']; @endphp
            <div class="flex flex-wrap gap-2">
                @foreach ($tabs as $key => $label)
                    <a href="{{ route('admin.tutor-applications.index', ['status' => $key]) }}"
                       class="px-3 py-1.5 rounded-full text-sm font-medium {{ $status === $key ? 'bg-indigo-600 text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600' }}">
                        {{ $label }}
                    </a>
                @endforeach
            </div>

            @if ($applications->isEmpty())
                <x-empty-state title="No applications found" description="There are no tutor applications matching this filter." />
            @else
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead>
                                <tr class="text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    <th class="px-4 py-3">Tutor</th>
                                    <th class="px-4 py-3">Headline</th>
                                    <th class="px-4 py-3">Applied</th>
                                    <th class="px-4 py-3">Certificates</th>
                                    <th class="px-4 py-3">Status</th>
                                    <th class="px-4 py-3"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                @foreach ($applications as $application)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30">
                                        <td class="px-4 py-3">
                                            <p class="font-medium text-gray-900 dark:text-gray-100 whitespace-nowrap">{{ $application->user->name ?? 'Unknown' }}</p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $application->user->email ?? '' }}</p>
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-300 max-w-xs truncate">{{ $application->headline ?: '—' }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400 whitespace-nowrap">{{ $application->created_at->format('D, M j, Y') }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-300">{{ $application->certificates->count() }}</td>
                                        <td class="px-4 py-3"><x-status-badge :status="$application->status" /></td>
                                        <td class="px-4 py-3 text-right">
                                            <a href="{{ route('admin.tutor-applications.show', $application) }}" class="text-sm font-medium text-indigo-600 dark:text-indigo-400 hover:underline">Review</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div>{{ $applications->links() }}</div>
            @endif
        </div>
    </div>
</x-app-layout>
